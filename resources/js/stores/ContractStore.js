import { defineStore } from 'pinia';
import { ethers } from 'ethers';
import Contracts from "../../js/constants/contracts.json";
import Providers from "../../js/constants/providers.json";

export const useContractStore = defineStore('contract',{
    state: () => ({
            name: "" , 
            chainNr: 97,
            endPoint:"",
            type: "",
            address:"0x0",
            decimals: 18,
            balances:{},
            abi:[],
            balance: -1,  // not used, 

            // new fields for contracts dashboard / stakepool
            stats:[] // these are the contractLines

    }),

    //Getters are exactly the equivalent of computed values for the state of a Store. They can be defined with the getters property in defineStore(). They receive the state as the first parameter to encourage the usage of arrow function:
    getters: {
        provider(state) {
            let _endPoint;
            if(this.chainNr==97) this.endPoint = Providers.BSC_Testnet_4.node;
            if(this.chainNr==56) this.endPoint = Providers.BSC_9.node;
            if(this.chainNr==5) this.endPoint = Providers.GOERLI_1.node;
            if(this.chainNr==137) this.endPoint = Providers.POLYGON_1.node;
            if(this.chainNr==80001) this.endPoint = Providers.POLYGON_Testnet_1.node;
            if(this.chainNr==62621) this.endPoint = Providers.MTV_1.node;
            return new ethers.JsonRpcProvider(this.endPoint);
        },
    },

    actions: {
        async initialize() {
            console.log('initialize');
        },

        async getStats(){
            console.log('getStats');
        },

        async reset(){
            this.name = "";
            this.type = "";
            this.description = "";
            this.balances = {};
            this.chainId = 0;
            this.address = "0x0";
            this.abi = [];
        },

        set(_name) {
            this.name=_name;
            this.chainId=Contracts[this.name].chain_id;
            this.type=Contracts[this.name].type;
            this.address=Contracts[this.name].address;
            this.balances = {};
            this.abi=Contracts[this.name].abi;
        },
        
        setFromDb(_e) {
            this.name = _e.title;
            this.chainNr = _e.web3chain.chain_nr
            this.type = _e.web3type.title;
            this.address = _e.address;
            this.decimals = 18;
            this.abi = _e.abi;
        },

        async readContract(contractAddress, abi, functionName, params = []) {
            this.address = contractAddress;
            this.decimals = 18;
            const name = new ethers.Contract(this.address, abi, this.provider);
            return await name[functionName](...params);
        },

        async getBalanceFromDb(_e, _wallet) {
            console.log(_e.address,_wallet);
            let _balance = await this.readContract(_e.address, _e.abi, "totalSupply", []);
            let _convert = Number(BigInt(_balance)/ 1000_000_000_000_000n);
            let _convertBalance = _convert/1000;
            console.log(_e.title , "balance",_wallet,': ', _convertBalance);
            if (this.balances[_e.title]===undefined)this.balances[_e.title]={};
                this.balances[_e.title][_wallet]= _convertBalance;
            return {name:_e.title, balance:_convertBalance};
        },

        async getTotalSupply(_e) {
            console.log(_e.address);
            let _supply = await this.readContract(_e.address, _e.abi, "duration", []);
           // let _convert = Number(BigInt(_supply)/ 1000_000_000_000_000n);
           // let _convertSupply = _convert/1000;
            console.log(_e.title , "supply",': ', _supply);
            return {name:_e.title, supply:_supply};
        },

        async getBalanceFromJson(_contract, _wallet) {
            let _balance = await this.readContract(Contracts[_contract].address, Contracts[_contract].abi, "totalSupply", [_wallet]);
            let _convert = Number(BigInt(_balance)/ 1000_000_000_000_000n);
            let _convertBalance = _convert/1000;
            console.log(_contract , "balance",_wallet,': ', _convertBalance);
            if (this.balances[_contract]===undefined)this.balances[_contract]={};
                this.balances[_contract][_wallet]= _convertBalance;
            return {name:_contract, balance:_convertBalance};
        },

/*        async runReadFunction(_e, _f, _params = []) {
            let result = await this.readContract(_e.address, _e.abi, _f, _params);
            console.log(_e.title , _f,': ', result);
            return result;
        },
*/

        async readContract(_address, _abi, _function, _chainNr , _params = []) {
            this.address = _address;
            this.decimals = 18;
            this.chainNr = _chainNr;
            const name = new ethers.Contract(this.address, _abi, this.provider);
            return await name[_function](..._params);
        },

        async runReadFunction(_line) {
            console.log(_line);

            let params = [];
            if (_line.parameters.length > 0) params = _line.parameters.split(",");
            console.log(params, params.length);

            let result = await this.readContract(_line.address, _line.contractAbi, _line.line, _line.chainNr,  params);
            console.log( _line.line,': ', result);
            return result;
        },

    },
});

