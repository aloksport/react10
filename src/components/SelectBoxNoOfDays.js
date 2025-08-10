function SelectBoxNoOfDays() {
  return (
    <div className="me-3">
        <label htmlFor="periods" className="form-label">Number of Days</label>
        <input
            name="days"
            list="days"
            type="text"
            className="form-control"
            id="periods"
            placeholder="Enter Periods"
            defaultValue="10"
        />
        <datalist id="days">
            <option value="10" />
            <option value="20" />
            <option value="30" />
            <option value="40" />
            <option value="50" />
            <option value="60" />
        </datalist>
        </div>
  );
}
export default SelectBoxNoOfDays;

