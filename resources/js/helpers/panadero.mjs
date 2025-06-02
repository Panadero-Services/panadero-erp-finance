// Authenticated client, can make signed calls
//const BINANCE_LIEUWE_API_KEY = '';
//const BINANCE_LIEUWE_API_SECRET = '';
//const client2 = Binance({
//  apiKey: BINANCE_LIEUWE_API_KEY,
//  apiSecret: BINANCE_LIEUWE_API_SECRET,
//  getTime: 12344,
//})

//import Binance from 'binance-api-full-node'
//const binance = Binance();


//import Mexc from 'node-mexc-api'
//const mexc = new Mexc();

export function useMexcTicker() {
      return new Promise(resolve => {
         const check = async () => {
          try {
            let _ticker = await mexc.spot.symbolPriceTicker();
            resolve(_ticker);
        } catch (err) {
          console.log(err);
        }
      }
      check(); 
    });
  }

export function useBinanceTicker() {
      return new Promise(resolve => {
         const check = async () => {
          try {
            //let _ticker = await binance.prices();
            let _ticker=[];
            resolve(_ticker);
        } catch (err) {
          console.log(err);
        }
      }
      check(); 
    });
  }




// mouse.js
import { ref, onMounted, onUnmounted } from 'vue'

// by convention, composable function names start with "use"
export function useMouse() {
  // state encapsulated and managed by the composable
  const x = ref(0)
  const y = ref(0)

  // a composable can update its managed state over time.
  function update(event) {
    x.value = event.pageX
    y.value = event.pageY
  }

  // a composable can also hook into its owner component's
  // lifecycle to setup and teardown side effects.
  onMounted(() => window.addEventListener('mousemove', update))
  onUnmounted(() => window.removeEventListener('mousemove', update))

  // expose managed state as return value
  return { x, y }
}


export function useQuote() {
   return 1322;

}