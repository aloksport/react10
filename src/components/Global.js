function Global(){}
export default Global;
const directoryName = 'react10/data';
const protocol = `${window.location.protocol}`;
const hostname = `${window.location.hostname}`;
Global.currentHost = protocol+'//'+hostname + '/'+directoryName;
Global.allow_useraction_role = ['1', '2'];
export function formatDate(date)  {
    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];    
    const day = String(date.getDate()).padStart(2, "0");
    const month = months[date.getMonth()];
    const year = date.getFullYear();    
    return `${day} ${month} ${year}`;
};