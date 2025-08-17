import React from 'react';
function SelectBoxNifty({ value, onChange,isInvalid  }) {
  return (
    <div className="me-3">
        <label htmlFor="niftySelect" className="form-label">Nifty</label>
        <select id="niftySelect" className={`form-select ${isInvalid ? "is-invalid" : ""}`} value={value} 
        onChange={(e) => onChange(e.target.value)} required>
          <option value="">Select </option>
           <option value="1" defaultValue>Nifty</option>
           <option value="2" >Nifty Next</option>
           <option value="3">Mid Cap</option>
           <option value="4">Small Cap</option>
        </select>
        {isInvalid && <div className="invalid-feedback">Please select a stock</div>}
    </div>
  );
}
export default SelectBoxNifty;

