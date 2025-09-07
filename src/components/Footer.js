import { Link } from "react-router-dom";
export default function Footer() {
  return (
    <footer className="bg-dark text-light text-center py-3 mt-auto">
      <p className="mb-0">
        © {new Date().getFullYear()} Stock Scanner | Built for traders
      </p>
      <small>
        <Link to="/privacy-policy" className="text-light text-decoration-none">Privacy Policy</Link> | <Link to="/terms" className="text-light text-decoration-none ms-2">Terms</Link>
      </small>
    </footer>
  );
}
