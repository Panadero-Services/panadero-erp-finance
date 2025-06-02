import Binance from 'binance-api-full-node'

const client = Binance()

const BINANCE_LIEUWE_API_KEY = 'uF7Jxb475bLUtVjTcUyYfD41K3JF7hT3YwX3kCLKMMi5mMcNLcE61DulxhvDQAwe';
const BINANCE_LIEUWE_API_SECRET = 'izk8bxcSIgt72tiWNdcnszF8p8dCBBoWOHobrajPzbrovBf3QwdCwjDlb5xnnXUX';

// Authenticated client, can make signed calls
const client2 = Binance({
  apiKey: BINANCE_LIEUWE_API_KEY,
  apiSecret: BINANCE_LIEUWE_API_SECRET,
  getTime: xxx,
})

client.time().then(time => console.log(time))