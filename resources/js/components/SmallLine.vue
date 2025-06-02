<script setup>
import { ref, computed } from 'vue';

import { StopIcon, WrenchIcon, EyeIcon } from '@heroicons/vue/24/solid'


// call parameters
const props = defineProps({
    id: Number,
    comp: Object,
    pulse: Boolean,
    title: String,
    detail: String,
    makeup: String,
    status: String,
    mainCard: String,
    oneLine: Boolean, 
    inputFound: Boolean, 
    initialized: Boolean, 
    autoMode: Boolean
});

// define emits
const emit = defineEmits(['button1', 'button2','kill', 'eachKey', 'wrench']);

//const _title = ref(props.title);
const _inputTxt = ref(props.title);

const _style =" h-10 sm:h-12 lg:h-14 ";
const _txt1 = " h-4 sm:h-6 lg:h-8 text-xxs sm:text-xs md:text-sm font-medium text-gray-900 hover:text-indigo-700 border-0 ";
const _txt2 = " -ml-4 h-4 sm:h-5 lg:h-6 text-xxxs sm:text-xxs md:text-xxs truncate text-gray-500 ";
const _txt2_multiLine = " ml-24 overflow-scroll h-8 md:h-10 lg:h-12 -mt-6 lg:-mt-8 h-4 sm:h-5 text-xxxs sm:text-xxs md:text-xxs text-gray-500 leading-none pt-0.5";

const _bgGood = "bg-green-600 hover:bg-green-500"
const _bgPause = "bg-yellow-500 hover:bg-green-500";
const _bgError = "bg-red-600 hover:bg-green-500";
const _bgDefault = "bg-slate-300 hover:bg-green-500";

const _icon1 = computed(()=>{
  if(props.inputFound) return _bgGood;
  if(props.status==="pause") return _bgPause ;
  if(props.status==="error") return _bgError;
  return _bgDefault;
});

const _icon2 = computed(()=>{
  if(props.autoMode) return _bgGood;
  if(props.status==="pause") return _bgPause ;
  if(props.status==="error") return _bgError;
  return _bgDefault;
});




</script>

<template>
    <div :class="makeup + mainCard +_style">
      <div class="md:-mt-1">
        <img @click="$emit('button1',_inputTxt)" class="m-1 h-3 w-3 sm:h-4 sm:w-4 lg:h-5 lg:w-5 rounded" :class="_icon1" alt="" />
        <img v-if="initialized" @click="$emit('button2',_inputTxt)" class="m-1 h-3 w-3 sm:h-4 sm:w-4 lg:h-5 lg:w-5 rounded" :class="_icon2" alt="" />
      </div>
      <div class="min-w-0 flex-1">
          <div :class="makeup">
            <input @keyup="$emit('eachKey',_inputTxt)" type="text" v-model="_inputTxt" :class="makeup+_txt1" />
             <span class="absolute left-8 bottom-1 lg:left-12 md:left-10 md:bottom-2 lg: lg:bottom-3 text-xxxs text-gray-500">{{status}}</span>

          </div>
          <p v-html="detail":class="oneLine ? _txt2 : _txt2_multiLine"></p>
      </div>
        <div @click="$emit('kill',id)" class="flex p-1 text-slate-400 text-xxxs hover:text-slate-600">
              X
        </div>

        <WrenchIcon v-if="!autoMode" @click="$emit('wrench',comp)" class="absolute top-7 right-1.5  h-3 w-3 text-xxxs" aria-hidden="false" :class=" (initialized && !autoMode) ? 'text-slate-400  hover:text-slate-600' : 'text-slate-200'" />

    </div>
</template>