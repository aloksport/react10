import React, { useState } from "react";
import SelectBoxNoOfDays from "../components/SelectBoxNoOfDays";
import SelectBoxNifty from "../components/SelectBoxNifty";
import Global from "../components/Global";
import calculateRSI from '../utils/RSI';
import {analyzeRSI} from '../utils/RSI';
//import {abcd,addNumbers} from '../utils/RSI';
const stockUrl = Global.currentHost + "/stockAction.php";

function RSIDivergence() {
  const [rows, setRows] = useState([]);
  const [nifty, setNifty] = useState("");
  const [days, setDays] = useState("");
  const [loading, setLoading] = useState(false);
  const [niftyInvalid, setNiftyInvalid] = useState(false);
  const [daysInvalid, setDaysInvalid] = useState(false);
  const [rsiDivergData, setrsiDivergData] = useState([]);

  const handleSubmit = async () => {
    setrsiDivergData([]);
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
      getDataFromDate: 299,
    };

    try {
      const res = await fetch(stockUrl, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      //console.log(data);
      setRows(data);

      //
      // Loop through each stock’s data
      data.forEach(stockArray => {
        // Extract symbol name (from first object)
        const stockSymbol = stockArray[0].stk_symbol;

        // Extract closing prices as numbers, sorted by date
        const closingPrices = stockArray
          //.sort((a, b) => new Date(a.stk_date) - new Date(b.stk_date)) // ensure chronological order
          .map(item => parseFloat(item.stk_close_price));

        // Call RSI function        
        const openPrice = stockArray.map(item => parseFloat(item.stk_open_price));
        const highPrice = stockArray.map(item => parseFloat(item.stk_high_price));
        const lowPrice = stockArray.map(item => parseFloat(item.stk_low_price));
        const closePrice = stockArray.map(item => parseFloat(item.stk_close_price));
        const tradingDate = stockArray.map(item => item.stk_date);
        const rsi = calculateRSI(closingPrices, 14);
        rsi.reverse();
        openPrice.reverse();
        highPrice.reverse();
        lowPrice.reverse();
        closePrice.reverse();
        tradingDate.reverse();
        const rsidiverg= analyzeRSI(stockSymbol,rsi, highPrice, lowPrice, closePrice, tradingDate, days);
        //setrsiDivergData(prev => [...prev, rsidiverg]);
        if (rsidiverg && Object.keys(rsidiverg).length > 0) {
          setrsiDivergData(prev => [...prev, rsidiverg]);
        }
        console.log(stockSymbol, "RSI1:", rsidiverg);
      });      
    } catch (err) {
        console.error("Error submitting:", err);
    } finally {
        setLoading(false);
    }
  };
  //console.log(rsiDivergData);
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
            <th>Stock Symbol</th>
            <th>Date</th>
            <th>Close</th>
        </tr>
      </thead>
      <tbody>
        {loading ? (
          <tr>
            <td className="text-center" colSpan={3}>
              <div className="spinner-border text-primary" role="status">
                <span className="visually-hidden">Loading...</span>
              </div>
            </td>
          </tr>
        ) : rsiDivergData.length > 0 ? (
          rsiDivergData.map((item, index) => (
            <tr key={index}>
              <td>{item.symbol}</td>
              <td>{item.min_value_date}</td>
              <td>{item.type}</td>
            </tr>
          ))
        ) : (
          <tr>
            <td className="text-center" colSpan={3}>
              No data available. Please submit.
            </td>
          </tr>
        )}
      </tbody>
    </table>
    </>
  );
}

export default RSIDivergence;
