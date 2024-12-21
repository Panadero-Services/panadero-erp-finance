<script setup>
    import {computed,ref} from 'vue';
    import TheButton from "@/CustomComponents/TheButton.vue";

    import {Interface} from 'ethers';

  const props = defineProps({
      store: Object,
      description: Object,
      color: String,
      min: Number,
      max: Number,
      isLoading: Boolean
  });


  let whatever = ref(3);
  let abiHuman = ref([]);

  const convertAbi = (_abi) => {
//    _abi.map(element ==)
//    return "whatever123";
        const iface = new Interface(_abi);
        abiHuman = iface.format(false);
        //return abiHuman;
  }

  const klik = () => {
    whatever.value++;
    //aAbi.value = 33;
}

  // const _convertedMenuItems=computed(()=>{return props.store.map(function(item){item.route="/public/"+item.id;return item});});
  // const _items=computed(()=>{return props.menuItems.map(function(item){item.route="/public/"+item.id;return item});});

  import { ArrowPathIcon, CloudArrowUpIcon, FingerPrintIcon, Bars3Icon, LockClosedIcon, WalletIcon } from '@heroicons/vue/24/outline'

</script>

<template>

  <div class="bg-white py-1 sm:py-2 lg:py-2 h-[900px] ">

    <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
      <div class="-ml-4 -mt-4 flex flex-wrap items-center justify-between sm:flex-nowrap">
        <div class="ml-4 mt-4">
          <h3 class="text-base font-semibold leading-6 text-gray-900">Contract Detailed</h3>
          <p class="mt-1 text-sm text-gray-500">Description of contract: {{whatever}}</p>
        </div>
        <div class="ml-4 mt-4 flex-shrink-0">
        </div>
      </div>
    </div>


    <div class="mx-auto max-w-7xl px-6 lg:px-8">

      <button @click="klik" class="text-blue-400 hover:text-indigo-600">klik</button>


      <div v-for="(value, key, index) in store" :key="`${ key }-${ index }`" >
        <p v-if="index>min && index<max" class="ml-1 p-1">
          <div class="flex grid grid-cols-3 max-w-[220px]">
            <div class="col-span-1 text-zinc-900  text-xs">
              {{index}} {{key}} 
            </div>
            <div v-if="!(key=='abi')" class="col-span-2 max-w-[220px] text-xs" :class="color">
              <pre>{{value}}</pre>
            </div>
            <div v-if="(key=='abi')" class="col-span-2 max-w-[220px] text-xs" :class="color">
              <pre>{{convertAbi(value)}}</pre>
            </div>
          </div>
        </p>

      </div>




      <div v-for="r in abiHuman">
        <div v-if="r.includes(' view ')">
          <p class="text-green-700" >{{r}}</p>
        </div>
      
      </div>



      


    </div>



  </div>



   

</template>















