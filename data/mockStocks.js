export function getMockStocks() {
  const symbols = ['NIFTY', 'BANKNIFTY', 'RELIANCE', 'INFY', 'HDFCBANK'];
  return symbols.map(symbol => {
    const price = (Math.random() * 2000 + 500).toFixed(2);
    const change = (Math.random() * 4 - 2).toFixed(2); // between -2% and +2%
    return { symbol, price, change };
  });
}