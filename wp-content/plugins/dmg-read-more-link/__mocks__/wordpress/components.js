const React = require("react");

module.exports = {
  PanelBody: ({ children }) => React.createElement("div", null, children),
  TextControl: ({ label, value, onChange }) =>
    React.createElement(
      "label",
      null,
      label,
      React.createElement("input", {
        value,
        onChange: (e) => onChange(e.target.value),
      }),
    ),
  Button: ({ children, onClick, disabled }) =>
    React.createElement("button", { onClick, disabled }, children),
  Spinner: () => React.createElement("div", null, "Loading..."),
  Notice: ({ children }) =>
    React.createElement("div", { role: "alert" }, children),
};
