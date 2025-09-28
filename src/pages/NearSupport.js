import React, { useState } from "react";
import SelectBoxNoOfDays from "../components/SelectBoxNoOfDays";
import SelectBoxNifty from "../components/SelectBoxNifty";
import Global from "../components/Global";
import { formatDate } from "../components/Global";
import {doubleBottom} from '../utils/RSI';
import { useMetaTags } from "../utils/MetaTags";
const stockUrl = Global.currentHost + "/stockAction.php";

function NearSupport() {
  const [nifty, setNifty] = useState("");
  const [days, setDays] = useState("");
  const [loading, setLoading] = useState(false);
  const [niftyInvalid, setNiftyInvalid] = useState(false);
  const [daysInvalid, setDaysInvalid] = useState(false);
  const [dblBottomData, setDblBottomData] = useState([]);
  useMetaTags({
       title: 'Stock Near Support | Stock Screener',
       description: 'Explore live stock screening tools and NSE data for informed trading decisions.',
       keywords: 'NSE, stock screener, live data, trading, finance',
       ogTitle: 'Stock Near Support | Stock Screener',
       ogDescription: 'Real-time stock screening with NSE data.',
       ogImage: 'http://springtown.in/images/stock-screener.jpg',
     });
  const handleSubmit = async () => {
    setDblBottomData([]);
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
      scannerType:'doublebottom'
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
        
        // Call the double Top function
        const dblBottom= doubleBottom(stockSymbol, lowPrice, closePrice, tradingDate, days);
        if (dblBottom) {
            setDblBottomData(prev => [...prev, dblBottom]);
        }
      });      
    } catch (err) {
        console.error("Error submitting:", err);
    } finally {
        setLoading(false);
    }
  };
  //console.log(dblBottomData);
  return (
    <>        
      <h2 className="mb-3">Stock Near Support</h2>
      <p>This scanner scan stock which are at near support for number of days provided by you.</p>
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
        ) : dblBottomData.length > 0 ? (
          dblBottomData.map((item, index) => (
            <tr key={index}>
              <td>{item.symbol}</td>
              <td>{formatDate(item.todayDate)} / {formatDate(item.lowestDate)} </td>
              <td>{item.todayLow} / {item.lowestPrice}</td>
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

export default NearSupport;
