/* import logo from './logo.svg'; */
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap-icons/font/bootstrap-icons.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "./App.css";
import Layout from "./Layout";
import RSIDivergence from './pages/RSIDivergence';
import StockRSI from './pages/StockRSI';
import Scanners from './pages/Scanners';
import About from "./pages/AboutUs";
import Contact from "./pages/Contact";
import PrivacyPolicy from "./pages/PrivacyPolicy";
import Terms from "./pages/Terms";
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
         
          {/* Other Pages */}
          <Route path="about" element={<About />} />
          <Route path="contact" element={<Contact />} />
          <Route path="privacy-policy" element={<PrivacyPolicy />} />
          <Route path="terms" element={<Terms />} />
        </Route>
      </Routes>
    </Router>
  );  
}

export default App;
