import React, { useState  } from "react";
import SelectBoxNoOfDays from "../components/SelectBoxNoOfDays";
import SelectBoxNifty from "../components/SelectBoxNifty";
import Global from "../components/Global";
import { formatDate } from "../components/Global";
import {breakoutwithvolume} from '../utils/RSI';
import { useMetaTags } from "../utils/MetaTags";
const stockUrl = Global.currentHost + "/stockAction.php";

function BreakoutWithVolume() {
  const [nifty, setNifty] = useState("");
  const [days, setDays] = useState("");
  const [loading, setLoading] = useState(false);
  const [niftyInvalid, setNiftyInvalid] = useState(false);
  const [daysInvalid, setDaysInvalid] = useState(false);
  const [breakoutWithVolume, setbreakoutWithVolume] = useState([]);
  useMetaTags({
      title: 'Breakout with volume | Stock Screener',
      description: 'Explore live stock screening tools and NSE data for informed trading decisions.',
      keywords: 'NSE, stock screener, live data, trading, finance',
      ogTitle: 'Breakout with volume | Stock Screener',
      ogDescription: 'Real-time stock screening with NSE data.',
      ogImage: 'http://springtown.in/images/stock-screener.jpg',
    });
  const handleSubmit = async () => {
    setbreakoutWithVolume([]);
    let valid = true;
    if (!nifty) {
      setNiftyInvalid(true);
      valid = false;
    } else setNiftyInvalid(false);

    if (!days) {
      setDaysInvalid(true);
      valid = false;
    } else setDaysInvalid(false);

    if (!valid) return;
    setLoading(true);
    const payload = {
      action: "ohlcData",
      niftyStatus: nifty,
      duration: days,
      getDataFromDate: days,
      scannerType:'breakoutwithvolume'
    };

    try {
      const res = await fetch(stockUrl, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      data.forEach(stockArray => {
        // Extract symbol name (from first object)        
        const stockSymbol = stockArray[0].stk_symbol;
        // Extract closing prices as numbers, sorted by date
        const closingPrices = stockArray.map(item => parseFloat(item.stk_close_price));
        const highPrice = stockArray.map(item => parseFloat(item.stk_high_price));
        const lowPrice = stockArray.map(item => parseFloat(item.stk_low_price));
        const closePrice = stockArray.map(item => parseFloat(item.stk_close_price));
        const tradingDate = stockArray.map(item => item.stk_date);
        const volume = stockArray.map(item => item.stk_ttl_trd_qnty);
        //highPrice.reverse();
        //lowPrice.reverse();
        //closePrice.reverse();
        //tradingDate.reverse();
        
        // Call the double Top function
        const boWV= breakoutwithvolume(stockSymbol, closePrice, tradingDate,volume, days);
        if (boWV) {
            setbreakoutWithVolume(prev => [...prev, boWV]);
        }
      });      
    } catch (err) {
        console.error("Error submitting:", err);
    } finally {
        setLoading(false);
    }
  };
  //console.log(breakoutWithVolume);
  return (
    <>        
      <h2 className="mb-3">Breakout With Volume</h2>
      <p>This area is reserved for your market content, scanners, analysis, and more.</p>
      <div className="d-flex align-items-end">
        <SelectBoxNifty value={nifty} onChange={setNifty} isInvalid={niftyInvalid} />
        <SelectBoxNoOfDays value={days} onChange={setDays} isInvalid={daysInvalid} />
        <div>
          <button className="btn btn-primary" onClick={handleSubmit} disabled={loading}>
            {loading ? "Loading..." : "Submit"}
          </button>
        </div>
      </div>
    <table className="table table-bordered table-striped mt-3">
      <thead>
        <tr>
            <th>Symbol</th>
            <th>Date</th>            
            <th>Close Price</th>
            <th>Type</th>
        </tr>
      </thead>
      <tbody>
        {loading ? (
          <tr>
            <td className="text-center" colSpan={6}>
              <div className="spinner-border text-primary" role="status">
                <span className="visually-hidden">Loading...</span>
              </div>
            </td>
          </tr>
        ) : breakoutWithVolume.length > 0 ? (
          breakoutWithVolume.map((item, index) => (
            <tr key={index}>
              <td>{item.symbol}</td>
              <td>{formatDate(item.todayDate)} / {formatDate(item.secondLargestclosePriceDate)} </td>
              <td>{item.todayClose} / {item.secondLargestPrice}</td>
              <td>{item.type}</td>
            </tr>
          ))
        ) : (
          <tr>
            <td className="text-center" colSpan={6}>
              No data available. Please submit.
            </td>
          </tr>
        )}
      </tbody>
    </table>
    </>
  );
}

export default BreakoutWithVolume;
