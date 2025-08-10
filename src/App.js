import logo from './logo.svg';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js'; 
import './App.css';
import TopNavBar from './components/TopNavBar';
import MobileTopBar from './components/MobileTopBar';
import LeftSideBar from './components/LeftSideBar';
import RightSideBar from './components/RightSideBar';
import MobileBottomBar from './components/MobileBottomBar';
import Footer from './components/Footer';
import SelectBoxNoOfDays from './components/SelectBoxNoOfDays';
import SelectBoxNifty from './components/SelectBoxNifty';


function App() {
  return (
    <>
    {/* Top Nav Bar */}
    <TopNavBar/>    
    {/* Mobile Top Ad */}
    <MobileTopBar/>
    {/* Main Content Layout */}
    <div className="container-fluid mt-3">
      <div className="row">
        {/* Left Ad (Desktop Only) */}
        <LeftSideBar/>
        {/* Main Content */}
        <div className="col-12 col-md-8 mb-3">
          <div className="bg-white p-4 border rounded shadow-sm">
            <h2 className="mb-3">Live Stock Screener</h2>
            <p>
              This area is reserved for your market content, scanners, analysis,
              and more.
            </p>
            <div className="d-flex align-items-end">
              <SelectBoxNoOfDays/>
              <SelectBoxNifty/>
              <div>
                <button className="btn btn-primary">Submit</button>
              </div>
            </div>
            <table className="table table-bordered table-striped mt-3">
        <thead>
          <tr>
            <th>Stock</th>
            <th>Price</th>
            <th>RSI</th>
            <th>Trend</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>ABC Ltd</td>
            <td>₹1500</td>
            <td>28</td>
            <td>Oversold</td>
          </tr>
          <tr>
            <td>XYZ Corp</td>
            <td>₹2500</td>
            <td>32</td>
            <td>Neutral</td>
          </tr>
        </tbody>
      </table>
            
          </div>
        </div>
        {/* Right Ad (Desktop Only) */}
        <RightSideBar/>
      </div>
    </div>
    {/* Mobile Bottom Ad */}
    <MobileBottomBar/>
    <Footer/>
  </>
  );
}

export default App;
