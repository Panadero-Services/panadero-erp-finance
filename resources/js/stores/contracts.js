import { defineStore } from 'pinia';
import { ethers, formatEther } from 'ethers';
import Contracts from "../../js/constants/contracts.json";
import Providers from "../../js/constants/providers.json";

export const useContractStore = defineStore('contract',{
    state: () => ({
            resolvedName: "" , 
            resolvedNames: "[]",
            i : 0, 
            contractName:  "StakePoolC_v0_17c",
            response: '',
            publicApr: 13.15,
            presaleApr: 16.85,
            priceSelf: 0.00,
            nftTotalSupply: 0,
            chainNr: 56,
            retrieveOn: true
    }),

    getters: {
        provider(state) {
            let _endPoint;
            let _chainNr=this.chainNr
            if(_chainNr==1) _endPoint = Providers.ETH_1.node;
            if(_chainNr==5) _endPoint = Providers.GOERLI_1.node;
            if(_chainNr==97) _endPoint = Providers.BSC_Testnet_4.node;
            if(_chainNr==56) _endPoint = Providers.BSC_9.node;
            if(_chainNr==137) _endPoint = Providers.POLYGON_1.node;
            if(_chainNr==80001) _endPoint = Providers.POLYGON_Testnet_1.node;
            if(_chainNr==62621) _endPoint = Providers.MTV_1.node;
            if(_chainNr==11155111) _endPoint = Providers.SEPOLIA.node;
            return new ethers.JsonRpcProvider(_endPoint);
        },
    },

    actions: {

        async reset(){
            this.resolvedName= "";
            this.resolvedNames= "[]";
            this.i=0;
        },

        async readContract(_c = 'ContractName', _f = 'apr', _params=[]) {
            let c = Contracts[_c];
            this.chainNr = c.chain_id;
            const name = new ethers.Contract(c.address, c.abi, this.provider);
            this.response = await name[_f](..._params);
            return this.response;
        },

        async readBigField(_contract, _field, _params, _digits=2) {
            let _bal = await this.readContract(_contract, _field, _params); 
            return parseFloat(formatEther(_bal,18)).toFixed(_digits);
        },

        async getNextSelfNameB(_wallet) {
            if (_wallet.length>10) this.resolvedNames = await this.readContract("SELF_NFT_v224",'getNames', [_wallet]);
            if (this.resolvedNames.length > this.i){
                return this.resolvedNames[this.i++];
            }
            return this.resolvedNames[this.i];
        },

        async getNextSelfName(_wallet) {
                let that = this;
                return new Promise((resolve, reject) => {
                const check = async () => {
                try {
                    let _resolvedNames =[''];
                    if (_wallet.length>10) {
                        _resolvedNames = await that.readContract("SELF_NFT_v224",'getNames', [_wallet]);
                        that.resolvedNames = _resolvedNames.toString();
                    }
                    if (_resolvedNames.length > that.i){
                        resolve(_resolvedNames[that.i++]);
                    }
                    resolve(_resolvedNames[that.i]);
                } catch (err) {
                    console.log(`errrrrorrrr contracts.js : getNextSelfName rejects:: ${err}`);
                    resolve(`nope`);
                }
              }
              check(); 
            });
        },

        async runReadFunction(_line) {
            console.log(_line);

            let params = [];
            if (_line.parameters.length > 0) params = _line.parameters.split(",");
            console.log(params, params.length);

            let result = await this.rreadContract(_line.address, _line.contractAbi, _line.line, _line.chainNr,  params);
            console.log( _line.line,': ', result);
            return result;
        },

        async rreadContract(_address, _abi, _function, _chainNr , _params = []) {
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

            let result = await this.rreadContract(_line.address, _line.contractAbi, _line.line, _line.chainNr,  params);
            console.log( _line.line,': ', result);
            return result;
        },









    },
});

