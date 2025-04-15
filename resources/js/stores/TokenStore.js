import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { ethers } from 'ethers';
import Tokens from "../../js/constants/tokens.json";
import Providers from "../../js/constants/providers.json";

export const useTokenStore = defineStore('token', () => {
    const name = ref("");
    const chainNr = ref(0);
    const endPoint = ref("");
    const type = ref("");
    const address = ref("0x0");
    const decimals = ref(18);
    const balances = ref({});
    const abi = ref([]);
    const balance = ref(-1);

    const provider = computed(() => {
        if (chainNr.value === 97) endPoint.value = Providers.BSC_Testnet_4.node;
        if (chainNr.value === 56) endPoint.value = Providers.BSC_9.node;
        if (chainNr.value === 5) endPoint.value = Providers.GOERLI_1.node;
        if (chainNr.value === 137) endPoint.value = Providers.POLYGON_1.node;
        if (chainNr.value === 80001) endPoint.value = Providers.POLYGON_Testnet_1.node;
        if (chainNr.value === 62621) endPoint.value = Providers.MTV_1.node;
        return new ethers.JsonRpcProvider(endPoint.value);
    });

    async function reset() {
        name.value = "";
        chainNr.value = 0;
        type.value = "";
        address.value = "0x0";
        decimals.value = 18;
        balances.value = {};
        abi.value = [];
        balance.value = -1;
    }

    function increment() {
        balance.value++;
    }

    function decrement() {
        balance.value--;
    }

    function set(_name) {
        name.value = _name;
        chainNr.value = Tokens[_name].chain_id;
        type.value = Tokens[_name].type;
        address.value = Tokens[_name].address;
        decimals.value = Tokens[_name].decimals;
        abi.value = Tokens[_name].abi;
    }

    function setFromDb(_e) {
        name.value = _e.title;
        chainNr.value = _e.web3chain.chain_nr;
        type.value = _e.web3type.title;
        address.value = _e.address;
        decimals.value = 18;
        abi.value = _e.abi;
    }

    async function readContract(contractAddress, abi, functionName, params = []) {
        const contract = new ethers.Contract(contractAddress, abi, provider.value);
        return await contract[functionName](...params);
    }

    async function getBalanceFromDb(_e, _wallet) {
        let _balance = await readContract(_e.address, _e.abi, "balanceOf", [_wallet]);
        let _convertBalance = Number(BigInt(_balance) / 1000_000_000_000_000n) / 1000;
        if (!balances.value[_e.title]) balances.value[_e.title] = {};
        balances.value[_e.title][_wallet] = _convertBalance;
        return { name: _e.title, balance: _convertBalance };
    }

    async function getBalanceFromJson(_token, _wallet) {
        let _balance = await readContract(Tokens[_token].address, Tokens[_token].abi, "balanceOf", [_wallet]);
        let _convertBalance = Number(BigInt(_balance) / 1000_000_000_000_000n) / 1000;
        if (!balances.value[_token]) balances.value[_token] = {};
        balances.value[_token][_wallet] = _convertBalance;
        return { name: _token, balance: _convertBalance };
    }

    async function initialize() {
        //await fill();
    }

    return {
        name, chainNr, endPoint, type, address, decimals, balances, abi, balance, provider, initialize,
        reset, increment, decrement, set, setFromDb, readContract, getBalanceFromDb, getBalanceFromJson
    };
});
