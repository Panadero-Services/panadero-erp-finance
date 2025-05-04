import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useDbStore = defineStore('db', () => {
    const records = ref([]);
    const debugMode = ref(false);
    const payload = ref({});
    const usdPrice = ref(0);

    const nRecords = computed(() => records.value.length);

    async function setDebugMode(_flag = false) {
        debugMode.value = _flag;
    }

    async function setState(_payload) {
        try {
            console.log(_payload);
            const response = await axios.post('/setstate', _payload, {
                headers: {
                    'withCredentials': true,
                    'content-type': 'application/json',
                    'Sign': 'postSignature'
                }
            });
            return response.data;
        } catch (err) {
            throw new Error(`store/db.js: setState error: ${err}`);
        }
    }

    async function getState(_type, _path, _projectId) {
        try {
            const response = await axios.get('/getstate', {
                params: {
                    caller: 'stores/db.js',
                    provider: 'stateDataset',
                    type: _type,
                    path: _path,
                    projectId: _projectId,
                    user: 'jaWsome',
                    key: '<hashedKey hash#31234KeyTestevnvornment />'
                }
            });
            return response.data.json;
        } catch (err) {
            throw new Error(`store/db.js: getState error: ${err}`);
        }
    }

    async function insertTeam(_model, _payload) {
       try {
                console.log(_payload);

            const response = await axios.post('/insertTeam', _payload, {
                headers: {
                    'withCredentials': true,
                    'content-type': 'application/json',
                    'caller': 'useDbStore',
                    'model': _model,
                    'Sign': 'postSignature'
                }
            });
            return response.data;
        } catch (err) {
            throw new Error(`store/db.js: set error: ${err}`);
        }
    }

    async function insertProject(_model, _payload) {
       try {
                console.log(_payload);

            const response = await axios.post('/insertProject', _payload, {
                headers: {
                    'withCredentials': true,
                    'content-type': 'application/json',
                    'caller': 'useDbStore',
                    'model': _model,
                    'Sign': 'postSignature'
                }
            });
            return response.data;
        } catch (err) {
            throw new Error(`store/db.js: set error: ${err}`);
        }
    }

    async function set(_model, _payload) {
        try {
            const response = await axios.post('/setrecord', _payload, {
                headers: {
                    'withCredentials': true,
                    'content-type': 'application/json',
                    'caller': 'useDbStore',
                    'model': _model,
                    'Sign': 'postSignature'
                }
            });
            return response.data;
        } catch (err) {
            throw new Error(`store/db.js: set error: ${err}`);
        }
    }

    async function setUser(_model, _payload) {
        try {
            const response = await axios.post('/updateuserprofile', _payload, {
                headers: {
                    'withCredentials': true,
                    'content-type': 'application/json',
                    'caller': 'useDbStore',
                    'model': _model,
                    'Sign': 'postSignature'
                }
            });
            return response.data;
        } catch (err) {
            throw new Error(`store/db.js: set error: ${err}`);
        }
    }

    async function get(_model) {
        try {
            const response = await axios.get('/getrecords', {
                params: {
                    caller: 'db',
                    provider: _model,
                    user: 'jaWsome',
                    key: '<hashedKey hash#31234KeyTestevnvornment />'
                }
            });
            return response.data;
        } catch (err) {
            throw new Error(`store/db.js: get error: ${err}`);
        }
    }

    async function getPrice(_token) {
        try {
            const response = await axios.get('/getprice', {
                params: {
                    caller: 'store/db.js',
                    provider: 'price',
                    token: _token,
                    user: 'jaWsome',
                    key: '<hashedKey hash#31234KeyTestevnvornment />'
                }
            });
            usdPrice.value = response.data;
            return response.data;
        } catch (err) {
            throw new Error(`store/db.js: getPrice error: ${err}`);
        }
    }

    return {
        records, debugMode, payload, usdPrice, nRecords,
        setDebugMode, setState, getState, set, get, getPrice, 
        insertTeam, insertProject, setUser
    };
});