/* import logo from './logo.svg'; */
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap-icons/font/bootstrap-icons.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "./App.css";
import Layout from "./Layout";
import RSIDivergence from './pages/RSIDivergence';
import DoubleTop from './pages/DoubleTop';
import DoubleBottom from './pages/DoubleBottom';
import StockRSI from './pages/StockRSI';
import Scanners from './pages/Scanners';
import About from "./pages/AboutUs";
import Contact from "./pages/Contact";
import PrivacyPolicy from "./pages/PrivacyPolicy";
import Terms from "./pages/Terms";
import NSEData from "./pages/NSEData";
function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Layout />}>
          {/* Home (Stock Screener) */}
          <Route index element={<About />} />
          <Route path="scanners" element={<Scanners />} />
          <Route path="scanners/StockRSI" element={<StockRSI />} />
          <Route path="scanners/rsi-divergence-scanner" element={<RSIDivergence />} />
          <Route path="scanners/double-top" element={<DoubleTop />} />
          <Route path="scanners/double-bottom" element={<DoubleBottom />} />
          {/* Other Pages */}
          <Route path="about" element={<About />} />
          <Route path="contact" element={<Contact />} />
          <Route path="privacy-policy" element={<PrivacyPolicy />} />
          <Route path="terms" element={<Terms />} />
        </Route>
        <Route element={<Layout showLeftSidebar={false} showRightSidebar={true} />}>
          <Route path="scanners/nse-data" element={<NSEData />} />
        </Route>
      </Routes>
    </Router>
  );  
}

export default App;
