import React from 'react';
function SelectBoxNifty() {
  return (
    <div className="me-3">
        <label htmlFor="niftySelect" className="form-label">Nifty</label>
        <select id="niftySelect" className="form-select">
           <option value="10" defaultValue>Nifty</option>
           <option value="20" >Nifty Next</option>
           <option value="30">30</option>
           <option value="40">40</option>
           <option value="50">50</option>
           <option value="60">60</option>
        </select>
    </div>
  );
}
export default SelectBoxNifty;

