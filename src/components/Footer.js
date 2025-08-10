import React from 'react';

export default function Footer() {
  return (
    <footer className="bg-dark text-light text-center py-3 mt-auto">
      <p className="mb-0">
        © {new Date().getFullYear()} Stock Scanner | Built for traders
      </p>
      <small>
        <a href="#" className="text-light text-decoration-none">Privacy Policy</a> | 
        <a href="#" className="text-light text-decoration-none ms-2">Terms</a>
      </small>
    </footer>
  );
}
