/* import React, { useState } from "react";
import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";
import "bootstrap/dist/css/bootstrap.min.css";

export default function Calender() {
  const [selectedDate, setSelectedDate] = useState(null);

  return (
    <div className="mt-3">
      <label htmlFor="datapicker" className="form-label">Select Date</label>
      <div className="input-group">
        <DatePicker id='datapicker'
          selected={selectedDate}
          onChange={(date) => setSelectedDate(date)}
          dateFormat="dd-MMM-yyyy"
          className="form-control"
          placeholderText="Day-Month-Year"
        />
        <span className="input-group-text">
          <i className="bi bi-calendar-date"></i>
        </span>
      </div>
    </div>
  );
}
 */

import React from "react";
import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";
import "bootstrap/dist/css/bootstrap.min.css";

export default function Calender({ selectedDate, onDateChange, error }) {
  return (
    <div className="mt-3">
      <label htmlFor="datepicker" className="form-label">Select Date</label>
      <div className="input-group">
        <DatePicker
          id="datepicker"
          selected={selectedDate}
          onChange={onDateChange}
          dateFormat="dd-MMM-yyyy"
          className={`form-control ${error ? "is-invalid" : ""}`}
          placeholderText="Day-Month-Year"
          maxDate={new Date()}  // prevent selecting future dates
          autoComplete="off"
        />
        <span className="input-group-text d-none d-md-block">
          <i className="bi bi-calendar-date"></i>
        </span>
      </div>
      {error && <div className="text-danger mt-1">{error}</div>}
    </div>
  );
}

