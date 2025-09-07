import { NavLink } from "react-router-dom";

function TopNavBar() {
  return (
    <>  
      <nav className="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div className="container-fluid">
          <NavLink to="/" className="navbar-brand fw-bold">StockScanner</NavLink>          
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
                <NavLink to="#" className="nav-link dropdown-toggle" data-bs-toggle="dropdown">Market Tools</NavLink>                
                <ul className="dropdown-menu">
                  <li><NavLink to="scanners/rsi-divergence-scanner" className="dropdown-item">RSI Divergence</NavLink></li>                  
                  <li><NavLink to="scanners/StockRSI" className="dropdown-item">Top Losers</NavLink></li>                  
                  <li><NavLink to="rsi-divergence-scanner" className="dropdown-item">Volume Buzzers</NavLink></li>                  
                </ul>
              </li>
              <li className="nav-item">
                <NavLink to="/scanners" className={({ isActive }) => "nav-link" + (isActive ? " active fw-bold" : "")}>
                  Scanners
                </NavLink>
              </li>
              <li className="nav-item">
                <NavLink to="/about" className={({ isActive }) => "nav-link" + (isActive ? " active fw-bold" : "")}>
                  Alerts
                </NavLink>
              </li>
              <li className="nav-item">
                <NavLink to="/about" className={({ isActive }) => "nav-link" + (isActive ? " active fw-bold" : "")}>
                  About Us
                </NavLink>
              </li>
              <li className="nav-item">
                <NavLink to="/contact" className={({ isActive }) => "nav-link" + (isActive ? " active fw-bold" : "")}>
                  Contact
                </NavLink>
              </li>
              <li className="nav-item">
                <NavLink to="/privacy-policy" className={({ isActive }) => "nav-link" + (isActive ? " active fw-bold" : "")}>
                  Privacy Policy
                </NavLink>
              </li>
              <li className="nav-item">
                <NavLink to="/terms" className={({ isActive }) => "nav-link" + (isActive ? " active fw-bold" : "")}>
                  Terms and Conditions
                </NavLink>
              </li>              
            </ul>

            {/* User Dropdown */}
            <ul className="navbar-nav ms-auto">
              <li className="nav-item dropdown">
                <NavLink to="/users" className="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                  <i className="bi bi-person-circle" /> User
                </NavLink>
                <ul className="dropdown-menu dropdown-menu-end">
                  <li><NavLink to="/users" className="dropdown-item">Profile</NavLink></li>
                  <li><hr className="dropdown-divider" /></li>
                  <li><NavLink to="/users" className="dropdown-item text-danger">Logout</NavLink></li>
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
