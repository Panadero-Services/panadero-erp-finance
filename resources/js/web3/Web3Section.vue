<script setup>
  import {computed, onMounted, onServerPrefetch, reactive, ref } from 'vue'
  import { ArrowPathIcon, CloudArrowUpIcon, FingerPrintIcon, Bars3Icon, LockClosedIcon, WalletIcon } from '@heroicons/vue/24/outline'
  import { useWalletStore } from "@/stores/WalletStore.js";
  import { useTokenStore } from "@/stores/TokenStore.js";
  import { useContractStore } from "@/stores/ContractStore.js";
  import { useWeb3DbStore } from "@/stores/Web3DbStore.js";

  import Tablet from "@/web3/Tablet.vue";
  import Web3Card from "@/web3/Web3Card.vue";

  import Web3Form from "@/web3/Web3Form.vue";
  import Web3Index from "@/web3/Web3Index.vue";
  import Web3Records from "@/web3/Web3Records.vue";

  import { useForm } from "@inertiajs/inertia-vue3";

  let form = useForm({
      title: 'title',
      content: 'content',
      user_id: 3,
      tag_id: 3,
    });

  const props = defineProps({
    filterArchived: Boolean
  });

  const wallet = useWalletStore()
  const token = useTokenStore();
  const contract = useContractStore();
  const web3Db = useWeb3DbStore();
    
  const isLoading= ref(true);

  const tokenButton = async (_name) => {
    await loadToken(_name);
  }

  const contractButton = async (_name) => {
    await loadContract(_name);
    console.log('contractButton: '+_name)
  }

  const loadContract = async (_contract) => {
    isLoading.value=true;
    console.log('loadContract: '+_contract)
    await contract.set(_contract);
    getBalanceContract(_contract, wallet.account);
    isLoading.value=false;
  }

    const getBalanceContract = async (_contract, _wallet) => {
        isLoading.value=true;
        let _bal=0;
        if (_wallet.length > 6){
          _bal = await contract.getBalance(_contract, _wallet,);
        } 
        console.log('getBalance: ', _contract, _wallet, _bal);
        isLoading.value=false;
    }

    onMounted(async ()=> {
        isLoading.value=true;
        token.initialize();
        wallet.initialize();
        contract.initialize();
        web3Db.initialize();
        isLoading.value=false;
    })
  
    ethereum.on('accountsChanged', async function (accounts) {
        isLoading.value = true;
        await wallet.fill();
        console.log('accountsChanged:', wallet.account)
        isLoading.value=false;
    });
  
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


  // form logic
  let _AddWeb3Record = async (i) => {
    // i.tag={title: i.tagTitle};
    // i.user={name: i.userName};
    //    assignments.value.push(i);  
    form =useForm(i);

    // add fields completed + archived;
    // look in web3Sstore
    // defaults false for new records

   //console.log(i);
    await web3Record_store();
   await web3Db.getRecords('Web3Record');
  }

  const web3Record_store = async () => {
      await form.post(route("web3Records.store"));
      console.log('created: !!')
  };

  const web3Record_destroy = (id) => {
        form.delete(route('web3Records.destroy', id));
  };

  const _loadPayload = async (i) => {

    console.log('_loadPayload', i)
    let _e = await web3Db.getRecord('Web3Record','id',i);
    web3Db.payload =_e;
  }

  const _loadTabletload = async (i) => {
    let _e = await web3Db.getRecord('Web3Record','id',i);
    await loadTablet(_e);
  }

  const loadTablet = async (_e) => {
      isLoading.value=true;
      console.log('loadTablet',_e);
      const _type = _e.web3type.title;

      if (_type=="token") {
        const _title = _e.title;
        console.log(_type, _title);
        await token.setFromDb(_e)
        await getBalance(_e, wallet.account);
      }

      if (_type=="contract") {
        const _title = _e.title;
        console.log(_type, _title);
        await contract.setFromDb(_e)


        // this is a working getContract function
         await getContractFunction(_e, );
      }

      isLoading.value=false;
  }

  const getContractFunction= async (_e) => {
      isLoading.value=true;

      console.log('getContractFunction');
      let _response = await contract.runReadFunction(_e, 'duration',[]);
      //let _response = await contract.runReadFunction(_e, 'totalSupply',[]);
      //let _response = await contract.runReadFunction(_e, 'stakingToken',[]);
      //let _response = await contract.runReadFunction(_e, 'owner',[]);

      isLoading.value=false;
    }



  const getBalance = async (_e, _wallet) => {
      isLoading.value=true;
      let _bal=0;
      if (_wallet.length > 6) {
          _bal = await token.getBalanceFromDb(_e, _wallet,);
      } 
      console.log('getBalance: ', _e.title, _wallet, _bal);
      isLoading.value=false;
    }


  const _Complete = async (_id, _flag, _field) => {
    let payload ={};
    payload.id = _id;
    payload.completed = _flag;
    payload.field = _field; // completed or archived
    console.log(payload);

    let _result = await web3Db.setComplete('Web3Record', payload);
    console.log('i have changed my completion Again!!', _id, _flag);
  } 
</script>

<template>

    <!-- Experimental !!
      <div class="text-blue-700 mb-3 max-h-24">
        web3DbStore.payload: {{web3Db.payload}}
      </div>
    Experimental -->

  <div class="grid grid-cols-8 gap-4">
    <web3-index :types="web3Db.types" :records="web3Db.records" scope="Activated" :archived="filterArchived" />
    <web3-form :types="web3Db.types" :projects="web3Db.projects" :chains="web3Db.chains" @add="_AddWeb3Record" />
    <web3-index :types="web3Db.types" :records="web3Db.records" scope="Finished" :archived="filterArchived" />
  </div>

  <div class="-mt-12">
    <web3-records :records="web3Db.records" :types="web3Db.types" :tag_id="3" tag_name="tagName" @loadpayload="_loadPayload" @setcomplete="_Complete" @loadtabletload="_loadTabletload" />
  </div>

<!-- Experimental !!
  <div class="m-6 dark:bg-gray-900 ml-10 grid  md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-4">
    <div>{{web3Db.projects}}</div>
    <div>{{web3Db.types}}</div>
    <div>{{web3Db.chains}}</div>
    <web3-card name="BUSD1" col="bg-indigo-600" />
    <web3-card name="SelfNft1" col="bg-red-600" />
    <web3-card name="AtpadPresale_v1.0.0" col="bg-red-600" />
    <web3-card name="BUSD1" col="bg-indigo-600" />
    <web3-card name="Swappy2" col="bg-green-600" />
    <web3-card name="Swappy1" col="bg-yellow-600" />
    <web3-card name="Atpad1" col="bg-yellow-600" />
    <web3-card name="Multivac" col="bg-indigo-600" />
    <web3-card name="stakepool_v1.0.7" col="bg-indigo-600" />
    <web3-card name="Gold1" col="bg-yellow-600" />
    <web3-card name="Self1" col="bg-green-600" />
    <button active="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 my-4 mx-1 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-green-600 hover:ring-green-800" :class="isLoading ? 'bg-indigo-400' : 'bg-indigo-600'" @click="tokenButton('Swappy1')">
      <span class="text-sm  text-gray-50 group-hover:text-white font-semibold ">Swappy1</span>
    </button>
    <button active="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 ml-4 my-4 mx-1 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-green-600 hover:ring-green-800" :class="isLoading ? 'bg-indigo-400' : 'bg-indigo-600'" @click="tokenButton('Swappy2')">
      <span class="text-sm text-gray-50 group-hover:text-white text-sm font-semibold ">Swappy2</span>
    </button>
    <button active="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 ml-4 my-4 mx-1 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-green-600 hover:ring-green-800" :class="isLoading ? 'bg-indigo-400' : 'bg-indigo-600'" @click="tokenButton('Multivac1')">
      <span class="text-sm text-gray-50 group-hover:text-white text-sm font-semibold ">Multivac</span>
    </button>
    <button active="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 ml-4 my-4 mx-1 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-green-600 hover:ring-green-800" :class="isLoading ? 'bg-indigo-400' : 'bg-indigo-600'" @click="tokenButton('BUSD1')">
      <span class="text-sm text-gray-50 group-hover:text-white text-sm font-semibold ">BUSD1</span>
    </button>
    <button active="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 ml-4 my-4 mx-1 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-green-600 hover:ring-green-800" :class="isLoading ? 'bg-indigo-400' : 'bg-indigo-600'" @click="tokenButton('Gold1')">
      <span class="text-sm text-gray-50 group-hover:text-white text-sm font-semibold ">Gold1</span>
    </button>
    <button active="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 ml-4 my-4 mx-1 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-green-600 hover:ring-green-800" :class="isLoading ? 'bg-indigo-400' : 'bg-indigo-600'" @click="tokenButton('Atpad')">
      <span class="text-sm text-gray-50 group-hover:text-white text-sm font-semibold ">Atpad</span>
    </button>
    <button active="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 ml-4 my-4 mx-1 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-green-600 hover:ring-green-800" :class="isLoading ? 'bg-indigo-400' : 'bg-indigo-600'" @click="tokenButton('Self1')">
      <span class="text-sm text-gray-50 group-hover:text-white text-sm font-semibold ">Self1</span>
    </button>
    <button active="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 ml-4 my-4 mx-1 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-green-600 hover:ring-green-800" :class="isLoading ? 'bg-indigo-400' : 'bg-indigo-600'" @click="tokenButton('Self2')">
      <span class="text-sm text-gray-50 group-hover:text-white text-sm font-semibold ">Self2</span>
    </button>
        <button active="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 ml-4 my-4 mx-1 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-green-600 hover:ring-green-800" :class="isLoading ? 'bg-indigo-400' : 'bg-indigo-600'" @click="tokenButton('SelfNft1')">
      <span class="text-sm text-gray-50 group-hover:text-white text-sm font-semibold ">SelfNft1</span>
    </button>
    <button active="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 ml-4 my-4 mx-1 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-yellow-600 hover:ring-green-800" :class="isLoading ? 'bg-indigo-400' : 'bg-indigo-600'" @click="contractButton('AtpadPresale_v1.1.1')">
      <span class="text-sm text-gray-50 group-hover:text-white text-sm font-semibold ">AtpadPresale_v1.1.1</span>
    </button>
    <button active="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 ml-4 my-4 mx-1 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-yellow-600 hover:ring-green-800" :class="isLoading ? 'bg-indigo-400' : 'bg-indigo-600'" @click="contractButton('AtpadPresale_v1.0.0')">
      <span class="text-sm text-gray-50 group-hover:text-white text-sm font-semibold ">AtpadPresale_v1.0.0</span>
    </button>
    <button active="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 ml-4 my-4 mx-1 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-yellow-600 hover:ring-green-800" :class="isLoading ? 'bg-indigo-400' : 'bg-indigo-600'" @click="contractButton('stakepool_v1.0.7')">
      <span class="text-sm text-gray-50 group-hover:text-white text-sm font-semibold ">stakepool_v1.0.7</span>
    </button>
  </div>
FooterData Components-->

        
    <div class="grid grid-cols-8 gap-4 mt-4">

<!-- 
      <div class="col-span-8 xl:col-span-4 mt-2 ">
        <Tablet :store="wallet" :min="5" :max="10" color="text-indigo-800" :description="describeWallet" :isLoading="isLoading" @reset="wallet.reset('')" @init="wallet.initialize('')" @balance="wallet.setBalances(token.balances)"></Tablet>
      </div>
-->

      <div class="col-span-8 xl:col-span-4 mt-2 ">
        <Tablet :store="token" :min="5" :max="14" color="text-green-800" :description="describeWallet" :isLoading="isLoading"  @reset="token.reset('')" @init="token.initialize('')"></Tablet>
      </div>
      <div class="col-span-8 xl:col-span-4 mt-2 ">
        <Tablet :store="contract" :min="5" :max="14" color="text-yellow-900" :description="describeWallet" :isLoading="isLoading"></Tablet>
      </div>

    </div>


</template>

<style type="text/css">


</style>