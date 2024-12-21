import { defineStore } from 'pinia';
import axios from 'axios';
//import crypto from 'crypto';

export const useDataLayerStore = defineStore('DataLayer',{
    state: () => ({
        kpi1: 82,
        kpi2: 23,
        kpi3: 3,
    }),

    getters: {
        nRecords: (state) => state.records.length, 
    },

    actions: {
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
    }
});
