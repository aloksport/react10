function Global(){}
export default Global;
const directoryName = 'react10/data';
const protocol = `${window.location.protocol}`;
const hostname = `${window.location.hostname}`;
Global.currentHost = protocol+'//'+hostname + '/'+directoryName;
Global.allow_useraction_role = ['1', '2'];
export function formatDate(dateStr) {
  if (!dateStr) return "";
  const date = new Date(dateStr);
  return date.toLocaleDateString("en-GB", {
    day: "2-digit",
    month: "short",
    year: "numeric"
  }).replace(/ /g, "-"); 
}