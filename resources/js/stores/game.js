import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useGameStore = defineStore('game', () => {
    const gameOn = ref(false);
    const highScore = ref(230);
    const stage = ref(0);
    const score = ref(0);
    const highScoreHolder = ref('none');

    async function set(_model, _payload) {
        try {
            console.log(_payload);
            const response = await axios.post(`/setgame`, _payload, {
                headers: {
                    'withCredentials': true,
                    'content-type': 'application/json',
                    'caller': 'useGameStore',
                    'model': _model,
                    'Sign': 'postSignature'
                }
            });
            console.log('response:', response);
        } catch (err) {
            console.error(`store/game.js:post:: ${err}`);
        }
    }

    async function get(_game) {
        try {
            const response = await axios.get("/getgame", {
                params: {
                    caller: 'store/game.js',
                    provider: 'game',
                    user: 'jaWsome',
                    title: _game,
                    key: '<hashedKey hash#31234KeyTestevnvornment />'
                }
            });
            return response.data;
        } catch (err) {
            console.error(`store/game.js:getGame: ${err}`);
        }
    }

    async function setScore(_model, _payload) {
        try {
            console.log(_model, _payload);
            const response = await axios.post(`/setscore`, _payload, {
                headers: {
                    'withCredentials': true,
                    'content-type': 'application/json',
                    'caller': 'useGameStore',
                    'model': _model,
                    'Sign': 'postSignature'
                }
            });
            return response.data;
        } catch (err) {
            console.error(`store/game.js:post:: ${err}`);
        }
    }

    async function getHighScore() {
        try {
            const response = await axios.get("/getgame", {
                params: {
                    caller: 'store/game.js',
                    provider: 'game',
                    user: 'jaWsome',
                    title: 'highScore',
                    key: '<hashedKey hash#31234KeyTestevnvornment />'
                }
            });
            highScore.value = response.data.nvalue;
            highScoreHolder.value = response.data.description;
            return response.data;
        } catch (err) {
            console.error(`store/game.js:getHighscore:: ${err}`);
        }
    }

    return {
        gameOn,
        highScore,
        stage,
        score,
        highScoreHolder,
        set,
        get,
        setScore,
        getHighScore
    };
});