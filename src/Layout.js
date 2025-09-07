import { Outlet } from "react-router-dom";
import TopNavBar from "./components/TopNavBar";
import MobileTopBar from "./components/MobileTopBar";
import LeftSideBar from "./components/LeftSideBar";
import RightSideBar from "./components/RightSideBar";
import MobileBottomBar from "./components/MobileBottomBar";
import Footer from "./components/Footer";

const Layout = () => {
  return (
    <>
      {/* Top Nav Bar */}
      <TopNavBar />

      {/* Mobile Top Ad */}
      <MobileTopBar />

      {/* Main Content Layout */}
      <div className="container-fluid mt-3">
        <div className="row">
          {/* Left Ad (Desktop Only) */}
          <LeftSideBar />

          {/* Main Content Outlet */}
          <div className="col-12 col-md-8 mb-3">
            <div className="bg-white p-4 border rounded shadow-sm">
              <Outlet />
            </div>
          </div>

          {/* Right Ad (Desktop Only) */}
          <RightSideBar />
        </div>
      </div>

      {/* Mobile Bottom Ad */}
      <MobileBottomBar />

      {/* Footer */}
      <Footer />
    </>
  );
};

export default Layout;
