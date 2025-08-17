function SelectBoxNoOfDays({ value, onChange, isInvalid  }) {
  return (
    <div className="me-3">
        <label htmlFor="periods" className="form-label">Number of Days</label>
        <input
            name="days"
            list="days"
            type="text"
            className={`form-select ${isInvalid ? "is-invalid" : ""}`}
            id="periods"
            placeholder="Enter Periods"
            value={value} onChange={(e) => onChange(e.target.value)}   required
        />
        <datalist id="days">
            <option value="10" defaultValue/>
            <option value="20" />
            <option value="30" />
            <option value="40" />
            <option value="50" />
            <option value="60" />
        </datalist>
        {isInvalid && <div className="invalid-feedback">Please select number of days</div>}
        </div>
  );
}
export default SelectBoxNoOfDays;

