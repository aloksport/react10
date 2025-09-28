import React from "react";
import { useMetaTags } from "../utils/MetaTags";
function Contact() {
  useMetaTags({
    title: 'Contact Us | Stock Screener',
    description: 'Explore live stock screening tools and NSE data for informed trading decisions.',
    keywords: 'NSE, stock screener, live data, trading, finance',
    ogTitle: 'Contact Us | Stock Screener',
    ogDescription: 'Real-time stock screening with NSE data.',
    ogImage: 'http://springtown.in/images/stock-screener.jpg',
  });
  return (
    <div>
      <h2>Contact Us</h2>
      <p>Email: support@example.com</p>
      <p>Phone: +91-9876543210</p>
      <p>We’re happy to answer your questions and provide support.</p>
    </div>
  );
}

export default Contact;
