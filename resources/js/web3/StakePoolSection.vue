<script setup>

import {computed, onMounted, ref} from 'vue'
import {Interface} from 'ethers';
import { useWalletStore } from "@/stores/WalletStore.js";
import { useTokenStore } from "@/stores/TokenStore.js";
import { useContractStore } from "@/stores/ContractStore.js";
import { useWeb3DbStore } from "@/stores/Web3DbStore.js";
import { ethers } from 'ethers';
import ContractParameter from "@/web3/ContractParameter.vue";
import ContractLabel from "@/web3/ContractLabel.vue";
import ToolTip from "@/web3/ToolTip.vue";
import { WalletIcon, XMarkIcon, EyeIcon, StopIcon } from '@heroicons/vue/24/solid'

import { storeToRefs } from "pinia";
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'
import EditContractParameterModal from "@/customComponents/EditContractParameterModal.vue";

const wallet = useWalletStore()
const token = useTokenStore();
const contract = useContractStore();
const web3Db = useWeb3DbStore();

//let parameter1 = ref('');
//let parameter2 = ref('');
//let parameter3 = ref('');
//let contractNr = ref(0);
//let nonR = 1323;
//let duration = ref('_');
//let totalSupply = ref('_');

// variables for modal - edit
let _editContractParameterActive= ref(false);
let _editLine = ref([]);
let _editContract = ref([]);

let stats = ref([]);
let cstats = ref([]);
let web3Record = ref([]);
let nextLineNr = ref(0);

let toggle = ref(false);
let autoMode = ref(false);
let abiHuman = ref([]);
let json = ref([]);
let parameters = ref(0);

let parameter = ref([]);


let selected = ref(0);

let account = '0xBC5f63270c24FD2CC74A76E324A1810862D7ee57';

// counter is used for the autoLoop
let counter = ref(0);

// to distinguish the label / line per pcontact
let contractCounter = ref(0);

// show all buttons on labels
let maintenanceMode = ref(false);

// resolve selfNames
let autoNameResolve = ref(false);

// eventHandlers
let eventHandlers = ref(false);

// advancedMode
let advancedMode = ref(true);

// tooltipdMode
let tooltipMode = ref(false);

// hotLoading
let hotLoading = ref(false);

onMounted(async () => {
    wallet.initialize();
    token.initialize();
    contract.initialize();
    web3Db.setDebugMode(true);
    web3Db.initialize();
    //web3Db.setDebugMode(false);
})

  // called by ListBox
  const hotLoadingFields = (_id) => {
    if (hotLoading.value) loadContract(_id, false);
  }

  // load contract from 
  const loadContract = async (_id, _push=false) => {
    // load record from database
    web3Record.value = await web3Db.getRecord('Web3Record', 'id', _id);

    // increase contractCounter
    contractCounter.value++;

    // convert the abi
    abiHuman.value = await convertAbi(web3Record.value.abi);

    // process the json
    json.value = await convertJson(web3Record.value.json);

    // do for each view function
    nextLineNr.value = 1;
    abiHumanView.value.forEach(async (x) => {
        const _f = resolveFunctionName(x);
        await resolveLine(web3Record.value, nextLineNr.value++, _f, x, _push);
    });
    if(_push) await resolveContractIndex(web3Record.value, nextLineNr.value);
  }

    const abiHumanView = computed (() => {
        return abiHuman.value.filter( (x) => { return x.includes(' view ');
      });
    });

  // load line from db or create new record 
  const resolveLine = async(_web3Record, _nr, _f,  _x, _push, _fromButton=0) => {

    let _payload = {
      'web3record_id' :  _web3Record.id,
      'line' : _f,
      'line_nr' : _nr
    }      
    let line = await web3Db.getLine('Web3RecordLine', _payload);

    line.address = _web3Record.address;
    line.chainNr = _web3Record.web3chain.chain_nr;
    line.contractAbi = _web3Record.abi;
    line.contractCounter = contractCounter.value;

    // create a new record if not found
    if (line.id===undefined){
        await web3Db.createNewLine( web3Record.value.id, _f, _x, _nr);
    } else {

      // line parameter1 with parameter1.value;
      for (let i=0;i<2;i++){
        if(parameters.value>=i) line.parameters = line.parameters.replace('parameter'+(i+1), parameter.value[i]);
      }

      // if(parameters.value>=0) line.parameters = line.parameters.replace('parameter1', parameter.value[0]);
      // if(parameters.value>=1) line.parameters = line.parameters.replace('parameter2', parameter.value[1]);
      //  if(parameters.value>=2) line.parameters = line.parameters.replace('parameter3', parameter.value[2]);
      console.log(line.parameters);

      if(_fromButton) line.line_nr = nextLineNr.value++;
      console.log(line);

      if(_push) stats.value.push(line);
    }
  }

  // load line from db or create new record 
  const resolveLineFromButton = async(_web3Record, _nr, _f,  _x, _push) => {

    let _payload = {
      'web3record_id' :  _web3Record.id,
      'line' : _f,
      'line_nr' : _nr
    }      
    let line = await web3Db.getLine('Web3RecordLine', _payload);

    line.address = _web3Record.address;
    line.chainNr = _web3Record.web3chain.chain_nr;
    line.contractAbi = _web3Record.abi;
    line.contractCounter = contractCounter.value;

    line._f =_f;
    line._nr = _nr;
    line._web3RecordId = _web3Record.id;   

    console.log(line);
    if(_push) stats.value.push(line);
  }

// load line from db or create new record 
const resolveContractIndex = async(_web3Record, _nextNr) => {
    cstats.value.push({'id':web3Record.value.id, 'title':web3Record.value.title, 'contractCounter' :contractCounter.value, 'nextNr' : _nextNr });
}

// convert ABI to per line function
const convertAbi = (_abi) => {
  // convert using ethers.js
  const iface = new Interface(_abi);
  // convert human format
  return iface.format(false);
}

function isJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

  // convert Json to parameters
  const convertJson = (_json) => {
    parameters.value = 0 ;
    for (let i=0;i<2;i++){parameter.value[i]='';}
    if (!isJsonString(_json)) return;
    const obj = JSON.parse(_json);
    for (let i=0;i<2;i++){
      let p = 'parameter'+(i+1);
      if (obj.hasOwnProperty(p)){ 
        parameter.value[i] = obj[p];
        parameters.value = i+1;
      }
    }
    console.log('json', _json, obj);
  }

  // resolve only the function name out of ABI line
  const resolveFunctionName = (_item) => {
    _item = _item.replace("function ", "")
    _item = _item.substring(0,_item.indexOf('('));
    return _item;
  }

  // create a new line in the db
  // resets stats[] to empty
  const reset = async () => {
    abiHuman.value=[];
    stats.value=[];
    cstats.value=[];
    await web3Db.getRecords('Web3Record');
  }

  // remove line with (contractCounter == _contractCounter && contractNr) == _contractNr; from memory
  function deleteLineFromStats(arr, _contractCounter, _lineNr) {
    return arr.filter((obj) => !(obj.contractCounter == _contractCounter && obj.line_nr == _lineNr));
  }
  let _deleteStat = (_contractCounter, _lineNr) => {
    const newArr = deleteLineFromStats(stats.value, _contractCounter, _lineNr);
    stats.value = newArr;
  }

  let _editStat = (_statindex) => {
      console.log('edit',  _statindex, stats.value[_statindex]);
      _editLine.value = stats.value[_statindex];
//      _editContract.value = cstats.value[stats.value[_statindex].contractCounter];
      _editContract.value = cstats.value[stats.value[_statindex].contractCounter-1];
      _editContractParameterActive.value = true;
  }

  let _viewStat = (_statindex) => {
      console.log('view', _statindex, stats.value[_statindex]);
  }

  function removeObjectWithContractCounterId(arr, _contractCounter) {
    return arr.filter((obj) => obj.contractCounter !== _contractCounter);
  }

  let _deleteCstat = (_contractCounter) => {
    // remove contract with contractCounter from memory
    const newArr = removeObjectWithContractCounterId(cstats.value, _contractCounter);
    cstats.value = newArr;
    // remove all lines from memory with this contractCounter
    const newArr2 = removeObjectWithContractCounterId(stats.value, _contractCounter);
    stats.value = newArr2;
  }

const getTimeRemaining = (_e, _s) => {
  const countDown = _e > _s;
  const t = (countDown) ? _e - _s : _s - _e;
  const s = Math.floor((t)%60);
  const m = Math.floor((t/60)%60);
  const h = Math.floor((t/(60*60))% 24);
  const d = Math.floor(t/(60*60*24));
  return {t, countDown, d, h, m, s };
}

const retrieve = async (_contractCounter, _lineNr) => {
  const i = stats.value.findIndex(x => (x.contractCounter === _contractCounter && x.line_nr === _lineNr));

  //  let _response = await contract.runReadFunction(web3Record.value, _f,params);
  let line = stats.value[i];
  let result = await contract.runReadFunction(line);

  if(['address','text'].includes(line.type))  line.value= result;
  if(['int'].includes(line.type)) line.value=result.toString();
  if(['percentage'].includes(line.type)) line.value= ((parseFloat(result.toString())/100).toFixed(2))+'%';


  let _convert=0;
  if (line.type=='bigInt')    { _convert = Number(BigInt(result)/ 1000_000_000_000_000_000n); line.value= _convert;}
  if (line.type=='bigInt_3n') { _convert = Number(BigInt(result)/ 1000_000_000_000_000n); line.value= _convert+'.div(3e)';}
  if (line.type=='bigInt_6n') { _convert = Number(BigInt(result)/ 1000_000_000_000n); line.value= _convert+'.div(6e)';}
  if (line.type=='bigInt_9n') { _convert = Number(BigInt(result)/ 1000_000_000n); line.value= _convert+'.div(9e)';}
  if (line.type=='bigInt_12n'){ _convert = Number(BigInt(result)/ 1000_000n); line.value= _convert+'.div(12e)';}
  if (line.type=='bigInt_15n'){ _convert = Number(BigInt(result)/ 1000n); line.value= _convert+'.div(15e)';}

  if (line.type=='array') line.value= result.toString();

  if(['finishAt','updatedAt'].includes(line.line)) {
    let val = (line.value);
    let now = Math.floor(Date.now()/1000);
    let t = getTimeRemaining(val, now); 
    line.detail= `${t.d>0?t.d+'d':''} ${t.h>0?t.h+'h':''} ${t.m}m ${t.s}s`;
  }
    console.log(line);
}

  const getContractFunction= async ( _f) => {
    console.log('getContractFunction');
    let _response = await contract.runReadFunction(web3Record.value, _f,params);
    return _response;
  }

  const _loopTimer = async () => {
    setInterval(() => {
      if (autoMode.value){
        toggle.value = !toggle.value;
        counter.value++; 
        if (counter.value >= stats.value.length) counter.value=0;
        while ((!stats.value[counter.value].is_live))counter.value++;
        if(counter.value >= stats.value.length)counter.value=0;
        if (stats.value[counter.value].is_live) retrieve(stats.value[counter.value].contractCounter, stats.value[counter.value].line_nr);      
      }
    }, 4000)
  }
  _loopTimer();

  const _parameters = () => { parameters.value++; if (parameters.value>3) parameters.value=0;}

  const main1 = {"title" : "Select a contract first","detail":"Use Contract Selector.. then click button: 'Get Contract'", "step":"Step 1 of 4"};
  const main2 = {"title" : "Then select the extra labels to monitor", "detail":"Now you can also select a 2nd Contract to show data in RealTime!", "step":"Step 2 of 4"};
  const main3 = {"title" : "Now you can see all variables!", "detail":"RealTime tags will be updated based on your settings", "step":"Step 3 of 4"};

</script>

<template>

  <div v-if="_editContractParameterActive" class="col-span-5 p-2"  > 
      <edit-contract-parameter-modal :line="_editLine" :contract="_editContract" :maintenanceMode="maintenanceMode" @close="_editContractParameterActive=false" /> 
  </div>

  <!--... Contracts Header Section -->
  <div class="grid grid-cols-1 l:grid-cols-2 2xl:grid-cols-4 text-right place-content-end my-6 ">
    <div class="text-3xl font-semibold text-left ml-4">Contracts</div>

    <div >
    </div>

    <div>
      <p v-if= "tooltipMode" class="mr-40 mt-8">
        <tool-tip v-if="contractCounter==0" :type="1" :main="main1"> </tool-tip>
        <tool-tip v-if="contractCounter==1" :type="2" :main="main2"> </tool-tip>
        <tool-tip v-if="contractCounter==2" :type="3" :main="main3"> </tool-tip>
      </p>
    </div>

    <div class="">
      <button type="button" @click="loadContract(selected.id, true)"  class="rounded-md bg-white px-3 py-2 xl:px-3.5 xl:py-2.5 text-xs xl:text-sm font-semibold text-indigo-700 shadow-sm ring-1 ring-inset ring-indigo-400 hover:bg-indigo-50 ml-2">Get Contract</button>
      <button type="button" @click="reset" class="rounded-md bg-white px-3 py-2 xl:px-3.5 xl:py-2.5 text-xs xl:text-sm font-semibold text-indigo-700 shadow-sm ring-1 ring-inset ring-indigo-400 hover:bg-indigo-50 ml-2">Reset</button>
    </div>

    <div class="" />
    <div class="" />

    <div class="flex text-left">
    </div>

    <div class="">
    </div>
  </div>


  <!--... Contracts Main Section -->


  <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-5 text-gray-700 font-semibold  min-h-[570px]">

    <div class="col-span-4 ml-4  border-t-2">
      <dl class="mt-5 grid grid-cols-3 gap-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 2xl:grid-cols-9">
        <contract-parameter v-for="(stat, index) in stats" 
                :stat="stat" 
                :statIndex="index"
                :active="counter==index" 
                :maintenanceMode="maintenanceMode"
                :advancedMode="advancedMode" 
                @retrieve="retrieve" 
                @delete="_deleteStat" 
                @view="_viewStat" 
                @edit="_editStat"/>
      </dl>
    </div>

  <!--... Contracts Right Side Section -->
    <div class="px-1 lg:px-0 lg:pt-1 col-span-1 mr-8 ml-12 border-t-2">


  <!--... Contracts SELECTOR -->
      <div class="text-xs text-gray-700 font-semibold my-3">
        <span class=" leading-2 ml-0 mb-3">CONTRACT SELECTOR {{contractCounter}}:{{counter}}</span>
      </div>
      <div class="">
        <Listbox as="div" v-model="selected" class="relative mt-0 w-80 ml-1" @click="hotLoadingFields(selected.id)">
          <ListboxButton class="relative cursor-default rounded-md bg-white px-3 py-2 xl:px-3.5 xl:py-2.5 text-xs xl:text-sm font-semibold text-indigo-700  w-full shadow-sm ring-1 ring-inset ring-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-700 sm:text-sm sm:leading-6">
            <span class="flex items-center">
              <span :aria-label="selected.is_live ? 'Online' : 'Offline'" :class="[selected.is_live ? 'bg-green-400' : 'bg-gray-200', 'inline-block h-2 w-2 flex-shrink-0 rounded-full']" />
              <span class="ml-3 block truncate">{{ selected.title }}</span>
            </span>
            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
              <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
            </span>
          </ListboxButton>
          <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <ListboxOptions class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
              <ListboxOption as="template" v-for="webrecord in web3Db.records" :key="webrecord.id" :value="webrecord" v-slot="{ active, selected }"  >
                <li :class="[active ? 'bg-indigo-700 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                  <div class="flex items-center">
                    <span :class="[webrecord.is_live ? 'bg-green-400' : 'bg-gray-200', 'inline-block h-2 w-2 flex-shrink-0 rounded-full']" aria-hidden="true" />
                    <span :class="[selected ? 'font-semibold' : 'font-normal', 'ml-3 block truncate']">
                      {{ webrecord.title }}
                      <span class="sr-only"> is {{ webrecord.is_live ? 'is_live' : 'offline' }}</span>
                    </span>
                  </div>

                  <span v-if="selected" :class="[active ? 'text-white' : 'text-indigo-700', 'absolute inset-y-0 right-0 flex items-center pr-4']">
                    <CheckIcon class="h-5 w-5" aria-hidden="true" />
                  </span>
                </li>
              </ListboxOption>
            </ListboxOptions>
          </transition>
        </Listbox>
      </div>
      <div class="max-w-lg">
        <div class="text-xs text-gray-700 font-semibold ">
          <div class="mt-2">
          </div>
        </div>
        <div class="text-xs text-gray-700 font-semibold">
          <span v-for="(r, index) in abiHumanView">
              <button @click="resolveLine(web3Record,index+1,resolveFunctionName(r),r,1,1)" class="flex-none rounded-md bg-gray-50 pr-2 py-2 m-1 text-xs text-gray-700 shadow-md hover:bg-indigo-100 hover:text-indigo-700">{{index}}{{resolveFunctionName(r)}}</button>
          </span>
        </div>
      </div>

  <!--... PARAMETERS -->
        <div class="text-xs text-gray-700 font-semibold my-3 border-t-2 mt-6">
          <div @click="_parameters" class="mt-4 ml-1">
            PARAMETERS {{parameters}}
          </div>
        </div>

        <form class="my-1 max-w-md">
          <div>
            <input v-if="parameters>0" v-model="parameter[0]" class="ml-1 mt-1 flex-auto rounded-md border-0 px-1 py-2 text-gray-900  placeholder:text-gray-400 sm:text-xs sm:leading-1 w-60 xl:w-80" placeholder="Parameter1" />
          </div>
          <div>
            <input v-if="parameters>1" v-model="parameter[1]" class="ml-1 mt-2 flex-auto rounded-md border-0 px-1 py-2 text-gray-900  placeholder:text-gray-400 sm:text-xs sm:leading-1 w-60 xl:w-80" placeholder="Parameter2" />
          </div>
          <div>
            <input v-if="parameters>2" v-model="parameter[2]" class="ml-1 mt-2 flex-auto rounded-md border-0 px-1 py-2 text-gray-900  placeholder:text-gray-400 sm:text-xs sm:leading-1 w-60  xl:w-80" placeholder="Parameter3" />
          </div>
        </form>

    </div>
  </div>


  <!--... Contracts Footer Section -->

  <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-5 text-gray-700 font-semibold  min-h-[170px]">

  <div class="col-span-4 ml-4 mt-6 min-h-[120px] border-t-2" >
    <div class="mt-4">

        <contract-label v-for="(cstat, index) in cstats" 
                :cstat="cstat" 
                :active="counter==index" 
                :maintenanceMode="maintenanceMode" 
                :advancedMode="advancedMode" 
                @retrieve="retrieve" 
                @delete="_deleteCstat" />

    </div>
    <div class="text-left mt-6 ml-4 flex">

      <span class="text-xs text-gray-700 font-semibold mb-3">SELF AUTONAME RESOLVE</span>
      <input type="checkbox" v-model="autoNameResolve" class="mx-2">

      <span class="text-xs text-gray-700 font-semibold mb-2 ml-3">EVENTHANDLERS</span>
      <input type="checkbox" v-model="eventHandlers" class="mx-2">

      <span class="text-xs text-gray-700 font-semibold mb-2 ml-3">ADVANCED MODE</span>
      <input type="checkbox" v-model="advancedMode" class="mx-2">

      <span class="text-xs text-gray-700 font-semibold mb-2 ml-3">MAINTENANCE MODE</span>
      <input type="checkbox" v-model="maintenanceMode" class="mx-2">

      <span class="text-xs text-gray-700 font-semibold mb-2 ml-3">TOOLTIPS</span>
      <input type="checkbox" v-model="tooltipMode" class="mx-2">

      <span class="text-xs text-gray-700 font-semibold mb-2 ml-3">HOTLOADING</span>
      <input type="checkbox" v-model="hotLoading" class="mx-2">

    </div>

      <div class="text-left mt-3 ml-4 flex">
        <span class="text-xs text-gray-700 font-semibold mb-2">STATE AGENT</span>
        <input type="checkbox" v-model="autoMode" class="mx-2">
        <span v-for="i in 10" :key="i">
        <StopIcon v-if="autoMode" class="h-6 w-6 -ml-1 -mt-1" aria-hidden="false" :class="counter%10==i ? 'text-green-600' : 'text-gray-200'"/>
      </span>
      </div>

  </div>
  </div>

</template>

<style type="text/css">


</style>