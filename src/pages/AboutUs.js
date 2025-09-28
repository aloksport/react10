import React from "react";
import { useMetaTags } from "../utils/MetaTags";

function About() {
  useMetaTags({
    title: 'About Us | Stock Screener',
    description: 'Explore live stock screening tools and NSE data for informed trading decisions.',
    keywords: 'NSE, stock screener, live data, trading, finance',
    ogTitle: 'NSE Data | Live Stock Screener',
    ogDescription: 'Real-time stock screening with NSE data.',
    ogImage: 'http://springtown.in/images/stock-screener.jpg',
  });
  return (
    <div>
      <h2>About Us</h2>
      <p>
        Welcome to our platform! We provide real-time stock screeners, analysis,
        and insights to help traders and investors make better decisions.
      </p>
    </div>
  );
}

export default About;
