import React, { useState } from "react";
import SelectBoxNoOfDays from "../components/SelectBoxNoOfDays";
import SelectBoxNifty from "../components/SelectBoxNifty";
import Global from "../components/Global";
import calculateRSI from '../utils/RSI';
const stockUrl = Global.currentHost + "/stockAction.php";

function RSIDivergence() {
  const [rows, setRows] = useState([]);
  const [nifty, setNifty] = useState("");
  const [days, setDays] = useState("");
  const [loading, setLoading] = useState(false);
  const [niftyInvalid, setNiftyInvalid] = useState(false);
  const [daysInvalid, setDaysInvalid] = useState(false);

  const handleSubmit = async () => {
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
      console.log(data);
      setRows(data);

      //
      const closingPrices = data.flatMap(item => item.array_close);
      const arrayHighPrice = data.flatMap(item => item.array_close);
      const arrayLowPrice = data.flatMap(item => item.array_close);
      const arrayClosePrice = data.flatMap(item => item.array_close);
      const arrayDate = data.flatMap(item => item.stk_date);
      //console.log("Close Prices:", closingPrices);      
      const rsiValues = calculateRSI(closingPrices, 14);
      //console.log("RSI Values:", rsiValues);
      //
    } catch (err) {
        console.error("Error submitting:", err);
    } finally {
        setLoading(false);
    }
  };
  //console.log(rows[1].stk_symbol);
  return (
    <>        
      <h2 className="mb-3">Live Stock Screener</h2>
      <p>This area is reserved for your market content, scanners, analysis, and more.</p>
      <div className="d-flex align-items-end">
        <SelectBoxNifty value={nifty} onChange={setNifty} isInvalid={niftyInvalid} />
        <SelectBoxNoOfDays value={days} onChange={setDays} isInvalid={daysInvalid} />
        <div>
          <button className="btn btn-primary" onClick={handleSubmit}>
            Submit
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
        ) : rows.length > 0 ? (
            rows.map((row, rowIndex) =>
                row.stk_date.map((date, i) => (
                <tr key={`${rowIndex}-${i}`}>
                    <td>{row.stk_symbol}</td>
                    <td>{date}</td>
                    <td>{row.array_close[i]}</td>
                </tr>
                ))
            )
        ) : (
        <tr>
            <td className="text-center" colSpan={3}>No data available. Please submit.</td>
        </tr>
        )}
      </tbody>
    </table>
    </>
  );
}

export default RSIDivergence;
