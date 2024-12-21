import { defineStore } from 'pinia';

export const useWalletStore = defineStore('wallet',{
    state: () => ({
        account: "0x0",
        assets: {},
        lastAsset: {},
        count: 0
    }),

/* getter example
  getters: {
    finishedTodos(state) {
      //return state.todos.filter((todo) => todo.isFinished)
    },
*/
actions:{

        async initialize() {
            this.fill();
        },

        async reset(){
            this.account = '0x0';
            this.assets = {};
        },

        async fill(){
            const accounts = await ethereum.request({ method: 'eth_requestAccounts' });
            const account = accounts[0];
            this.account = account;
        },


        async setBalances(_balances){
            Object.entries(_balances).forEach(([key, value]) => {
                console.log(`${key} ${value}`); // "a 5", "b 7", "c 9"
                this._setBalance(key,value[this.account]);
            });
        },
        async _setBalance(_key,_value){
            if (this.assets[_key]===undefined)this.assets[_key]={};
               this.assets[_key]= _value;
        },
    },
});


