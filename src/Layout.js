import { Outlet } from "react-router-dom";
import TopNavBar from "./components/TopNavBar";
import MobileTopBar from "./components/MobileTopBar";
import LeftSideBar from "./components/LeftSideBar";
import RightSideBar from "./components/RightSideBar";
import MobileBottomBar from "./components/MobileBottomBar";
import Footer from "./components/Footer";

const Layout = ({ showLeftSidebar = true, showRightSidebar = true }) => {
  const mainColClass = showLeftSidebar && showRightSidebar 
    ? "col-12 col-md-8 mb-3"
    : (showLeftSidebar || showRightSidebar)
      ? "col-12 col-md-10 mb-3"
      : "col-12 mb-3";
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
          {showLeftSidebar && <LeftSideBar />}

          {/* Main Content Outlet */}
          <div className={mainColClass}>
            <div className="bg-white p-4 border rounded shadow-sm">
              <Outlet />
            </div>
          </div>

          {/* Right Ad (Desktop Only) */}
          {showRightSidebar && <RightSideBar />}
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
