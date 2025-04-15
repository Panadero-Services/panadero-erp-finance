import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { ethers, formatEther } from 'ethers';
import Contracts from "../../js/constants/contracts.json";
import Providers from "../../js/constants/providers.json";

export const useContractStore = defineStore('contract', () => {
    const resolvedName = ref("");
    const resolvedNames = ref("[]");
    const i = ref(0);
    const contractName = ref("StakePoolC_v0_17c");
    const response = ref('');
    const publicApr = ref(13.15);
    const presaleApr = ref(16.85);
    const priceSelf = ref(0.00);
    const nftTotalSupply = ref(0);
    const chainNr = ref(56);
    const retrieveOn = ref(true);

    const provider = computed(() => {
        let _endPoint;
        switch (chainNr.value) {
            case 1: _endPoint = Providers.ETH_1.node; break;
            case 5: _endPoint = Providers.GOERLI_1.node; break;
            case 97: _endPoint = Providers.BSC_Testnet_4.node; break;
            case 56: _endPoint = Providers.BSC_9.node; break;
            case 137: _endPoint = Providers.POLYGON_1.node; break;
            case 80001: _endPoint = Providers.POLYGON_Testnet_1.node; break;
            case 62621: _endPoint = Providers.MTV_1.node; break;
            case 11155111: _endPoint = Providers.SEPOLIA.node; break;
            default: _endPoint = "";
        }
        return new ethers.JsonRpcProvider(_endPoint);
    });

    async function reset() {
        resolvedName.value = "";
        resolvedNames.value = "[]";
        i.value = 0;
    }

    async function readContract(_c = 'ContractName', _f = 'apr', _params = []) {
        let c = Contracts[_c];
        chainNr.value = c.chain_id;
        const contract = new ethers.Contract(c.address, c.abi, provider.value);
        response.value = await contract[_f](..._params);
        return response.value;
    }

    async function readBigField(_contract, _field, _params, _digits = 2) {
        let _bal = await readContract(_contract, _field, _params);
        return parseFloat(formatEther(_bal, 18)).toFixed(_digits);
    }

    async function getNextSelfNameB(_wallet) {
        if (_wallet.length > 10) resolvedNames.value = await readContract("SELF_NFT_v224", 'getNames', [_wallet]);
        if (resolvedNames.value.length > i.value) {
            return resolvedNames.value[i.value++];
        }
        return resolvedNames.value[i.value];
    }

    async function getNextSelfName(_wallet) {
        return new Promise(async (resolve) => {
            try {
                let _resolvedNames = [''];
                if (_wallet.length > 10) {
                    _resolvedNames = await readContract("SELF_NFT_v224", 'getNames', [_wallet]);
                    resolvedNames.value = _resolvedNames.toString();
                }
                if (_resolvedNames.length > i.value) {
                    resolve(_resolvedNames[i.value++]);
                }
                resolve(_resolvedNames[i.value]);
            } catch (err) {
                console.log(`Error in getNextSelfName: ${err}`);
                resolve(`nope`);
            }
        });
    }

    async function readContractWithChain(_address, _abi, _function, _chainNr, _params = []) {
        chainNr.value = _chainNr;
        const contract = new ethers.Contract(_address, _abi, provider.value);
        return await contract[_function](..._params);
    }

    async function runReadFunction(_line) {
        console.log(_line);
        let params = _line.parameters.length > 0 ? _line.parameters.split(",") : [];
        let result = await readContractWithChain(_line.address, _line.contractAbi, _line.line, _line.chainNr, params);
        console.log(_line.line, ': ', result);
        return result;
    }

    return {
        resolvedName, resolvedNames, i, contractName, response, publicApr, presaleApr, priceSelf,
        nftTotalSupply, chainNr, retrieveOn,
        provider,
        reset, readContract, readBigField, getNextSelfNameB, getNextSelfName,
        readContractWithChain, runReadFunction
    };
});
