<script setup>
    import Panel from "@/CustomComponents/Panel.vue";
    import { WalletIcon, XMarkIcon, EyeIcon, StopIcon } from '@heroicons/vue/24/outline'

    import { ref, computed, reactive } from 'vue';

    const props = defineProps({
        e: Object,
        col: String,
        toggleAble: {type: Boolean, default: true}
    });

    const close = (id) => {
        alert('closed!'+ id);
    }

    const c = computed (() => {
        let _class = "text-black";
        if (props.e.web3type.title =="token") _class = 'bg-teal-700';
        if (props.e.web3type.title =="contract") _class = 'bg-indigo-700';
        if (props.e.web3type.title =="wallet") _class = 'bg-pink-700';
        if (props.e.web3type.title =="chain") _class = 'bg-yellow-700';
        return _class;
    });

    const bgColor = computed (() => {
        let _bgColor = 'bg-white';
        if (props.e.completed) _bgColor = 'bg-gray-400';
        return _bgColor;
    });

    const bgTagColor = computed ((color) => {
        return "text-black bg-lime-400";
    });

    const imageUrl = '/CryptoLogos/PNG/bsctestnet.png';
    /*
        $table->bigIncrements('id');
        $table->string('title',80);
        $table->integer('web3type_id');
        $table->integer('web3project_id');
        $table->integer('web3chain_id');
        $table->string('tags',80);
        $table->string('address',80);
        $table->text('abi');
        $table->text('description');
        $table->text('json');
        $table->timestamps();
        $table->boolean('completed');
        $table->boolean('archived');
        });
    */



</script>

<template>

    <div v-show="!e.archived" class="flex flex-col w-full h-screen text-xs p-5 pb-2 border-gray-500 rounded-2xl shadow-2xl max-h-64 mt-4" :class="bgColor">

        <span class ="text-xs py-0.5 -mt-2 mb-1 text-white font-bold text-center tracking-widest uppercase" :class="c">
            {{e.web3type.title }}
        </span>

        <div class="grid grid-cols-4 mt-2 ">
            <p class="text-lg text-base font-bold text-left mt-0 mb-2 col-span-3">

            <input type="checkbox" v-model="e.completed" :checked="e.completed" @change="$emit('complete',e.completed)" class="mr-2">
                {{e.title }}
            </p>

            <p class="text-xs mb-1 mt-1 ml-2 align-right">
                <img class="h-5 w-5 flex-shrink-0 rounded-full bg-gray-300" :src="imageUrl" alt="" />
            </p>
        </div>
       
        <div class="grid grid-cols-4 mt-0 ">
            <div class="text-xs uppercase mb-1 rounded-full px-1 py-0.5 text-center" :class="`${e.web3project.color}`">
                {{e.web3project.title}}
            </div>
            <div class="text-center col-span-3 text-xs text-gray-700 tolower uppercase mb-1 rounded-full px-2 py-0.5">
                {{e.tags}}
            </div>
        </div>

        <p class="text-sm text-slate-900 h-24 min-h-max mb-1 overflow-hidden text-center"> 
            {{e.description}}
        </p>

      <div>

        <div class="text-xs text-indigo-600 mb-2 text-right mr-2"> 
            <span class="text-gray-500">
                {{e.web3chain.title.slice(0,8)}}
            </span>
            {{e.web3chain.chain_nr}}
            
            <span class="text-gray-500 ml-2">
                address 
            </span >
            <a :href="e.url">
            {{e.address.slice(0,3)}}... {{e.address.slice(-3)}}
            </a>
        </div>

        <div class="-mt-px flex divide-x divide-gray-200 bg-transparent " >
          <div class="flex w-0 flex-1 ">
         
            <button v-if="e.completed"  @click="[e.archived = !e.archived, $emit('archive',e.archived)]" class="relative -mr-px inline-flex w-0 flex-1 items-center justify-center gap-x-1 rounded-bl-lg border border-transparent py-0.5 font-semibold text-red-500 hover:text-indigo-600"  >
              ARCHIVE
            </button>
          </div>
          <div class="-ml-px flex w-0 flex-1">
            <button v-if="!e.completed" @click="$emit('loadpay')" class="relative inline-flex w-0 flex-1 items-center justify-center gap-x-1 rounded-br-lg border border-transparent py-0.5 text-gray-400 hover:text-indigo-600">
              <EyeIcon class="h-5 w-5" aria-hidden="true" />
            </button>

          </div>
          <div class="-ml-px flex w-0 flex-1">
            <button v-if="!e.completed" @click="$emit('loadtablet')" class="relative inline-flex w-0 flex-1 items-center justify-center gap-x-1 rounded-br-lg border border-transparent py-0.5 text-gray-400 hover:text-indigo-600">
              <WalletIcon class="h-5 w-5 " aria-hidden="true" />
            </button>
          </div>
        </div>
      </div>

    </div>

</template>

