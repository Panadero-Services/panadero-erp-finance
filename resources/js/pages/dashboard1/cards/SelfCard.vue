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
let selfToken=ref("SELF");


let UsdtBalanceTo = ref(0);
let UsdtBalanceFrom = ref(0);

let SelfBalanceTo = ref(0);
let SelfBalanceFrom = ref(0);


const tokenAddress = computed( ()=> {
    if (selfToken.value=="SELF") return "0x50c34995a273075a80c23625f69f40d56ce414de";
//    if (selfToken.value=="BNB") return "0x50c34995a273075a80c23625f69f40d56ce414de";
    if (selfToken.value=="USDT") return "0x55d398326f99059fF775485246999027B3197955";

});


const getWallet = async (_n) => {
    // endpoint abi contract
    const e = "https://bsc-dataseed1.binance.org/";
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

const getBalances = async () => {
    let accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });

    let _bal1 = await contract.readContract("USDT", "balanceOf", [accounts[0]]); 
    UsdtBalanceFrom.value = parseFloat(formatEther(_bal1,18)).toFixed(2);
    
    let _bal2 = await contract.readContract("SELF", "balanceOf", [accounts[0]]); 
    SelfBalanceFrom.value = parseFloat(formatEther(_bal2,18)).toFixed(2);

    const _to = await getWallet(selfTo.value); 


    let _bal3 = await contract.readContract("USDT", "balanceOf", [_to]); 
    UsdtBalanceTo.value = parseFloat(formatEther(_bal3,18)).toFixed(2);
    
    let _bal4 = await contract.readContract("SELF", "balanceOf", [_to]); 
    SelfBalanceTo.value = parseFloat(formatEther(_bal4,18)).toFixed(2);

}

const trySmthTres = async () => {
    let accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });

    const provider = new ethers.BrowserProvider(window.ethereum)
    const signer = await provider.getSigner();
    const _abi = [ "function transfer(address to, uint amount)"]
    const _contract = new Contract(tokenAddress.value, _abi, signer)

    const _amount = parseEther(selfAmount.value);
    const _to = await getWallet(selfTo.value); 

    // Begin listening for any Transfer event
    /*    contract.on("Transfer", (from, to, _amount, event) => {
            const amount = formatEther(_amount, 18)
            console.log(`${ from } => ${ to }: ${ amount }`);
        });
    */
    // Send the transaction
    if(_to.length==42){
        const _tx = await _contract.transfer(_to, _amount,  { message: 'whatever' });
        console.log("_tx",_tx)
    }
}

let achieved=ref(25);

const _getContract = async () => {
    let _big = await contract.readContract("SELF_NFT_v224",'collectedSelf');
    achieved.value = parseFloat(_big.toString().slice(0,-18));
}

const aSelf = ['mamen','12345', 'ruwaifa', 'superman', 'levinus'];

onMounted(async ()=> {
    await _getContract();
})


</script>

<template>
    <!-- SELF WideScreen Wallet Card -->
    <div v-if="names.some(r=>aSelf.includes(r))"
        class="col-span-3 md:mb-2 opacity-90 p-6 bg-white dark:bg-purple-900/70 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500">
        
        <div class="grid lg:grid-cols-8 gap-2 text-gray-800 dark:text-white">

            <div class="h-14 w-14 bg-purple-50 dark:bg-purple-800/20 flex items-center justify-center rounded-full"><ApplicationLogo /></div>

            <div class="col-span-4 -ml-8 ">
                <h2 class="mt-1 text-xl text-gray-900 dark:text-white font-semibold">Self <font class="text-green-600 font-normal">{{wallet}}</font></h2>
                <span  class='text-sm text-green-600'><font class="text-gray-800 dark:text-gray-200">MEMBER: </font> {{aSelf}}</span>
            </div>

            <div class="col-span-3 mr-4 ml-4 mt-1">

    <p @click="getBalances">  <span class="text-green-400">SENDER</span>Balance USDT: <span class="text-green-400">{{ UsdtBalanceFrom }}</span>
                        SELF: <span class="text-green-400">{{ SelfBalanceFrom }}</span>
    </p>
    <p>
        <span class="text-pink-400 uppercase">{{selfTo}}</span> Balance USDT: <span class="text-pink-400">{{ UsdtBalanceTo }}</span>
        SELF: <span class="text-pink-400">{{ SelfBalanceTo }}</span>
    </p>


            </div>

            <div></div>

            <div class="col-span-1 -ml-8 mt-2">
                <h2 class="mt-1 text-xl text-gray-900 dark:text-white font-semibold">Actions <font class="text-green-600 font-normal"></font></h2>
            </div>

            <div class="col-span-6 -mr-2 text-xs mt-4">
                <div class="grid grid-cols-4 gap-6">
                   
                    <div>
                        <span class="block">TOKEN</span>
                        <input v-model="selfToken" class="pl-4 bg-gradient-to-r from-purple-300 to-blue-200  w-48 rounded-md border-0 py-1.5 pr-2 text-black ring-2 ring-inset ring-pink-300 placeholder:text-gray-800 focus:ring-2 focus:ring-inset focus:ring-purple-800 sm:text-sm sm:leading-6" />
                    </div>

                    <div>
                        <span class="block">TO</span>
                        <input v-model="selfTo" class="pl-4 bg-gradient-to-r from-purple-300 to-blue-200  w-48 rounded-md border-0 py-1.5 pr-2 text-black ring-2 ring-inset ring-pink-300 placeholder:text-gray-800 focus:ring-2 focus:ring-inset focus:ring-purple-800 sm:text-sm sm:leading-6" />
                    </div>

                    <div >
                        <span class="block">QTY</span>
                        <input v-model="selfAmount" class="pl-4 bg-gradient-to-r from-purple-300 to-blue-200 w-24 rounded-md border-0 py-1.5 pr-2 text-black ring-2 ring-inset ring-pink-300 placeholder:text-gray-800 focus:ring-2 focus:ring-inset focus:ring-purple-800 sm:text-sm sm:leading-6" />
                    </div>
                
                    <div v-if="['SELF','USDT'].includes(selfToken)">
                        <button :disabled="selfAmount<10 || selfAmount>50001"  @click="trySmthTres">
                            <span  :class="selfAmount<10 || selfAmount>50001  ? 'text-gray-400' : 'text-purple-600 dark:text-pink-50 hover:text-green-600' " class="lg:text-lg ">SEND</span>
                        </button>
                    </div>
                
                </div>
            </div>
        </div>
    </div>

</template>