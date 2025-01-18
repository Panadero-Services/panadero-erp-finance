<script setup>
import {computed, ref, onMounted, defineComponent, h } from 'vue';
//import ApplicationMark from '@/layouts/logos/self1.png';
import self1 from "@/layouts/logos/self1.png";
import self2 from "@/layouts/logos/self2.png";
import { HomeIcon, WrenchIcon, LockClosedIcon, LockOpenIcon, WalletIcon, CakeIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    set: Object,
    contract: Object
});

</script>


<template>
  <footer class="bg-indigo-900 text-gray-300">
    <div class="mx-auto max-w-7xl px-3 pb-4 pt-2 sm:pt-2 lg:px-2 lg:pt-4">
      <div class="xl:grid xl:grid-cols-3 xl:gap-4">
        <div class="space-y-2">
          <p class=" ">DEVELOPER SECTION</p>
        </div>
      </div>
      <div class="mt-2 border-t border-white/10 pt-2 sm:mt-4 lg:mt-6 ">
        <p class="text-xl font-bold">store/settings.js:</p>
        {{set}}
      </div>

      <div class="mt-2 border-t border-white/10 pt-2 sm:mt-4 lg:mt-6 ">
        <p class="text-xl font-bold">store/contract.js:</p>
        {{contract}}
      </div>


    </div>
  </footer>
</template>
