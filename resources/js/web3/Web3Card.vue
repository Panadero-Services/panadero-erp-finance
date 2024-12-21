<script setup>
  import { ref } from 'vue'    

  import { WalletIcon, XMarkIcon, EyeIcon } from '@heroicons/vue/20/solid'
    const props = defineProps({
      col: String,
      name: String
    });

let activated = ref(false);

const people = [
  {
    name: 'SELF',
    title: '0x51ddedcad24f123406d18e275d1fcbd59dbcb9fd9',
    role: '$JaW',
    email: 'janecooper@example.com',
    telephone: '+1-202-555-0170',
    imageUrl: '/CryptoLogos/PNG/bsctestnet.png',
  },
]
</script>


<template>
  <div v-if="!activated" class="">

  <ul role="list" class="">
    <li v-for="person in people" :key="person.email" class="col-span-1 divide-y divide-gray-200 rounded-lgs rounded-xl h-64" :class="col">
      <div  class="flex w-full items-center justify-between space-x-2 p-6 pb-14">
        <div class="flex-1 truncate">
          <div class="flex items-center space-x-3">
            <h3 class="truncate text-base font-medium text-gray-100">{{ name }}</h3>
            <span class="inline-block flex-shrink-0 rounded-full bg-green-300 px-2 py-0.5 text-xs font-medium"  >{{ person.role }}</span>
          </div>
          <p class="mt-1 truncate text-sm font-semibold text-gray-800">{{ person.title }}</p>
        </div>
        <img class="-mt-10 h-6 w-6 flex-shrink-0 rounded-full bg-gray-300" :src="person.imageUrl" alt="" />
      </div>
      <div>
        <div class="-mt-px flex divide-x divide-gray-200">
          <div class="flex w-0 flex-1">
            <button @click="activated=!activated" class="relative -mr-px inline-flex w-0 flex-1 items-center justify-center gap-x-3 rounded-bl-lg border border-transparent py-2 text-sm font-semibold text-gray-100 hover:text-green-400" >
              <XMarkIcon class="h-4 w-4" aria-hidden="true" />
            </button>
          </div>
          <div class="-ml-px flex w-0 flex-1">
            <a :href="`tel:${person.telephone}`" class="relative inline-flex w-0 flex-1 items-center justify-center gap-x-3 rounded-br-lg border border-transparent py-2 text-sm font-semibold text-gray-100 hover:text-green-400">
              <EyeIcon class="h-4 w-4" aria-hidden="true" />
            </a>
          </div>
          <div class="-ml-px flex w-0 flex-1">
            <a :href="`tel:${person.telephone}`" class="relative inline-flex w-0 flex-1 items-center justify-center gap-x-3 rounded-br-lg border border-transparent py-2 text-sm font-semibold text-gray-100 hover:text-green-400">
              <WalletIcon class="h-4 w-4" aria-hidden="true" />
            </a>
          </div>
        </div>
      </div>
    </li>
  </ul>

</div>

  <div v-if="activated" class="">
    <button active="!isLoading" class="group max-w-xs mx-auto rounded-lg p-2 ml-4 my-4 mx-1 ring-1 ring-slate-900/5 shadow-lg space-y-1 hover:bg-green-600 hover:ring-green-800" :class="col" @click="activated=!activated">
      <span class="text-sm text-gray-50 group-hover:text-white text-sm font-semibold ">{{name}}</span>
    </button>
  </div>

  <div v-show="finished" class="flex flex-col text-xs bg-gray-300 p-4 pb-2 border-gray-200 text-gray-500 rounded-lg">
    <h2 v-if="$slots.heading" class ="text-xs uppercase mb-0">
      <button v-show="toggleAble && id>0" @click="$emit('delete', {id: id})">&times;</button>
      <slot name="heading" />
    </h2>
    <div class="mt-2 mb-1 text-sm text-gray-600">
            <slot name="title" />
    </div>

    <div class="mt- text-xs text-indigo-500 dark:text-indigo-700 uppercase mt-auto w-full">
          <slot name="author" />
    </div>
  </div>



</template>