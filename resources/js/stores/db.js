import { defineStore } from 'pinia';
import axios from 'axios';
//import crypto from 'crypto';

export const useDbStore = defineStore('db',{
    state: () => ({
        records: [],
        debugMode: false,
        payload: {},
        usdPrice: 0
    }),

    getters: {
        nRecords: (state) => state.records.length, 
    },

    actions: {
        async setDebugMode(_flag = false){
            this.debugMode=_flag;
        },

        async setState(_payload) {
            //console.log(_model);
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    //this.DebugMode('set.payload', _payload);
                    console.log(_payload);
                    const jsonPayload = JSON.stringify(_payload);
                   // const postSignature = crypto.createHmac('sha256', '31234KeyTestevnvornment')
                   //     .update(jsonPayload)
                   //     .digest('hex');
                    axios
                        .post(`/setstate`,_payload, {
                            headers: {
                                'withCredentials': true,
                                'content-type': 'application/json',
//                                'json' : jsonPayload,
                                'Sign': 'postSignature'
                            }
                        })
                    .then((response) => {
                        resolve(response.data);
                    })
                } catch (err) {
                  reject(`store/db.js:post:: ${err}`);
                }
              }
              check(); 
            });
        }, 
        async getState (_type, _path, _projectId) {
           // return ('nowGetPage');
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    axios
                        .get("/getstate", {
                            params: { 
                                caller: 'stores/db.js', 
                                provider: 'stateDataset', 
                                type: _type, 
                                path: _path, 
                                projectId: _projectId, 
                                user: 'jaWsome',
                                key: '<hashedKey hash#31234KeyTestevnvornment />'
                            }
                        })
                        .then((response) => {
                        // console.log(response.data.json);
                        // resolve('whatever');
                            resolve(response.data.json);
                        })
                } catch (err) {
                  reject(`store/db.js:getState:: ${err}`);
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
                        .post(`/setrecord`,_payload, {
                            headers: {
                                'withCredentials': true,
                                'content-type': 'application/json',
                                'caller': 'useDbStore', 
                                'model': _model, 
                                'Sign': 'postSignature',
                            }
                        })
                    .then((response) => {
                        resolve(response.data);
                    })
                } catch (err) {
                  reject(`store/db.js:post:: ${err}`);
                }
              }
              check(); 
            });
        }, 


        async setUser(_model, _payload) {
            //console.log(_model);
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    //this.DebugMode('set.payload', _payload);
                    const jsonPayload = JSON.stringify(_payload);
                   // const postSignature = crypto.createHmac('sha256', '31234KeyTestevnvornment')
                   //     .update(jsonPayload)
                   //     .digest('hex');
                    axios
                        .post(`/updateuserprofile`,_payload, {
                            headers: {
                                'withCredentials': true,
                                'content-type': 'application/json',
                                'caller': 'useDbStore', 
                                'model': _model, 
                                'Sign': 'postSignature',
                            }
                        })
                    .then((response) => {
                        console.log(response.data);
                        resolve(response.data);
                    })
                } catch (err) {
                  reject(`store/db.js:post:: ${err}`);
                }
              }
              check(); 
            });
        }, 





        async get (_model) {
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    axios
                        .get("/getrecords", {
                            params: { 
                                caller: 'db', 
                                provider: _model, 
                                user: 'jaWsome',
                                key: '<hashedKey hash#31234KeyTestevnvornment />'
                            }
                        })
                        .then((response) => {
                            resolve(response.data);
                        })
                } catch (err) {
                  reject(`store/db.js:get:: ${err}`);
                }
              }
              check(); 
            });
        }, 

    async getTables (_model) {
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    axios
                        .get("/gettables", {
                            params: { 
                                caller: 'db', 
                                provider: _model, 
                                user: 'jaWsome',
                                key: '<hashedKey hash#31234KeyTestevnvornment />'
                            }
                        })
                        .then((response) => {
                            resolve(response.data);
                        })
                } catch (err) {
                  reject(`store/db.js:getTables:: ${err}`);
                }
              }
              check(); 
            });
        }, 



        async getPrice (_token) {
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    axios
                        .get("/getprice", {
                            params: { 
                                caller: 'store/db.js', 
                                provider: 'price', 
                                token: _token, 
                                user: 'jaWsome',
                                key: '<hashedKey hash#31234KeyTestevnvornment />'
                            }
                        })
                        .then((response) => {
                            this.usdPrice = response.data;
                            resolve(response.data);
                        })
                } catch (err) {
                  reject(`store/db.js:getPrice:: ${err}`);
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
                                caller: 'useDbStore', 
                                provider: _model, 
                                field: _field, 
                                search: _search, 
                                user: 'jaWsome',
                                key: 'hash#31234KeyTestevnvornment'
                            }
                        })
                        .then((response) => {
                            resolve(response.data[0]);
                        })
                } catch (err) {
                  reject(`store/db.js:.getRecord:: ${err}`);
                }
              }
              check(); 
            });
        }, 

        async getPage (_name) {
           // return ('nowGetPage');
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    axios
                        .get("/getpage", {
                            params: { 
                                caller: 'stores/db.js', 
                                provider: 'page', 
                                name: _name, 
                                user: 'jaWsome',
                                key: '<hashedKey hash#31234KeyTestevnvornment />'
                            }
                        })
                        .then((response) => {
                        //resolve('whatever');
                            resolve(response.data);
                        })
                } catch (err) {
                  reject(`store/db.js:getPrice:: ${err}`);
                }
              }
              check(); 
            });
        }



    }
});
