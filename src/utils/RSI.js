// utils/rsi.js

export default function calculateRSI(closingValues, period = 14) {
  let rsi = [];
  let avgGain = 0;
  let avgLoss = 0;

  closingValues.forEach((close, key) => {
    let change = 0;
    if (key > 0) {
      change = closingValues[key] - closingValues[key - 1];
    }

    const gain = change > 0 ? change : 0;
    const loss = change < 0 ? Math.abs(change) : 0;

    if (key === 0) {
      avgGain = 0;
      avgLoss = 0;
    } else {
      avgGain = (avgGain * (period - 1) + gain) / period;
      avgLoss = (avgLoss * (period - 1) + loss) / period;
    }

    let rs = 0;
    if (avgLoss !== 0) {
      rs = avgGain / avgLoss;
    }

    if (key >= period - 1) {
      rsi.push(Number((100 - 100 / (1 + rs)).toFixed(2)));
    } else {
      rsi.push(0); // keep 0 for the initial values
    }
  });

  return rsi;
}
export const  abcd = (days) => {
  return days;
};
export const addNumbers = (a, b) => {
  console.log("Sum:", a + b);
  return a + b;
};
export const analyzeRSI =(stockSymbol,rsi, arrayHighPrice, arrayLowPrice, arrayClosePrice, arrayDate, days) => {
  /*console.log( "RSI:", rsi);
  console.log( "openPrice:", arrayHighPrice);
  console.log( "highPrice:", arrayLowPrice);
  console.log( "lowPrice:", arrayClosePrice);
  console.log( "closePrice:", arrayClosePrice);
  console.log( "tradingDate:", arrayDate); 
  console.log( "days:", days); */
  let max = null;
  let max_value_date = null;
  let min_value = null;
  let min_value_date = null;

  let min_rsi = rsi[0];
  let max_rsi = rsi[0];

  let allMaxHighprice = [];
  let allMinLowPrice = [];

  for (let i = 0; i < days; i++) {
    if (max_rsi < rsi[i]) {
      max = arrayHighPrice[i];
      max_value_date = arrayDate[i];
      max_rsi = rsi[i];
      allMaxHighprice.push(arrayClosePrice[i]);
    }

    if (min_rsi > rsi[i]) {
      min_value = arrayLowPrice[i];
      min_value_date = arrayDate[i];
      min_rsi = rsi[i];
      allMinLowPrice.push(arrayClosePrice[i]);
    }
  }

  const current_rsi = rsi[0];
  const current_low = arrayLowPrice[0];
  const current_high = arrayHighPrice[0];
  const current_date = arrayDate[0];

  // ✅ BULLISH DIVERGENCE
  if (current_rsi > min_rsi && current_low < min_value) {
    if (current_low <= Math.min(...allMinLowPrice)) {
      const date1 = new Date(current_date);
      const date2 = new Date(min_value_date);
      const daysDifference = (date1 - date2) / (1000 * 60 * 60 * 24);

      if (daysDifference > 4) {
        const diffInPrice = current_low - min_value;
        const percentDiffInPrice = (diffInPrice * 100) / min_value;

        if (current_rsi - min_rsi > 1 /* && percentDiffInPrice > 1 */) {
          return {
            symbol:stockSymbol,
            type: "Bull",
            min_rsi,
            min_value,
            min_value_date,
            current_rsi,
            current_low,
            current_date
          };
        }
      }
    }
  }

  // ✅ BEARISH DIVERGENCE
  if (current_rsi < max_rsi && current_high > max) {
    if (max >= Math.max(...allMaxHighprice)) {
      const date1 = new Date(current_date);
      const date2 = new Date(max_value_date);
      const daysDifference = (date1 - date2) / (1000 * 60 * 60 * 24);

      if (daysDifference > 4) {
        const diffInPrice = current_high - max;
        const percentDiffInPrice = (diffInPrice * 100) / max;

        if (max_rsi - current_rsi > 1 /* && percentDiffInPrice > 1 */) {
          return {
            symbol:stockSymbol,
            type: "Bear",
            max_rsi,
            max,
            max_value_date,
            current_rsi,
            current_high,
            current_date
          };
        }
      }
    }
  }

  return ""; // no signal
}

export const getStockBySymbol = (rows, symbol) => {
  const stock = rows.find(item => item.stk_symbol === symbol);
  if (!stock) return null;

  return {
    stk_symbol: stock.stk_symbol,
    data: stock.stk_date.map((date, idx) => ({
      date,
      open: stock.stk_open[idx],
      high: stock.array_high[idx],
      low: stock.stk_low[idx],
      close: stock.array_close[idx]
    }))
  };
};

        //console.log(symbol, "RSI:", rsi);
        //const greeting = addNumbers(25,10);
        //console.log("RSI Values:", greeting);
        //const greeting1 = abcd(50);
        //console.log("RSI greeting1:", greeting1);
        /*console.log(symbol, "openPrice:", openPrice.reverse());
        console.log(symbol, "highPrice:", highPrice.reverse());
        console.log(symbol, "lowPrice:", lowPrice.reverse());
        console.log(symbol, "closePrice:", closePrice.reverse());
        console.log(symbol, "tradingDate:", tradingDate.reverse());*/ 
        
        //const rsiValues2 = abcd(50);
        //console.log("RSI Values:", rsiValues2);