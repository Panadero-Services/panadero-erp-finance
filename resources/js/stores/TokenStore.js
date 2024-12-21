import { defineStore } from 'pinia';
import { ethers } from 'ethers';
import Tokens from "../../js/constants/tokens.json";
import Providers from "../../js/constants/providers.json";

//import { useWalletStore } from "@/stores/WalletStore.js";
//const walletStore = useWalletStore()


//const SwappyTestnet="0xd2ca18bb5aF11dF7c349184eA10CF8Fc35aF9b12";
//const bscScanApiKey = import.meta.env.VITE_BSCSCAN_API_KEY;
//const bscKey = "SQWT7KQ9QR7DCPE7HQV17N1EQBF935QQKZ";
//import { useWalletStore } from "@/stores/WalletStore.js";
//const walletStore = useWalletStore();
/*
*/
export let useTokenStore = defineStore('token',{
    state: () => ({
        name: "" , 
        chainNr: 0,
        endPoint:"",
        type: "",
        address:"0x0",
        decimals: 18,
        balances:{},
        abi:[],
        balance: -1 // not used
    }),
  
    //Getters are exactly the equivalent of computed values for the state of a Store. They can be defined with the getters property in defineStore(). They receive the state as the first parameter to encourage the usage of arrow function:
    getters: {
        doubleCount: (state) => state.balance * 2,

        provider(state) {

//            let _endPoint = 'https://data-seed-prebsc-1-s1.binance.org:8545/';
//            if(this.chainNr==56)_endPoint = 'https://bsc-dataseed1.binance.org/';

            let _endPoint;
            
            if(this.chainNr==97) this.endPoint = Providers.BSC_Testnet_4.node;
            if(this.chainNr==56) this.endPoint = Providers.BSC_9.node;
            if(this.chainNr==5) this.endPoint = Providers.GOERLI_1.node;
            if(this.chainNr==137) this.endPoint = Providers.POLYGON_1.node;
            if(this.chainNr==80001) this.endPoint = Providers.POLYGON_Testnet_1.node;
            if(this.chainNr==62621) this.endPoint = Providers.MTV_1.node;

            // When using the JSON-RPC API, the network will be automatically detected
            // API Providers
            // const provider = new ethers.providers.AlchemyProvider('goerli', 'your-alchemy-api-key');
            // const provider = new ethers.providers.InfuraProvider('goerli', 'your-infura-api-key');
            // const provider = new ethers.providers.EtherscanProvider('goerli', 'your-etherscan-api-key');
            // Node Providers .. JsonRpcProvider... HardhatProvider.. Web3Provider
            // const provider = new ethers.providers.JsonRpcProvider('http://localhost:8545');

            // 1. Using Web3Provider(), When we want to do transactions via metamask.
            // const web3Modal = new Web3Modal.connect();
            // const provider = new ethers.providers.Web3Provider(connection);
            // now ethers.js will not use the default hardhat provider


            // 2. using JsonRpcProvider
            // this will not use MM
            return new ethers.JsonRpcProvider(this.endPoint);


// Setup: npm install ethers
//const ethers = require("ethers");
//const url = "https://eth-mainnet.alchemyapi.io/v2/your-api-key";
//const customHttpProvider = new ethers.providers.JsonRpcProvider(url);


        }
    },
    actions: {

        async initialize() {
          //  this.fill();
        },

        async reset(){
            this.name = "";
            this.assets = {};
            this.chainNr = 0;
            this.type = "";
            this.address = "0x0";
            this.decimals =  18;
            this.balances = {};
            this.abi = [];
            this.balance = -1; // not used
        },

        increment() {
            this.balance++;
        },
        decrement() {
            this.balance--;
        },
        set(_name) {
            this.name=_name;
            this.chainNr=Tokens[this.name].chain_id;
            this.type=Tokens[this.name].type;
            this.address=Tokens[this.name].address;
            this.decimals=Tokens[this.name].decimals;
            this.abi=Tokens[this.name].abi;
        },
        
        setFromDb(_e) {
            this.name = _e.title;
            this.chainNr = _e.web3chain.chain_nr
            this.type = _e.web3type.title;
            this.address = _e.address;
            this.decimals = 18;
            this.abi = _e.abi;
        },

        async initialize() {
            //this.balance();
        },
        
        async readContract(contractAddress, abi, functionName, params = []) {
            this.address = contractAddress;
            this.decimals = 18;
            const name = new ethers.Contract(this.address, abi, this.provider);
            return await name[functionName](...params);
        },

        async getBalanceFromDb(_e, _wallet) {
            console.log(_e.address,_wallet);
            let _balance = await this.readContract(_e.address, _e.abi, "balanceOf", [_wallet]);
            let _convert = Number(BigInt(_balance)/ 1000_000_000_000_000n);
            let _convertBalance = _convert/1000;
            console.log(_e.title , "balance",_wallet,': ', _convertBalance);
            if (this.balances[_e.title]===undefined)this.balances[_e.title]={};
                this.balances[_e.title][_wallet]= _convertBalance;
            return {name:_e.title, balance:_convertBalance};
        },

        async getBalanceFromJson(_token, _wallet) {
            let _balance = await this.readContract(Tokens[_token].address, Tokens[_token].abi, "balanceOf", [_wallet]);
            let _convert = Number(BigInt(_balance)/ 1000_000_000_000_000n);
            let _convertBalance = _convert/1000;
            console.log(_token , "balance",_wallet,': ', _convertBalance);
            if (this.balances[_token]===undefined)this.balances[_token]={};
                this.balances[_token][_wallet]= _convertBalance;
            return {name:_token, balance:_convertBalance};
        },
    },

});
