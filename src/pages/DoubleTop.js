import React, { useState,useEffect  } from "react";
import SelectBoxNoOfDays from "../components/SelectBoxNoOfDays";
import SelectBoxNifty from "../components/SelectBoxNifty";
import Global from "../components/Global";
import { formatDate } from "../components/Global";
import {doubleTop} from '../utils/RSI';
const stockUrl = Global.currentHost + "/stockAction.php";

function DoubleTop() {
  const [nifty, setNifty] = useState("");
  const [days, setDays] = useState("");
  const [loading, setLoading] = useState(false);
  const [niftyInvalid, setNiftyInvalid] = useState(false);
  const [daysInvalid, setDaysInvalid] = useState(false);
  const [dblTopData, setDblTopData] = useState([]);
  useEffect(() => {
    document.title = "Double Top | Live Stock Screener";
  }, []);
  const handleSubmit = async () => {
    setDblTopData([]);
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
      scannerType:'doubletop'
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
        //highPrice.reverse();
        //lowPrice.reverse();
        //closePrice.reverse();
        //tradingDate.reverse();
        
        // Call the double Top function
        const dblTop= doubleTop(stockSymbol, highPrice, closePrice, tradingDate, days);
        if (dblTop) {
            setDblTopData(prev => [...prev, dblTop]);
        }
      });      
    } catch (err) {
        console.error("Error submitting:", err);
    } finally {
        setLoading(false);
    }
  };
  //console.log(dblTopData);
  return (
    <>        
      <h2 className="mb-3">Live Stock Screener</h2>
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
        ) : dblTopData.length > 0 ? (
          dblTopData.map((item, index) => (
            <tr key={index}>
              <td>{item.symbol}</td>
              <td>{formatDate(item.todayDate)} / {formatDate(item.highestDate)} </td>
              <td>{item.todayHigh} / {item.highestPrice}</td>
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

export default DoubleTop;
