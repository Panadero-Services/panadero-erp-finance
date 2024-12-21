<script setup>
    import {computed, onMounted, onServerPrefetch, reactive, ref } from 'vue'

    import { useWalletStore } from "@/stores/WalletStore.js";
    const walletStore = useWalletStore()
    import { useTokenStore } from "@/stores/TokenStore.js";

    import  Tablet  from "@/Web3/Tablet.vue";

    const token = useTokenStore();
    token.initialize();

    const isLoading= ref(true);
    const tokenButton = async (_name) => {

      await loadToken(_name);
    }

    let wallet = walletStore;

    const loadToken = async (_name) => {
      console.log('loadToken: '+_name)
      await token.set(_name);
      let _asset = await token.getBalance(wallet.account);
      await wallet.setBalance(_asset);
      console.log('asset: '+_asset.name+' balance:'+_asset.balance);
    }

    onMounted(async ()=> {
       // token.set('Swappy1');
      //  await loadToken();
        isLoading.value=false;
    })
  
  import { ArrowPathIcon, CloudArrowUpIcon, FingerPrintIcon, Bars3Icon, LockClosedIcon, WalletIcon } from '@heroicons/vue/24/outline'


const features=  
 [
  {
    name: 'Wallet Store',
    description:
      'chain_id, Address.', 
      icon: WalletIcon,
  },
  {
    name: 'Token Store',
    description:
      'chain_id, name, type, address, decimals, abi.',
      icon: CloudArrowUpIcon,
  },
  {
    name: 'Contract Store',
    description:
      'chain_id, type, address',
      icon: Bars3Icon,
  },
  {
    name: 'Chain Store',
    description:
      'chain_id, name, provider, block',
    icon: FingerPrintIcon,
  },
  ];


const describeWallet =  {
    name: 'Wallet Store',
    description:
      'chain_id, Address.', 
      icon: WalletIcon,
  };

</script>

<template>

<div class="px-1 lg:px-0 lg:pt-1">
<div class="ml-10 max-w-7xl">
<div class="max-w-lg">

<div class="mt-1">
<p class="text-xs lg:text-lg font-semibold leading-2 text-green-600">The Hardhat Quest</p>
</div>
<h1 class="mt-6 text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-300 xl:text-5xl">Web3 Dashboard</h1>
<p class="my-6 text-xl leading-8 text-gray-600">Lets connect to  wallet.. token... contract and chain</p>

<div class="mt-2 flex items-center gap-x-6">

</div>
</div>
</div>
</div>

  <div class="m-6 dark:bg-gray-900 ml-10">
    <button v-if="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 my-4 mx-1 bg-indigo-600 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-green-600 hover:ring-green-800" @click="tokenButton('Swappy1')">
      <span class="text-sm  text-gray-50 group-hover:text-white font-semibold ">Swappy1</span>
    </button>

    <button v-if="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 ml-4 my-4 mx-1 bg-indigo-600 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-green-600 hover:ring-green-800" @click="tokenButton('Swappy2')">
      <span class="text-sm text-gray-50 group-hover:text-white text-sm font-semibold ">Swappy2</span>
    </button>
    <button v-if="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 ml-4 my-4 mx-1 bg-indigo-600 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-green-600 hover:ring-green-800" @click="tokenButton('Multivac1')">
      <span class="text-sm text-gray-50 group-hover:text-white text-sm font-semibold ">Multivac</span>
    </button>
    <button v-if="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 ml-4 my-4 mx-1 bg-indigo-600 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-green-600 hover:ring-green-800" @click="tokenButton('BUSD1')">
      <span class="text-sm text-gray-50 group-hover:text-white text-sm font-semibold ">BUSD1</span>
    </button>
    <button v-if="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 ml-4 my-4 mx-1 bg-indigo-600 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-green-600 hover:ring-green-800" @click="tokenButton('Gold1')">
      <span class="text-sm text-gray-50 group-hover:text-white text-sm font-semibold ">Gold1</span>
    </button>
  </div>
        
<div class="relative bg-white dark:bg-gray-900 -m-6 mb-8">
  <div class="ml-12 isolate overflow-hidden bg-gradient-to-b from-indigo-100 dark:from-gray-700/20">
    <div class="grid grid-cols-4 m-4">
      <div class="col-span-4 lg:col-span-2 2xl:col-span-1">
        <Tablet :store="wallet" min="5" max="10" color="text-indigo-400" :description="describeWallet"></Tablet>
      </div>
      <div class="col-span-4 lg:col-span-2 2xl:col-span-1">
        <Tablet :store="token" min="5" max="12" color="text-green-400" :description="describeWallet"></Tablet>
      </div>

      <div class="col-span-4 lg:col-span-2 2xl:col-span-1">
        <Tablet :store="token" min="5" max="12" color="text-yellow-400" :description="describeWallet"></Tablet>
      </div>
      <div class="col-span-4 lg:col-span-2 2xl:col-span-1">
        <Tablet :store="token" min="5" max="12" color="text-red-400" :description="describeWallet"></Tablet>
      </div>
    </div>
    <div class="absolute inset-x-0 bottom-0 -z-10 h-24 bg-gradient-to-t dark:from-white dark:from-indigo-700 sm:h-80">
    </div> 
  </div> 
</div>












</template>

<style type="text/css">


</style>