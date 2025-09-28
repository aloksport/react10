import React, { useState } from "react";
import SelectBoxNifty from "../components/SelectBoxNifty";
import Calender from "../components/Calender";
import Global from "../components/Global";
//import { formatDate } from "../components/Global";
import { useMetaTags } from "../utils/MetaTags";
const stockUrl = Global.currentHost + "/stockAction.php";

function NSEData() {
  //const [rows, setRows] = useState([]);
  const [nifty, setNifty] = useState("");
  const [days, setDays] = useState("");
  const [loading, setLoading] = useState(false);
  const [niftyInvalid, setNiftyInvalid] = useState(false);
  const [NSEData, setNSEData] = useState([]);
  const [selectedDate, setSelectedDate] = useState(null);
  const [dateError, setDateError] = useState("");
  useMetaTags({
       title: 'NSE Data | Stock Screener',
       description: 'Explore live stock screening tools and NSE data for informed trading decisions.',
       keywords: 'NSE, stock screener, live data, trading, finance',
       ogTitle: 'NSE Data | Stock Screener',
       ogDescription: 'Real-time stock screening with NSE data.',
       ogImage: 'http://springtown.in/images/stock-screener.jpg',
     });
  const handleSubmit = async () => {
    setDays(1);// Just use it so that it doesn't give error
    setNSEData([]);let  formatted='';
    let valid = true;
    if (!nifty) {
      setNiftyInvalid(true);
      valid = false;
    } else setNiftyInvalid(false);

    if (!selectedDate) {
      setDateError("Please select a date.");
      valid = false;
    } else {
      formatted = selectedDate.toLocaleDateString("en-CA"); 
      setDateError("");
    }
    if (!valid) return;
    setLoading(true);
    
    const payload = {
      action: "ohlcData",
      niftyStatus: nifty,
      duration: days,
      getDataFromDate: formatted, // send formatted date
      scannerType:'nsedata'
    };    
    try {
      const res = await fetch(stockUrl, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      //console.log(data);
      //setRows(data);

      //
      // Loop through each stock’s data
      data.forEach(stockArray => {
      stockArray.forEach(item => {
        setNSEData(prev => [...prev, item]);
      });
    });     
    } catch (err) {
        console.error("Error submitting:", err);
    } finally {
        setLoading(false);
    }
  };
  //console.log(NSEData);
  return (
    <>        
      <h2 className="mb-3">NSE Data</h2>
      <p>Here is NSE Data </p>
      <div className="d-flex align-items-end">
        <SelectBoxNifty value={nifty} onChange={setNifty} isInvalid={niftyInvalid} />        
        <Calender selectedDate={selectedDate} onDateChange={setSelectedDate} error={dateError} />
        <div>
          <button className="btn btn-primary ms-2" onClick={handleSubmit} disabled={loading}>
            {loading ? "Loading..." : "Submit"}
          </button>
        </div>
      </div>
    <div class="table-responsive mt-3">
    <table className="table table-bordered table-striped mt-3">
      <thead>
        <tr>
            <th>Symbol</th>
            <th>Delivery Per.</th>            
            <th>Open</th>
            <th>High</th>
            <th>Low</th>
            <th>Close</th>
            <th>Previous Close</th>
            <th>Volumn</th>
            <th>No Of Trades</th>            
            <th>Action</th>
        </tr>
      </thead>
      <tbody>
        {loading ? (
          <tr>
            <td className="text-center" colSpan={10}>
              <div className="spinner-border text-primary" role="status">
                <span className="visually-hidden">Loading...</span>
              </div>
            </td>
          </tr>
        ) : NSEData.length > 0 ? (
          NSEData.map((item, index) => (
            <tr key={index}>
              <td>{item.stk_symbol}</td>{/* <td>{formatDate(item.stk_date)} </td> */}
              <td style={{ backgroundColor: parseFloat(item.stk_deliv_per) > 70 ? 'lightgreen' : 'transparent' }}>
              {item.stk_deliv_per}</td>
              <td>{item.stk_open_price}</td>
              <td>{item.stk_high_price}</td>
              <td>{item.stk_low_price}</td>
              <td>{item.stk_close_price}</td>
              <td>{item.stk_prev_close} </td>
              <td>{item.stk_ttl_trd_qnty}</td>
              <td>{item.stk_no_of_trades}</td>              
              <td>Action</td>
            </tr>
          ))
        ) : (
          <tr>
            <td className="text-center" colSpan={10}>
              No data available. Please submit.
            </td>
          </tr>
        )}
      </tbody>
    </table>
    </div>
    </>
  );
}

export default NSEData;