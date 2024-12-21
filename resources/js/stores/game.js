 import { defineStore } from 'pinia';
import axios from 'axios';

export const useGameStore = defineStore('game',{
    state: () => ({
        gameOn: false,
        highScore: 230,
        stage: 0,
        score: 0,
        highScoreHolder:'none'
    }),

    actions: {
        async set(_model, _payload) {
            //console.log(_model);
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    const jsonPayload = JSON.stringify(_payload);
                   // const postSignature = crypto.createHmac('sha256', '31234KeyTestevnvornment')
                   //     .update(jsonPayload)
                   //     .digest('hex');
                    console.log(_payload);
                    axios
                        .post(`/setgame`,_payload, {
                            headers: {
                                'withCredentials': true,
                                'content-type': 'application/json',
                                'caller': 'useGameStore', 
                                'model': _model, 
                                'Sign': 'postSignature'
                            }
                        })
                        .then((response) => {
                            console.log('response:', response)
                        resolve();
                    })
                } catch (err) {
                  reject(`store/game.js:post:: ${err}`);
                }
              }
              check(); 
            });
        }, 

        async get (_game) {
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    axios
                        .get("/getgame", {
                            params: { 
                                caller: 'store/game.js', 
                                provider: 'game', 
                                user: 'jaWsome',
                                title: _game,
                                key: '<hashedKey hash#31234KeyTestevnvornment />'
                            }
                        })
                        .then((response) => {
                            resolve(response.data);
                        })
                } catch (err) {
                  reject(`store/game.js:getGame: ${err}`);
                }
              }
              check(); 
            });
        }, 









        async setScore(_model, _payload) {
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    const jsonPayload = JSON.stringify(_payload);
                    
console.log(_model, _payload);

                    axios
                        .post(`/setscore`,_payload, {
                            headers: {
                                'withCredentials': true,
                                'content-type': 'application/json',
                                'caller': 'useGameStore', 
                                'model': _model, 
                                'Sign': 'postSignature'
                            }
                        })
                        .then((response) => {
                        resolve(response.data);
                    })
                } catch (err) {
                  reject(`store/game.js:post:: ${err}`);
                }
              }
              check(); 
            });
        }, 





        async getScore (_game) {
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    axios
                        .get("/getgame", {
                            params: { 
                                caller: 'store/game.js', 
                                provider: 'game', 
                                user: 'jaWsome',
                                title: 'highScore',
                                key: '<hashedKey hash#31234KeyTestevnvornment />'
                            }
                        })
                        .then((response) => {
                            this.highScore = response.data.nvalue;
                            this.highScoreHolder = response.data.description;
                            resolve(response.data);
                        })
                } catch (err) {
                  reject(`store/game.js:getHighscore:: ${err}`);
                }
              }
              check(); 
            });
        }, 



        async getHighScore (_game) {
            return new Promise((resolve, reject) => {
              const check = async () => {
                try {
                    axios
                        .get("/getgame", {
                            params: { 
                                caller: 'store/game.js', 
                                provider: 'game', 
                                user: 'jaWsome',
                                title: 'highScore',
                                key: '<hashedKey hash#31234KeyTestevnvornment />'
                            }
                        })
                        .then((response) => {
                            this.highScore = response.data.nvalue;
                            this.highScoreHolder = response.data.description;
                            resolve(response.data);
                        })
                } catch (err) {
                  reject(`store/game.js:getHighscore:: ${err}`);
                }
              }
              check(); 
            });
        }, 
    }
});
