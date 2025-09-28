import React from "react";
import { useMetaTags } from "../utils/MetaTags";
function Terms() {
  useMetaTags({
      title: 'Terms | Stock Screener',
      description: 'Explore live stock screening tools and NSE data for informed trading decisions.',
      keywords: 'NSE, stock screener, live data, trading, finance',
      ogTitle: 'RSI Divergence | Stock Screener',
      ogDescription: 'Real-time stock screening with NSE data.',
      ogImage: 'http://springtown.in/images/stock-screener.jpg',
    });
  return (
    <div>
      <h2>Terms & Conditions</h2>
      <p>
        By using our platform, you agree to follow our rules and guidelines.
        Please read these terms carefully before continuing.
      </p>
    </div>
  );
}

export default Terms;
