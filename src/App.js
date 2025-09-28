/* import logo from './logo.svg'; */
import { BrowserRouter as Router, Routes, Route ,useLocation} from "react-router-dom";
import React, { useEffect } from "react";
import { useAnalytics } from "./google-analytics";
import ReactGA from 'react-ga4';
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap-icons/font/bootstrap-icons.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "./App.css";
import Layout from "./Layout";
import RSIDivergence from './pages/RSIDivergence';
import NearResistance from './pages/NearResistance';
import NearSupport from './pages/NearSupport';
import StockRSI from './pages/StockRSI';
import Scanners from './pages/Scanners';
import About from "./pages/AboutUs";
import Contact from "./pages/Contact";
import PrivacyPolicy from "./pages/PrivacyPolicy";
import Terms from "./pages/Terms";
import NSEData from "./pages/NSEData";
import VolumnBreakout from "./pages/VolumnBreakout";
import BreakoutWithVolume from "./pages/BreakoutWithVolume";
// Component to track page views on route change
  const TrackPageViews = () => {
    const location = useLocation();
    useEffect(() => {
      ReactGA.send({ hitType: 'pageview', page: location.pathname + location.search });
    }, [location]);

    return null;
  };
function App() {
  // Google Analytics //
  useAnalytics();
  // Google Analytics //
  return (
    <Router>
      <TrackPageViews />
      <Routes>
        <Route path="/" element={<Layout />}>
          {/* Home (Stock Screener) */}
          <Route index element={<About />} />
          <Route path="scanners" element={<Scanners />} />
          <Route path="scanners/StockRSI" element={<StockRSI />} />
          <Route path="scanners/rsi-divergence-scanner" element={<RSIDivergence />} />
          <Route path="scanners/stock-near-resistance" element={<NearResistance />} />
          <Route path="scanners/stock-near-support" element={<NearSupport />} />
          <Route path="scanners/volume-breakout" element={<VolumnBreakout />} />
          <Route path="scanners/breakout-with-volume" element={<BreakoutWithVolume />} />
          {/* Other Pages */}
          <Route path="about" element={<About />} />
          <Route path="contact" element={<Contact />} />
          <Route path="privacy-policy" element={<PrivacyPolicy />} />
          <Route path="terms" element={<Terms />} />
        </Route>
        <Route element={<Layout showLeftSidebar={false} showRightSidebar={true} />}>
          <Route path="nse-data" element={<NSEData />} />
        </Route>
      </Routes>
    </Router>
  );  
}

export default App;
