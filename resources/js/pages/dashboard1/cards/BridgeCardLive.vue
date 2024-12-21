<script setup>
import ApplicationLogo from '@/components/logoSelf.vue';
import {computed, onMounted, ref} from 'vue'

import { useContractStore } from '@/stores/contracts';
let contract = useContractStore();

import { ethers, parseEther, formatEther, Contract, keccak256, toUtf8Bytes } from 'ethers';

defineProps({
	wallet : String,
	names : String,
	on: Boolean
});

let selfAmount=ref("0");
let selfTo=ref("selfName");
let selfToken=ref("BSC_SELF");

let HolderTestnetBalance = ref(0);
let HolderethBalance = ref(0);

let HolderBscAllowance = ref(0);
let HolderEthAllowance = ref(0);

let ethBridge = ref(0);
let bscBridge = ref(0);

let EthVault = ref(0);
let BscVault = ref(0);

let chainId = ref("");
let connected = ref(false);

let bscNonce = ref(3);
let bscInonce = ref(4);

let ethNonce = ref(3);
let ethInonce = ref(4);


let bscFee = ref(0);
let ethFee = ref(0);

let bscBridgeFee = ref(0);
let bscMinBridge = ref(0);
let bscMaxBridge = ref(0);
let bscMaxSupply = ref(0);
let bscTransferredFee = ref(0);

let ethBridgeFee = ref(0);
let ethMinBridge = ref(0);
let ethMaxBridge = ref(0);
let ethMaxSupply = ref(0);
let ethTransferredFee = ref(0);

let busy = ref(false);




const getWallet = async (_n) => {
    // endpoint abi contract
    const e = "https://data-seed-prebsc-1-s1.binance.org:8545/";
    const a = [{"inputs": [{ "internalType": "uint256", "name": "tokenId", "type": "uint256" }],"name": "ownerOf","outputs": [{ "internalType": "address", "name": "", "type": "address" }],"stateMutability": "view","type": "function"}];
    const c = "0x125Bb13F77f3565d421bD22e92aaFfC795D97a72";
    const abi = [{"inputs":[{"internalType":"address","name":"_self","type":"address"}],"stateMutability":"nonpayable","type":"constructor"},{"inputs":[],"name":"AlreadyAgentError","type":"error"},{"inputs":[],"name":"ConsecutiveUnderscoreError","type":"error"},{"inputs":[],"name":"InvalidAddressError","type":"error"},{"inputs":[],"name":"InvalidCommissionError","type":"error"},{"inputs":[],"name":"NameAlreadyRegisteredError","type":"error"},{"inputs":[],"name":"NameLengthOutOfRangeError","type":"error"},{"inputs":[],"name":"NameNotReservedError","type":"error"},{"inputs":[],"name":"NameReservedError","type":"error"},{"inputs":[],"name":"NoTokensAvailableError","type":"error"},{"inputs":[],"name":"NotAgentError","type":"error"},{"inputs":[],"name":"NotNameOwnerError","type":"error"},{"inputs":[],"name":"PriceNotSet","type":"error"},{"inputs":[],"name":"ReservedWordStartError","type":"error"},{"inputs":[],"name":"UnderscoreStartError","type":"error"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"agent","type":"address"},{"indexed":false,"internalType":"uint256","name":"commission","type":"uint256"}],"name":"AgentAdded","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"agent","type":"address"}],"name":"AgentRemoved","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"agent","type":"address"},{"indexed":false,"internalType":"uint256","name":"commission","type":"uint256"}],"name":"AgentUpdated","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"approved","type":"address"},{"indexed":true,"internalType":"uint256","name":"tokenId","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"operator","type":"address"},{"indexed":false,"internalType":"bool","name":"approved","type":"bool"}],"name":"ApprovalForAll","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"receiver","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"CollectedSelfForwarded","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"uint256","name":"tokenId","type":"uint256"},{"indexed":false,"internalType":"string","name":"metadata","type":"string"}],"name":"MetadataUpdated","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":false,"internalType":"string","name":"name","type":"string"},{"indexed":false,"internalType":"uint256","name":"tokenId","type":"uint256"}],"name":"NameRegistered","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"string","name":"name","type":"string"}],"name":"NameReserved","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"string","name":"name","type":"string"}],"name":"NameUnreserved","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"account","type":"address"}],"name":"Paused","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"uint256","name":"length","type":"uint256"},{"indexed":true,"internalType":"uint256","name":"price","type":"uint256"}],"name":"PriceUpdated","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"newSelfToken","type":"address"}],"name":"SelfTokenUpdated","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":true,"internalType":"uint256","name":"tokenId","type":"uint256"}],"name":"Transfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"address","name":"account","type":"address"}],"name":"Unpaused","type":"event"},{"inputs":[{"internalType":"address","name":"_agent","type":"address"},{"internalType":"uint256","name":"_commission","type":"uint256"}],"name":"addAgent","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_to","type":"address"},{"internalType":"string","name":"_name","type":"string"}],"name":"adminRegisterName","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_to","type":"address"},{"internalType":"string","name":"_name","type":"string"}],"name":"agentRegisterName","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"agents","outputs":[{"internalType":"bool","name":"isAgent","type":"bool"},{"internalType":"uint256","name":"commission","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256","name":"tokenId","type":"uint256"}],"name":"approve","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"owner","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"string[]","name":"_names","type":"string[]"}],"name":"batchReserveNames","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"string[]","name":"_names","type":"string[]"}],"name":"batchUnreserveNames","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"collectedSelf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"_agent","type":"address"},{"internalType":"uint256","name":"_commission","type":"uint256"}],"name":"editAgent","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"forwardCollectedSelf","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"tokenId","type":"uint256"}],"name":"getApproved","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"_owner","type":"address"}],"name":"getNames","outputs":[{"internalType":"string[]","name":"names","type":"string[]"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"string","name":"_name","type":"string"}],"name":"getPrice","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"operator","type":"address"}],"name":"isApprovedForAll","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"string","name":"_name","type":"string"}],"name":"isNameAvailable","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"lengthToPrice","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"tokenId","type":"uint256"}],"name":"ownerOf","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"pause","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"paused","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"string","name":"_name","type":"string"}],"name":"registerName","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_agent","type":"address"}],"name":"removeAgent","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"renounceOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"string","name":"_name","type":"string"}],"name":"reserveName","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"string","name":"","type":"string"}],"name":"reservedNames","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"reservedWords","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"_tokenId","type":"uint256"},{"internalType":"uint256","name":"_salePrice","type":"uint256"}],"name":"royaltyInfo","outputs":[{"internalType":"address","name":"","type":"address"},{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"from","type":"address"},{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256","name":"tokenId","type":"uint256"}],"name":"safeTransferFrom","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"from","type":"address"},{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256","name":"tokenId","type":"uint256"},{"internalType":"bytes","name":"data","type":"bytes"}],"name":"safeTransferFrom","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"self","outputs":[{"internalType":"contract IERC20","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"operator","type":"address"},{"internalType":"bool","name":"approved","type":"bool"}],"name":"setApprovalForAll","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_receiver","type":"address"},{"internalType":"uint96","name":"_feeNumerator","type":"uint96"}],"name":"setDefaultRoyalty","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_tokenId","type":"uint256"},{"internalType":"string","name":"_metadata","type":"string"}],"name":"setNameMetadata","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_length","type":"uint256"},{"internalType":"uint256","name":"_price","type":"uint256"}],"name":"setPrice","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_self","type":"address"}],"name":"setSelf","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"bytes4","name":"interfaceId","type":"bytes4"}],"name":"supportsInterface","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"index","type":"uint256"}],"name":"tokenByIndex","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"tokenIdToName","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"uint256","name":"index","type":"uint256"}],"name":"tokenOfOwnerByIndex","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"tokenId","type":"uint256"}],"name":"tokenURI","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"from","type":"address"},{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256","name":"tokenId","type":"uint256"}],"name":"transferFrom","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"unpause","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"string","name":"_name","type":"string"}],"name":"unreserveName","outputs":[],"stateMutability":"nonpayable","type":"function"}];
    // getProvider
    function gp() {return new ethers.JsonRpcProvider(e);}

    // readContract
    async function rc( _c, _a, _f, _p=[]) {
        const p = gp();
        const c = new ethers.Contract(_c, _a, p);
        return await c[_f](..._p);
    }
    async function rn(_n) {return( await rc(c,abi,"ownerOf",[keccak256(toUtf8Bytes(_n))]));}

    let _wallet = await rn(_n);
    return _wallet;
}
const getApproval = async () => {
}

const getBalances = async () => {
    let accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });

    let _bal1 = await contract.readContract("BSC_SELF", "balanceOf", [accounts[0]]); 
    HolderTestnetBalance.value = parseFloat(formatEther(_bal1,18)).toFixed(2);

    let _bal2 = await contract.readContract("ETH_SELF", "balanceOf", [accounts[0]]); 
    HolderethBalance.value = parseFloat(formatEther(_bal2,18)).toFixed(2);

    let aBscBridge = '0xDf20476d57eC39379fA4eB11b5541ae0232099E5';
    let aBscVault = '0xf7BBE3DA978E5746640c891eEE73b466D798056e';

    let aEthBridge = '0x3d1d847Ebddb67DE302FfdC43FE5A68931E6b346';
    let aEthVault = '0x1e4d236e12Ae62514d7Fd52f88bb8FCEb56a9b7C';

    let _allow1 = await contract.readContract("ETH_SELF", "allowance", [accounts[0],aEthBridge]); 
    HolderEthAllowance.value = parseFloat(formatEther(_allow1,18)).toFixed(2);

    let _allow2 = await contract.readContract("BSC_SELF", "allowance", [accounts[0],aBscBridge]); 
    HolderBscAllowance.value = parseFloat(formatEther(_allow2,18)).toFixed(2);

    let _bal3 = await contract.readContract("BSC_SELF", "balanceOf",[aBscBridge]); 
    bscBridge.value = parseFloat(formatEther(_bal3,18)).toFixed(2);

    let _bal5 = await contract.readContract("BSC_SELF", "balanceOf",[aBscVault]); 
    BscVault.value = parseFloat(formatEther(_bal5,18)).toFixed(2);

    let _bal4 = await contract.readContract("ETH_SELF", "balanceOf", [aEthBridge]); 
    ethBridge.value = parseFloat(formatEther(_bal4,18)).toFixed(2);

    let _bal6 = await contract.readContract("ETH_SELF", "balanceOf",[aEthVault]); 
    EthVault.value = parseFloat(formatEther(_bal6,18)).toFixed(2);

    let _nonce1 = await contract.readContract("BSC_BRIDGE", "nonce", []); 
    bscNonce.value = _nonce1;

    let _nonce2 = await contract.readContract("BSC_BRIDGE", "inonce", []); 
    bscInonce.value = _nonce2;

    let _nonce3 = await contract.readContract("ETH_BRIDGE", "nonce", []); 
    ethNonce.value = _nonce3;

    let _nonce4 = await contract.readContract("ETH_BRIDGE", "inonce", []); 
    ethInonce.value = _nonce4;

    let _fee1 = await contract.readContract("BSC_BRIDGE", "collectedFee", []); 
    bscFee.value = parseFloat(formatEther(_fee1,18)).toFixed(2);

    let _fee2 = await contract.readContract("ETH_BRIDGE", "collectedFee", []); 
    ethFee.value = parseFloat(formatEther(_fee2,18)).toFixed(2);

    let _bscBridgeFee = await contract.readContract("BSC_BRIDGE", "bridgeFee", []); 
    bscBridgeFee.value = _bscBridgeFee;

    let _bscMinBridge = await contract.readContract("BSC_BRIDGE", "minBridge", []); 
    bscMinBridge.value = parseFloat(formatEther(_bscMinBridge,18)).toFixed(0);

    let _bscMaxBridge = await contract.readContract("BSC_BRIDGE", "maxBridge", []); 
    bscMaxBridge.value = parseFloat(formatEther(_bscMaxBridge,18)).toFixed(0);

    let _bscMaxSupply = await contract.readContract("BSC_BRIDGE", "maxSupply", []); 
    bscMaxSupply.value = parseFloat(formatEther(_bscMaxSupply,18)).toFixed(0);

    let _bscTransferredFee = await contract.readContract("BSC_BRIDGE", "transferredFee", []); 
    bscTransferredFee.value = parseFloat(formatEther(_bscTransferredFee,18)).toFixed(0);

    let _ethBridgeFee = await contract.readContract("ETH_BRIDGE", "bridgeFee", []); 
    ethBridgeFee.value =_ethBridgeFee

    let _ethMinBridge = await contract.readContract("ETH_BRIDGE", "minBridge", []); 
    ethMinBridge.value = parseFloat(formatEther(_ethMinBridge, 18)).toFixed(0);

    let _ethMaxBridge = await contract.readContract("ETH_BRIDGE", "maxBridge", []); 
    ethMaxBridge.value = parseFloat(formatEther(_ethMaxBridge, 18)).toFixed(0);

    let _ethMaxSupply = await contract.readContract("ETH_BRIDGE", "maxSupply", []); 
    ethMaxSupply.value = parseFloat(formatEther(_ethMaxSupply, 18)).toFixed(0);

    let _ethTransferredFee = await contract.readContract("ETH_BRIDGE", "transferredFee", []); 
    ethTransferredFee.value = parseFloat(formatEther(_ethTransferredFee, 18)).toFixed(0);

    /*    let _bal2 = await contract.readContract("SELF", "balanceOf", [accounts[0]]); 
        bscBridge.value = parseFloat(formatEther(_bal2,18)).toFixed(2);
        const _to = await getWallet(selfTo.value); 
        let _bal3 = await contract.readContract("USDT", "balanceOf", [_to]); 
        HolderethBalance.value = parseFloat(formatEther(_bal3,18)).toFixed(2);
        let _bal4 = await contract.readContract("SELF", "balanceOf", [_to]); 
        ethBridge.value = parseFloat(formatEther(_bal4,18)).toFixed(2);
    */

}

  const sleep = (ms) => { return new Promise(resolve => setTimeout(resolve, ms));}


const bridge = async (_qty) => {
    busy.value = true;
    let accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });

    const provider = new ethers.BrowserProvider(window.ethereum)
    const signer = await provider.getSigner();

    // abi https://eth.etherscan.io/address/0x6473f6aD3f8fdbC665be180318CaD30275f6Ff6A#code == ETH_BRIDGE
    const _abi = [{"inputs":[{"internalType":"address","name":"_selfToken","type":"address"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"admin","type":"address"}],"name":"adminUpdated","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"agent","type":"address"}],"name":"agentUpdated","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"deposited","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"feeTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"newBridgeFee","type":"uint256"}],"name":"feeUpdated","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"inonce","type":"uint256"}],"name":"iNonceUpdated","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"maxBridgeUpdated","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"maxSupplyUpdated","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"minBridgeUpdated","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"newMinFee","type":"uint256"}],"name":"minFeeUpdated","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"nonce","type":"uint256"}],"name":"nonceUpdated","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"bool","name":"flag","type":"bool"}],"name":"shutdownActivated","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"nonce","type":"uint256"},{"indexed":true,"internalType":"address","name":"holder","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"tokensInBound","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"nonce","type":"uint256"},{"indexed":true,"internalType":"address","name":"holder","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"tokensOutBound","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"vault","type":"address"}],"name":"vaultUpdated","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"}],"name":"withdrawn","type":"event"},{"inputs":[],"name":"admin","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"agent","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"bridgeFee","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"_newAdmin","type":"address"}],"name":"changeAdmin","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_newAgent","type":"address"}],"name":"changeAgent","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"collectedFee","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"deposit","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_nonce","type":"uint256"}],"name":"getOrderAmount","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"_nonce","type":"uint256"}],"name":"getOutBound","outputs":[{"internalType":"address","name":"_owner","type":"address"},{"internalType":"address","name":"_token","type":"address"},{"internalType":"uint256","name":"_amount","type":"uint256"},{"internalType":"bool","name":"_completed","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"_inonce","type":"uint256"},{"internalType":"address","name":"_to","type":"address"},{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"inBound","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"inbounds","outputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"token","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"},{"internalType":"bool","name":"completed","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"inonce","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"lastTransaction","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"maxBridge","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"maxSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"minBridge","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"minFee","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"nonce","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"outBound","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"outbounds","outputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"token","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"},{"internalType":"bool","name":"completed","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"selfDecimals","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"selfToken","outputs":[{"internalType":"contract IERC20","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"_newBridgeFee","type":"uint256"}],"name":"setBridgeFee","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_inonce","type":"uint256"}],"name":"setInonce","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"setMaxBridge","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"setMaxSupply","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"setMinBridge","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_newMinFee","type":"uint256"}],"name":"setMinFee","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"_nonce","type":"uint256"}],"name":"setNonce","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"bool","name":"_flag","type":"bool"}],"name":"setShutDownFlag","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"_newVault","type":"address"}],"name":"setVault","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"shutDownFlag","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"timerReady","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"title","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"transferFee","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"transferredFee","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"vault","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"withdraw","outputs":[],"stateMutability":"nonpayable","type":"function"}];

    let _bridgeAddress = "";
    if (connected.value && activeChain.value=='bsc') _bridgeAddress = "0xDf20476d57eC39379fA4eB11b5541ae0232099E5";
    if (connected.value && activeChain.value=='ethereum') _bridgeAddress = "0x3d1d847Ebddb67DE302FfdC43FE5A68931E6b346";


    const _contract = new Contract(_bridgeAddress, _abi, signer)
    // const _amount = parseEther(selfAmount.value);
    // const _to = await getWallet(selfTo.value); 

    // for the sake of simplicity .. one contract expects Ether (eth.. mint()_.. the other plain value (bsc.. surprice());
    let _amount = parseEther(_qty);
//    if (activeChain.value=='bsc') _amount = 5000;    


    // Send the transaction eth
    const _tx = await _contract.outBound(_amount,  { message: 'whatever' });
    console.log("_tx outbound",_tx)

    await sleep(30000);
    await getBalances();
    await sleep(30000);

    busy.value = false;
}

const approve = async () => {
    let accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });

    console.log('cut here');

    const provider = new ethers.BrowserProvider(window.ethereum)
    const signer = await provider.getSigner();


    // abi https://eth.etherscan.io/address/0x362501D7eB013356b685A7A926e7914Ba7AfC270#code == eth_SELF
    const _abi = [{"inputs":[{"internalType":"string","name":"name","type":"string"},{"internalType":"string","name":"symbol","type":"string"},{"internalType":"uint8","name":"_decimals","type":"uint8"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"spender","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"spender","type":"address"}],"name":"allowance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"approve","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"subtractedValue","type":"uint256"}],"name":"decreaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"addedValue","type":"uint256"}],"name":"increaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"mint","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"from","type":"address"},{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transferFrom","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"}, {"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"surprise","outputs":[],"stateMutability":"nonpayable","type":"function"}];
    
    let _selfAddress = "";
    if (connected.value && activeChain.value=='bsc') _selfAddress = "0x50c34995a273075a80c23625F69F40d56CE414DE";
    if (connected.value && activeChain.value=='ethereum') _selfAddress = "0x4af8aa621Df6dd3e2D653188a357FC2b35c6A037";

    let _spender = "";
    if (connected.value && activeChain.value=='bsc') _spender = "0xDf20476d57eC39379fA4eB11b5541ae0232099E5";
    if (connected.value && activeChain.value=='ethereum') _spender = "0x3d1d847Ebddb67DE302FfdC43FE5A68931E6b346";


    console.log('activeChain.value=', activeChain.value);


    const _contract = new Contract(_selfAddress, _abi, signer)
    // const _amount = parseEther(selfAmount.value);
    // const _to = await getWallet(selfTo.value); 





    // for the sake of simplicity .. one contract expects Ether (eth.. mint()_.. the other plain value (bsc.. surprice());
    let _amount = parseEther("1000000");

    // Send the transaction eth
    if(_spender.length==42){
        const _tx = await _contract.approve(_spender, _amount,  { message: 'whatever' });
        console.log("_tx eth",_tx)
    }
}

const mint = async (_qty) => {
    let accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
    const _to = accounts[0];

    const provider = new ethers.BrowserProvider(window.ethereum)
    const signer = await provider.getSigner();

    // abi https://eth.etherscan.io/address/0x362501D7eB013356b685A7A926e7914Ba7AfC270#code == eth_SELF
    const _abi = [{"inputs":[{"internalType":"string","name":"name","type":"string"},{"internalType":"string","name":"symbol","type":"string"},{"internalType":"uint8","name":"_decimals","type":"uint8"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"spender","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"spender","type":"address"}],"name":"allowance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"approve","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"subtractedValue","type":"uint256"}],"name":"decreaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"addedValue","type":"uint256"}],"name":"increaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"mint","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"from","type":"address"},{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transferFrom","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"}, {"inputs":[{"internalType":"uint256","name":"_amount","type":"uint256"}],"name":"surprise","outputs":[],"stateMutability":"nonpayable","type":"function"}];
    
    let _selfAddress = "";
    if (connected.value && activeChain.value=='bsc') _selfAddress = "0x50c34995a273075a80c23625F69F40d56CE414DE";
    if (connected.value && activeChain.value=='ethereum') _selfAddress = "0x4af8aa621Df6dd3e2D653188a357FC2b35c6A037";


    const _contract = new Contract(_selfAddress, _abi, signer)
    // const _amount = parseEther(selfAmount.value);
    // const _to = await getWallet(selfTo.value); 

    // for the sake of simplicity .. one contract expects Ether (eth.. mint()_.. the other plain value (bsc.. surprice());
    let _amount = parseEther(_qty);
    if (activeChain.value=='bsc') _amount = _qty;    


    // Send the transaction eth
    if(_to.length==42 && activeChain.value=='ethereum'){
        const _tx = await _contract.mint(_to, _amount,  { message: 'whatever' });
        console.log("_tx eth",_tx)
    }

    // Send the transaction bsc
    if(_to.length==42 && activeChain.value=='bsc'){
        const _tx = await _contract.surprise( _amount,  { message: 'whatever' });
        console.log("_tx bsc",_tx)
    }
}

let achieved=ref(25);

const _getContract = async () => {
    let _big = await contract.readContract("SELF_NFT_v224",'collectedSelf');
    achieved.value = parseFloat(_big.toString().slice(0,-18));
}

const aSelf = ['mamen','12345', 'ruwaifa', 'superman', 'levinus'];



const activeChain = computed(() => {
    if (chainId.value=='0x61') return 'bsc';
//    if (chainId.value=='0xaa36a7') return 'eth';
    if (chainId.value=='0x1') return 'ethereum';
    if (chainId.value=='0x5') return 'goerlitest';
    if (chainId.value=='0x539') return 'localhost';
    if (chainId.value=='0x89') return 'polygon';
    if (chainId.value=='0x38') return 'bsc';
    if (chainId.value=='0xf49d') return 'mtv';
    return 'empty';
});



onMounted(async ()=> {
    await _getContract();
    chainId.value = await window.ethereum.request({ method: 'eth_chainId' });
    connected.value = await window.ethereum.isConnected();

    if(connected.value) getBalances();
})



const _eth = computed(() => {
    if (chainId.value=='0x1') return 'text-pink-600 dark:text-pink-400';
    return ';'
});
const _bsc = computed(() => {
    if (chainId.value=='0x38') return ' text-green-600 dark:text-green-400';
});

const _cardClass = "shadow-xl col-span-3 md:mb-2 opacity-90 bg-white p-6 dark:bg-gray-800/70 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500";
const _button = "m-1 dark:bg-indigo-300 dark:hover:bg-white rounded-md border-indigo-600 dark:border-black border px-2 lg:px-3 py-1 lg:py-2 lg:text-sm lg:font-semibold text-black shadow-sm hover:bg-indigo-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600";
const _button2 = "m-1 dark:bg-indigo-300 dark:hover:bg-white rounded-md border-indigo-600 dark:border-black border px-2 lg:px-3 py-1 lg:py-2 lg:text-sm lg:font-semibold text-black shadow-sm hover:bg-indigo-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600";

</script>

<template>
    <!-- SELF WideScreen Wallet Card -->
    <div :class="_cardClass">
        <div class="grid grid-cols-4 lg:grid-cols-8 text-xs lg:text-sm gap-2 text-gray-900 dark:text-gray-400 p-2 lg:p-8">
           
            <div class="">
                <div class="h-6 w-6 lg:h-12 lg:w-12 flex items-center justify-center rounded-full"><ApplicationLogo />
                </div>
            </div>

            <div class="col-span-3 grid grid-cols-3 -ml-12 p-4">
                <div>
                    <p :class="_bsc" >BSC</p>
                    <p>nonce <span :class="_bsc">{{bscNonce}}</span></p>
                    <p>inonce <span :class="_bsc">{{bscInonce}}</span></p>                
                    <p>bridgeFee <span :class="_bsc">{{bscBridgeFee}}</span></p>                
                    <p>minBridge <span :class="_bsc">{{bscMinBridge}}</span></p>                
                    <p>maxBridge <span :class="_bsc">{{bscMaxBridge}}</span></p>                
                    <p>maxSupply <span :class="_bsc">{{bscMaxSupply}}</span></p>                
                    <p>transferredFee <span :class="_bsc">{{bscTransferredFee}}</span></p>  
                </div>
                <div class="col-span-2 ml-4">
                    <p class="mr-3">holder <span :class="_bsc" >{{ HolderTestnetBalance }}</span></p>
                    <p class="mr-3">bridge <span :class="_bsc" >{{ bscBridge }}</span></p>
                    <p class="mr-3">vault <span :class="_bsc" >{{ BscVault }}</span></p>
                    <p class="mr-3">allowance <span :class="_bsc" >{{ HolderBscAllowance }}</span></p>
                    <p class="mr-3">collectedFee <span :class="_bsc" >{{ bscFee }}</span></p>
                </div>
                <div class="col-span-3 ">
                    <div v-if="!busy" class="mt-2" :class="activeChain=='bsc' ? 'text-green-400 ': 'text-gray-500'">
                        <button :disabled="!(activeChain=='bsc')" type="button" @click="mint('5000')" :class="_button">mint5k</button>
                        <button :disabled="!(activeChain=='bsc')" type="button" @click="mint('10000')" :class="_button">mint10k</button>
                        <button :disabled="!(activeChain=='bsc')" type="button" @click="bridge('2000')" :class="_button">bridge 2k</button>
                        <button :disabled="!(activeChain=='bsc')" type="button" @click="bridge('10000')" :class="_button">bridge 10K</button>
                        <button :disabled="!(activeChain=='bsc')" type="button" @click="bridge('25000')" :class="_button">bridge 25K</button>
                        <button :disabled="!(activeChain=='bsc')" type="button" @click="bridge('100000')" :class="_button">bridge 100k</button>
                    </div>
                </div>
            </div>

            <div>
            </div>

            <div class="col-span-3 grid grid-cols-3 -ml-12 mt-4 lg:mt-0">
                <div>
                <p :class="activeChain=='ethereum' ? 'text-pink-400' : ''" >ethereum</p>
                <p >nonce <span :class="_eth" >{{ethNonce}}</span></p>
                <p>inonce <span :class="_eth" >{{ethInonce}}</span></p>       
                <p>bridgeFee <span :class="_eth" >{{ethBridgeFee}}</span></p>                
                <p>minBridge <span :class="_eth" >{{ethMinBridge}}</span></p>                
                <p>maxBridge <span :class="_eth" >{{ethMaxBridge}}</span></p>                
                <p>maxSupply <span :class="_eth" >{{ethMaxSupply}}</span></p>                
                <p>transferredFee <span :class="_eth" >{{ethTransferredFee}}</span></p>  
                </div>
                <div class="col-span-2 ml-4">
                <p class="mr-3"> holder <span :class="_eth" >{{ HolderethBalance }}</span></p>
                <p class="mr-3">bridge <span :class="_eth" >{{ ethBridge }}</span></p>
                <p class="mr-3">vault <span :class="_eth" >{{ EthVault }}</span></p>
                <p class="mr-3">allowance <span :class="_eth">{{ HolderEthAllowance }}</span></p>
                    <p class="mr-3">collectedFee <span :class="_eth" >{{ ethFee }}</span></p>
                </div>
                <div class="col-span-3">
                    <div v-if="!busy" class="col-span-3 mt-2" :class="activeChain=='ethereum' ? 'text-pink-400': ''">
                        <button :disabled="!(activeChain=='ethereum')" type="button" @click="mint('5000')" :class="_button">mint5k</button>
                        <button :disabled="!(activeChain=='ethereum')" type="button" @click="mint('10000')" :class="_button">mint10k</button>
                        <button :disabled="!(activeChain=='ethereum')" type="button" @click="bridge('3000')" :class="_button">bridge3k</button>
                        <button :disabled="!(activeChain=='ethereum')" type="button" @click="bridge('5000')" :class="_button">bridge5k</button>
                        <button :disabled="!(activeChain=='ethereum')" type="button" @click="bridge('25000')" :class="_button">bridge25k</button>
                        <button :disabled="!(activeChain=='ethereum')" type="button" @click="bridge('100000')" :class="_button">bridge100k</button>
                    </div>
                </div>
            </div>

            <div class="col-span-3 mt-4 lg:mt-8 grid grid-cols-3">
                <div class="text-xs"> 
                    <p>{{chainId}}</p>
                    <p>{{activeChain}}</p>
                </div>
                <div class="col-span-1">
                    <button type="button" @click="getBalances" :class="_button2">balance</button>
                    <button type="button" @click="approve" :class="_button2">approve</button>
                </div>

            <div class="text-left text-indigo-500 dark:text-green-400" v-if="busy" >working...
                <span class="relative flex h-8 w-8 ">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-8 w-8 bg-indigo-500 dark:bg-green-400"></span>
                </span>
            </div>

            </div>




        </div>
    </div>

</template>




