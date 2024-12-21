import { defineStore } from 'pinia';
import axios from 'axios';
//import crypto from 'crypto';

export const useDbStore = defineStore('db',{
    state: () => ({
        records: [],
        debugMode: false,
        payload: {}
    }),

    getters: {
        nRecords: (state) => state.records.length, 
    },

    actions: {
        async setDebugMode(_flag = false){
            this.debugMode=_flag;
        },

        async set(_model, _payload) {
            //console.log(_model);
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    this.DebugMode('set.payload', _payload);
                    const jsonPayload = JSON.stringify(_payload);
                    // todo !! find new Hmac module.. and test.. this aint Node.js for christ sake.
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
                  reject(`shared/stores/db.js:post:: ${err}`);
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
                  reject(`shared/stores/db.js:get:: ${err}`);
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
                  reject(`shared/stores/db.js:.getRecord:: ${err}`);
                }
              }
              check(); 
            });
        }, 
    }
});
