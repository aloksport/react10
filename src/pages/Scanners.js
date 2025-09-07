import React from "react";
import { Link } from "react-router-dom";
const Scanners = () => {
  return (
    <div>
      <h2>About Us</h2>
      <p>
        Welcome to our platform! We provide real-time stock screeners, analysis,
        and insights to help traders and investors make better decisions.
      </p>
      <ul>
        <li><Link to="rsi-divergence-scanner" className="dropdown-item">RSI Divergence</Link></li>                  
        <li><Link to="StockRSI" className="dropdown-item">Top Losers</Link></li>                  
        <li><Link to="rsi-divergence-scanner" className="dropdown-item">Volume Buzzers</Link></li>                  
      </ul>
    </div>
  );
}

export default Scanners;
