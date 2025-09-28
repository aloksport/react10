import React from "react";
import { Link } from "react-router-dom";
import { useMetaTags } from "../utils/MetaTags";
const Scanners = () => {
  useMetaTags({
    title: 'Scanner | Stock Screener',
    description: 'Explore live stock screening tools and NSE data for informed trading decisions.',
    keywords: 'NSE, stock screener, live data, trading, finance',
    ogTitle: 'RSI Divergence | Stock Screener',
    ogDescription: 'Real-time stock screening with NSE data.',
    ogImage: 'http://springtown.in/images/stock-screener.jpg',
  });
  return (
    <div>
      <h2>Scanner List</h2>
      <p>
        Welcome to our platform! We provide stock screeners, analysis,
        and insights to help traders and investors make better decisions.
      </p>
      <ul>
        <li><Link to="rsi-divergence-scanner" className="dropdown-item">RSI Divergence</Link></li>                  
        <li><Link to="stock-near-resistance" className="dropdown-item">Stock Near Resistance</Link></li>                  
        <li><Link to="stock-near-support" className="dropdown-item">Stock Near Support</Link></li>
        {/*<li><Link to="nse-data" className="dropdown-item">NSE Data</Link></li>                   
        <li><Link to="StockRSI" className="dropdown-item">Top Losers</Link></li>                  
         <li><Link to="volume-breakout" className="dropdown-item">Volume Buzzers</Link></li>                   */}
        <li><Link to="breakout-with-volume" className="dropdown-item">Breakout With Volume</Link></li>                  
      </ul>
    </div>
  );
}

export default Scanners;
