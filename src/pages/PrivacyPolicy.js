import React from "react";
import { useMetaTags } from "../utils/MetaTags";
function PrivacyPolicy() {
  useMetaTags({
         title: 'Privacy Policy | Stock Screener',
         description: 'Explore live stock screening tools and NSE data for informed trading decisions.',
         keywords: 'NSE, stock screener, live data, trading, finance',
         ogTitle: 'Privacy Policy | Stock Screener',
         ogDescription: 'Real-time stock screening with NSE data.',
         ogImage: 'http://springtown.in/images/stock-screener.jpg',
       });
  return (
    <div>
      <h2>Privacy Policy</h2>
      <p>
        We value your privacy. Your data is secure and will never be shared with
        third parties without your consent.
      </p>
    </div>
  );
}

export default PrivacyPolicy;
