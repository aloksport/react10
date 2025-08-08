import React from 'react';
function TopNavBar() {
  return (
    <>  
      <nav className="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div className="container-fluid">
          <a className="navbar-brand fw-bold" href="#">
            StockScanner
          </a>
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
                <a
                  className="nav-link dropdown-toggle"
                  href="#"
                  data-bs-toggle="dropdown"
                >
                  Market Tools
                </a>
                <ul className="dropdown-menu">
                  <li>
                    <a className="dropdown-item" href="#">
                      Top Gainers
                    </a>
                  </li>
                  <li>
                    <a className="dropdown-item" href="#">
                      Top Losers
                    </a>
                  </li>
                  <li>
                    <a className="dropdown-item" href="#">
                      Volume Buzzers
                    </a>
                  </li>
                </ul>
              </li>
              <li className="nav-item">
                <a className="nav-link" href="#">
                  Screener
                </a>
              </li>
              <li className="nav-item">
                <a className="nav-link" href="#">
                  Alerts
                </a>
              </li>
            </ul>
            {/* User Dropdown */}
            <ul className="navbar-nav ms-auto">
              <li className="nav-item dropdown">
                <a
                  className="nav-link dropdown-toggle"
                  href="#"
                  data-bs-toggle="dropdown"
                >
                  <i className="bi bi-person-circle" /> User
                </a>
                <ul className="dropdown-menu dropdown-menu-end">
                  <li>
                    <a className="dropdown-item" href="#">
                      Profile
                    </a>
                  </li>
                  <li>
                    <hr className="dropdown-divider" />
                  </li>
                  <li>
                    <a className="dropdown-item text-danger" href="#">
                      Logout
                    </a>
                  </li>
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


