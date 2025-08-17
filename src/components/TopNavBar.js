import { Link } from "react-router-dom";
function TopNavBar() {
  return (
    <>  
      <nav className="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div className="container-fluid">
          <Link to="/" className="navbar-brand fw-bold">StockScanner</Link>          
          <button
            className="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarMenu"
          >
            <span className="navbar-toggler-icon" />
          </button>
          <div className="collapse navbar-collapse" id="navbarMenu">
            <ul className="navbar-nav me-auto">
              <li className="nav-item dropdown">
                <Link to="#" className="nav-link dropdown-toggle" data-bs-toggle="dropdown">Market Tools</Link>                
                <ul className="dropdown-menu">
                  <li><Link to="rsi-divergence-scanner" className="dropdown-item">RSI Divergence</Link></li>                  
                  <li><Link to="StockRSI" className="dropdown-item">Top Losers</Link></li>                  
                  <li><Link to="rsi-divergence-scanner" className="dropdown-item">Volume Buzzers</Link></li>                  
                </ul>
              </li>
              <li className="nav-item"><Link to="/about" className="nav-link">Screener</Link></li>
              <li className="nav-item"><Link to="/about" className="nav-link">Alerts</Link></li>              
              <li className="nav-item"><Link to="/about" className="nav-link">About Us</Link></li>
              <li className="nav-item"><Link to="/contact" className="nav-link">Contact</Link></li>
              <li className="nav-item"><Link to="/privacy-policy" className="nav-link">Privacy Policy</Link></li>
              <li className="nav-item"><Link to="/terms" className="nav-link">Terms and Conditions</Link></li>              
            </ul>
            {/* User Dropdown */}
            <ul className="navbar-nav ms-auto">
              <li className="nav-item dropdown">
                <Link to="/users" className="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i className="bi bi-person-circle" /> User</Link>
                <ul className="dropdown-menu dropdown-menu-end">
                  <li><Link to="/users" className="dropdown-item">Profile</Link></li>
                  <li><hr className="dropdown-divider" /></li>
                  <li><Link to="/users" className="dropdown-item text-danger">Logout</Link></li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </>
  );
}
export default TopNavBar;


