import { defineStore } from 'pinia';
import axios from 'axios';
//import crypto from 'crypto';

export const useWeb3DbStore = defineStore('web3Db',{
    state: () => ({
        name: "start" , 
        type: "" ,
        types: [],
        projects: [],
        records: [],
        payload: {},
        description: "", 
        debugMode: false,
        storeName: 'web3DbStore'
    }),

    //Getters are exactly the equivalent of computed values for the state of a Store. They can be defined with the getters property in defineStore(). They receive the state as the first parameter to encourage the usage of arrow function:
    getters: {
        provider: (state) => state.projects.length, 
    },

    actions: {

        addPost(_client, _msg) {
            if(this.debugMode){
                console.log(this.storeName,"->", _client,"->",_msg);
            }
        },
        async setDebugMode(_flag = false){
            this.debugMode=_flag;
        },

        async _loadPayload(_id=0) {
            const event = new Date();
            const timestamp = event.toISOString();
            this.payload = {
                timestamp: timestamp,
                title: 'FlightToLondon23',
                address: '0x02',
                url: 'https://',
                web3type_id: 1,
                web3project_id: 1,
                web3chain_id: 1,
                tags: 'test',
                abi: '[abi]',
                description: 'description',
                json: '{"key":"value"}',
                completed:0,
                archived:0
              };
        },

        async initialize() {
            this._loadPayload();
            this.types =    await this.get('Web3Type');
            this.projects = await this.get('Web3Project');
            this.chains = await this.get('Web3Chain');
            this.records =  await this.get('Web3Record');
            this.addPost('initialize', 'initializez');
            // console.log(this.records);
            // await this.set('Web3Record', _payload);
        },


        async setComplete(_model , _payload) {
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    const jsonPayload = JSON.stringify(_payload);
                    axios
                        .post(`/setweb3recordcomplete`,_payload, {
                            headers: {
                                'withCredentials': true,
                                'content-type': 'application/json',
                                'caller': 'web3DbStore', 
                                'model': _model, 
                                'Sign': 'postSignature',
                            }
                        })
                    .then((response) => {
                        this.addPost('setComplete', response);
                        resolve(response.data);
                    })
                } catch (err) {
                  reject(`web3DbStore.setComplete:: ${err}`);
                }
              }
              check(); 
            });
        }, 


        async set(_model, _payload) {
            //console.log(_model);
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    this.DebugMode('set.payload', _payload);
                    const jsonPayload = JSON.stringify(_payload);
                   // const postSignature = crypto.createHmac('sha256', '31234KeyTestevnvornment')
                   //     .update(jsonPayload)
                   //     .digest('hex');
                    axios
                        .post(`/setweb3record`,_payload, {
                            headers: {
                                'withCredentials': true,
                                'content-type': 'application/json',
                                'caller': 'web3DbStore', 
                                'model': _model, 
                                'Sign': 'postSignature',

                            }
                        })
                    .then((response) => {
                        this.addPost('set', response);
                        resolve(response.data);
                    })
                } catch (err) {
                  reject(`web3DbStore.post:: ${err}`);
                }
              }
              check(); 
            });
        }, 

        /** @dev setLine : routine called by StakePoolSection.vue = lines per contract
         *  @parameter model : title of the mvc model
         *      example: 'web3RecordLine',
         *  @parameter payload : json column_names + values 
         *      example:
         *    { 'web3record_id' : '24',
         *      'line' : 'tokenIdToName',
         *      'line_nr' : '13',
         *      'abi' : 'tokenIdToName function tokenIdToName(uint256) view returns (string)',
         *      'parameters' : '{}',
         *      'value' : 'empty',
         *      'int_value' : '0.00',
         *      'columns' : 'col-span-1',
         *      'style' : 'text-sm text-green-700',
         *      'type' : 'bigInt',
         *      'detail' : '',
         *      'is_active' : '1',
         *      'is_live' : '1'
         *     }
         */
        async setLine(_model, _payload) {
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    const jsonPayload = JSON.stringify(_payload);
                    // const postSignature = crypto.createHmac('sha256', '31234KeyTestevnvornment')
                    //     .update(jsonPayload)
                    //     .digest('hex');
                    axios
                        .post(`/setweb3recordline`,_payload, {
                            headers: {
                                'withCredentials': true,
                                'content-type': 'application/json',
                                'caller': 'web3DbStore', 
                                'model': _model, 
                                'Sign': 'postSignature',
                            }
                        })
                    .then((response) => {
                        this.addPost('setLine', response);
                        resolve(response.data);
                    })
                } catch (err) {
                  reject(`web3DbStore.post:: ${err}`);
                }
              }
              check(); 
            });
        }, 

        /** @dev createNewLine : routine called by StakePoolSection.vue for creating a new contractLine
         *  @parameter _web3RecordId : the web3Record_id (contract)
         *  @parameter _line : the functionName
         *  @parameter _abi : the ABI for this functionName
         *  @parameter _nr : the sequenceNumber for this contract
         */
        async createNewLine(_web3RecordId,_line, _abi, _nr=0) {
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    let columns = 'col-span-1';
                    let style = " text-sm text-green-700";
                    let type = "bigInt";
                    let update = 1;
                    let detail = '';
                    let parameters = '0';

                    if(_abi.indexOf('apr ()')>0){ type = "percentage";}
                    if(_abi.indexOf('whitelistedAddresses(')>0) { parameters = "parameter1"; type = "int"}
                    if(_abi.indexOf('whiteListDisabled(')>0) { type = "int"}
                    if(_abi.indexOf('userRewardPerTokenPaid(')>0) { type = "bigInt"}
                    if(_abi.indexOf('stakingToken(')>0) { type = "text"}
                    if(_abi.indexOf('stakeSupply(')>0) { type = "bigInt"}
                    if(_abi.indexOf('rewardsToken(')>0) { type = "text"}
                    if(_abi.indexOf('rewards(')>0) { parameters = "parameter1"; type = "bigInt_3n"}
                    if(_abi.indexOf('rewardSupply(')>0) { type = "bigInt"}
                    if(_abi.indexOf('rewardRate(')>0) { type = "bigInt_3n"}
                    if(_abi.indexOf('rewardPerToken(')>0) { type = "bigInt"}
                    if(_abi.indexOf('rewardPerTokenStored(')>0) { type = "bigInt"}
                    if(_abi.indexOf('nameContract(')>0) { type = "text"; columns ='col-span-2'}
                    if(_abi.indexOf('lastTimeRewardApplicable(')>0) { type = "int"}
                    if(_abi.indexOf('getWhitelisted(')>0) { parameters = "parameter1"; type = "text"}
                    if(_abi.indexOf('earned(')>0) { parameters = "parameter1"; type = "bigInt_3n"}
                    if(_abi.indexOf('balanceOf(')>0) { parameters = "parameter1"; type = "bigInt"}
                    if(_abi.indexOf('getWhiteListed(')>0) { parameters = "parameter1"; type = "text"}
                    if(_abi.indexOf('blacklistedNames(')>0) { parameters = "parameter1"; type = "text"}
                    if(_abi.indexOf('getNames(')>0) { parameters = "parameter1"; type = "array"}
                    if(_abi.indexOf('agents(')>0) { parameters = "parameter1"; type = "array"}
                    if(_abi.indexOf('isApprovedForAll(')>0) { parameters = "parameter1,parameter2"; type = "text"}
                    if(_abi.indexOf('isNameAvailable(')>0) { parameters = "parameter1"; type = "int"}
                    if(_abi.indexOf('tokenIdToName(')>0) { parameters = "parameter1"; type = "text"}
                    if(_abi.indexOf('tokenOfOwnerByIndex(')>0) { parameters = "parameter1"; type = "int"}
                    if(_abi.indexOf('tokenURI(')>0) { parameters = "parameter1"; type = "int"}
                    if(_abi.indexOf('symbol(')>0) { type = "text"}
                    if(_abi.indexOf('name(')>0) { type = "text"}

                    if(_abi.indexOf('returns (address)')>0){ 
                        columns = 'col-span-3 text-xs';
                        style = " text-indigo-700 text-xs" ;
                        type = "address";
                    }

                    if(_abi.indexOf('returns (bool)')>0){ 
                        type = "int";
                    }

                  if(['duration','finishAt','updatedAt'].includes(_line)){ 
                    columns = 'col-span-1 text-sm';
                    style = " text-gray-900 text-sm";
                    type = "int";  
                    update = 1;
                    detail = '-';
                  }

                  if (_nr>0) {
                    let payload = 
                    { 'web3record_id' : _web3RecordId,
                      'line' : _line,
                      'line_nr' : _nr,
                      'abi' : _abi,
                      'parameters' : parameters,
                      'value' : '0',
                      'int_value' : '0.00',
                      'columns' : columns,
                      'style' : style,
                      'type' : type,
                      'detail' : '',
                      'computed' : '0',
                      'is_active' : '1',
                      'is_live' : update
                    }
                    let result =   await this.setLine('Web3RecordLine',payload);
                  }
                } catch (err) {
                  reject(`web3DbStore.post:: ${err}`);
                }
              }
              check(); 
            });
        }, 


        /** Panadero API Calls
        * important: SELF API requirement specs!!!!
        * all API' need to be 100% secure!
        * but also:  
        * 1. reduces complexity
        * 2. creates simplicity
        * 3. operates in real-time
        * 4. provides transparency
        * 5. seamless integration
        * 6. encrypted calls 
        * 7. ease of management
        */
        async get (_model) {
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    axios
                        .get("/getweb3records", {
                            params: { 
                                caller: 'web3DbStore', 
                                provider: _model, 
                                user: 'jaWsome',
                                key: '<hashedKey hash#31234KeyTestevnvornment />'
                            }
                        })
                        .then((response) => {
                            this.addPost('get', response);
                            resolve(response.data);
                        })
                } catch (err) {
                  reject(`web3DbStore.get:: ${err}`);
                }
              }
              check(); 
            });
        }, 
     

        /** @dev getLine : routine called by StakePoolSection.vue
         *  @parameter model : title of the mvc model
         *      example: 'web3RecordLine',
         *  @parameter payload : json column_names + values 
         *      example:
         *    { 'web3record_id' : '24',
         *      'line' : 'tokenIdToName',
         *      'line_nr' : '13'
         *     }
         */
        async getLine(_model, _payload) {
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {

                    axios
                        .get("/getweb3recordline", {
                            params: { 
                                caller: 'web3DbStore', 
                                model: _model, 
                                web3record_id: _payload.web3record_id,
                                line: _payload.line,
                                line_nr: _payload.line_nr ,
                            }
                        })
                    .then((response) => {
                        this.addPost('getLine', response);
                        resolve(response.data);
                    })
                } catch (err) {
                  reject(`web3DbStore.post:: ${err}`);
                }
              }
              check(); 
            });
        }, 

        async getRecord (_model, _field, _search) {
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    axios
                        .get("/getrecord", {
                            params: { 
                                caller: 'web3DbStore', 
                                provider: _model, 
                                field: _field, 
                                search: _search, 
                                user: 'jaWsome',
                                key: 'hash#31234KeyTestevnvornment'
                            }
                        })
                        .then((response) => {
                            this.addPost('getRecord', response);
                            resolve(response.data[0]);
                        })
                } catch (err) {
                  reject(`web3DbStore.getRecord:: ${err}`);
                }
              }
              check(); 
            });
        }, 


        // njet working.. crypto module needs attention!
        /** Panadero API Calls
        * important: SELF API requirement specs!!!!
        * all API' need to be 100% secure!
        * but also:  
        * 1. reduces complexity
        * 2. creates simplicity
        * 3. operates in real-time
        * 4. provides transparency
        * 5. seamless integration
        * 6. encrypted calls 
        * 7. ease of management
        */
        async apiGet(_model) {
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    const event = new Date();
                    const timestamp = event.toISOString();
                    
                    // example transfer of data
                    const _payload = {
                        timestamp: timestamp,
                        symbol: 'BTCUSDT',
                        transfer: 'SEND',
                        maxPrice: '21440',
                        quantity: '0.02',
                        selfOrderId: 'self-23000019'
                      };
                    const jsonPayload = JSON.stringify(_payload);
                  //  const postSignature = crypto.createHmac('sha256', '<hashedKey_hash#31234KeyTestevnvornment/>')
                  //      .update(jsonPayload)
                  //      .digest('hex');
                    axios
                        .post(`/setweb3record`,_payload, {
                            headers: {
                                'content-type': 'application/json',
                                'caller': 'web3DbStore', 
                    //            'Sign': postSignature,
                            caller: 'web3DbStore', 
                                provider: _model, 
                                field: _field, 
                                search: _search, 
                                user: 'jaWsome',
                                key: '<hashedKey_hash#31234KeyTestevnvornment/>'
                            }
                        })
                    .then((response) => {
                        this.addPost('apiGet', response);
                        resolve(response.data);
                    })
                } catch (err) {
                  reject(`web3DbStore.post:: ${err}`);
                }
              }
              check(); 
            });
        }, 

        async reset() {
            this.name = "";
            this.type = "";
            this.description = "";
        },

        async getRecords(_scope) {
            let start = 0;
            if(_scope=="all" || _scope=="Web3Type") this.types = await this.get('Web3Type');
            if(_scope=="all" || _scope=="Web3Project") this.projects = await this.get('Web3Project');
            if(_scope=="all" || _scope=="Web3Chain") this.chains = await this.get('Web3Chain');
            if(_scope=="all" || _scope=="Web3Record") this.records =  await this.get('Web3Record');
        },

        async setRecord(_i) {
            let start = 0;
        },
    },
});
