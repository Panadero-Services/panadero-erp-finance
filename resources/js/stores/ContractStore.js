import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { ethers } from 'ethers';
import Contracts from "../../js/constants/contracts.json";
import Providers from "../../js/constants/providers.json";

export const useContractStore = defineStore('contract', () => {
    const name = ref("");
    const chainNr = ref(97);
    const endPoint = ref("");
    const type = ref("");
    const address = ref("0x0");
    const decimals = ref(18);
    const balances = ref({});
    const abi = ref([]);
    const stats = ref([]); // contractLines

    const provider = computed(() => {
        switch (chainNr.value) {
            case 97: endPoint.value = Providers.BSC_Testnet_4.node; break;
            case 56: endPoint.value = Providers.BSC_9.node; break;
            case 5: endPoint.value = Providers.GOERLI_1.node; break;
            case 137: endPoint.value = Providers.POLYGON_1.node; break;
            case 80001: endPoint.value = Providers.POLYGON_Testnet_1.node; break;
            case 62621: endPoint.value = Providers.MTV_1.node; break;
            default: endPoint.value = "";
        }
        return new ethers.JsonRpcProvider(endPoint.value);
    });

    async function initialize() {
        console.log('initialize');
    }

    async function getStats() {
        console.log('getStats');
    }

    async function reset() {
        name.value = "";
        type.value = "";
        balances.value = {};
        chainNr.value = 0;
        address.value = "0x0";
        abi.value = [];
    }

    function set(_name) {
        name.value = _name;
        chainNr.value = Contracts[_name].chain_id;
        type.value = Contracts[_name].type;
        address.value = Contracts[_name].address;
        balances.value = {};
        abi.value = Contracts[_name].abi;
    }

    function setFromDb(_e) {
        name.value = _e.title;
        chainNr.value = _e.web3chain.chain_nr;
        type.value = _e.web3type.title;
        address.value = _e.address;
        decimals.value = 18;
        abi.value = _e.abi;
    }

    async function readContract(contractAddress, contractAbi, functionName, params = []) {
        const contract = new ethers.Contract(contractAddress, contractAbi, provider.value);
        return await contract[functionName](...params);
    }

    async function getBalanceFromDb(_e, _wallet) {
        console.log(_e.address, _wallet);
        let _balance = await readContract(_e.address, _e.abi, "totalSupply", []);
        let _convertBalance = Number(BigInt(_balance) / 1_000_000_000_000_000n) / 1000;
        console.log(_e.title, "balance", _wallet, ": ", _convertBalance);
        if (!balances.value[_e.title]) balances.value[_e.title] = {};
        balances.value[_e.title][_wallet] = _convertBalance;
        return { name: _e.title, balance: _convertBalance };
    }

    async function getTotalSupply(_e) {
        console.log(_e.address);
        let _supply = await readContract(_e.address, _e.abi, "duration", []);
        console.log(_e.title, "supply", ": ", _supply);
        return { name: _e.title, supply: _supply };
    }

    async function getBalanceFromJson(_contract, _wallet) {
        let _balance = await readContract(Contracts[_contract].address, Contracts[_contract].abi, "totalSupply", [_wallet]);
        let _convertBalance = Number(BigInt(_balance) / 1_000_000_000_000_000n) / 1000;
        console.log(_contract, "balance", _wallet, ": ", _convertBalance);
        if (!balances.value[_contract]) balances.value[_contract] = {};
        balances.value[_contract][_wallet] = _convertBalance;
        return { name: _contract, balance: _convertBalance };
    }

    async function readContractWithChain(_address, _abi, _function, _chainNr, _params = []) {
        chainNr.value = _chainNr;
        return await readContract(_address, _abi, _function, _params);
    }

    async function runReadFunction(_line) {
        console.log(_line);
        let params = _line.parameters.length > 0 ? _line.parameters.split(",") : [];
        let result = await readContractWithChain(_line.address, _line.contractAbi, _line.line, _line.chainNr, params);
        console.log(_line.line, ": ", result);
        return result;
    }

    return {
        name, chainNr, endPoint, type, address, decimals, balances, abi, stats,
        provider,
        initialize, getStats, reset, set, setFromDb,
        readContract, getBalanceFromDb, getTotalSupply, getBalanceFromJson,
        readContractWithChain, runReadFunction
    };
});
