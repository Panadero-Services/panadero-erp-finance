import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useWalletStore = defineStore('wallet', () => {
    const account = ref("0x0");
    const assets = ref({});
    const lastAsset = ref({});
    const count = ref(0);

    async function reset() {
        account.value = "0x0";
        assets.value = {};
    }

    async function fill() {
        const accounts = await ethereum.request({ method: 'eth_requestAccounts' });
        account.value = accounts[0];
    }

    async function initialize() {
        await fill();
    }

    async function setBalances(_balances) {
        Object.entries(_balances).forEach(([key, value]) => {
            console.log(`${key} ${value}`);
            _setBalance(key, value[account.value]);
        });
    }

    function _setBalance(_key, _value) {
        if (!assets.value[_key]) assets.value[_key] = {};
        assets.value[_key] = _value;
    }

    return {
        account, assets, lastAsset, count,
        reset, fill, initialize, setBalances
    };
});