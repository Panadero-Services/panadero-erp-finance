<script setup>
import ApplicationLogo from '@/components/logoSelf.vue';
import {computed, onMounted, ref} from 'vue'

import { useContractStore } from '@/stores/contracts';
let contract = useContractStore();

defineProps({
	wallet : String,
	names : String,
	on: Boolean
});

let achieved=ref(25);

const _getContract = async () => {
    let _big = await contract.readContract("SELF_NFT_v224",'collectedSelf');
    achieved.value = parseFloat(_big.toString().slice(0,-18));
}

const aKlever = ['mamen','laura','klever','jawsome','ruwaifa','12345'];

onMounted(async ()=> {
    await _getContract();
})


</script>

<template>
    <!-- SELF WideScreen Wallet Card -->
    <div v-if="names.some(r=>aKlever.includes(r))"
        class="col-span-3 md:mb-2 scale-100 opacity-90 p-6 bg-white dark:bg-indigo-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-purple-500">
        <div class="h-14 w-14 bg-purple-50 dark:bg-purple-800/20 flex items-center justify-center rounded-full"><ApplicationLogo /></div>
        <div class="ml-4 mb-16">
            <h2 class="mt-1 text-xl text-gray-900 dark:text-white font-semibold">Klever <font class="text-green-600 font-normal">{{wallet}}</font></h2>
            <span  class='text-sm text-green-600'><font class="ml-8 text-gray-800 dark:text-gray-200 text-xs">MEMBER: </font> {{aKlever}}</span>
        </div>

        <div class="ml-12 mb-6">
            <h2 class="mt-1 text-xl text-gray-900 dark:text-white font-semibold">Results <font class="text-green-600 font-normal"></font></h2>
            <span  class='text-sm text-green-600'><font class="ml-8 text-gray-800 dark:text-gray-200 text-xs">Achievements: </font> {{achieved}}</span>
            <span  class='text-sm text-green-600'><font class="ml-8 text-gray-800 dark:text-gray-200 text-xs">Results: </font> 456</span>
            <span  class='text-sm text-green-600'><font class="ml-8 text-gray-800 dark:text-gray-200 text-xs">Scores: </font> 789</span>
        </div>





    </div>
</template>