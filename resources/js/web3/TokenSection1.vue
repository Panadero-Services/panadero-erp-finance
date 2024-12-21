<script setup>
    import {computed, onMounted, onServerPrefetch, reactive, ref } from 'vue'

    import { useWalletStore } from "@/stores/WalletStore.js";
    const walletStore = useWalletStore()
    import { useTokenStore } from "@/stores/TokenStore.js";

    const token = useTokenStore();
    token.initialize();

    const isLoading= ref(true);
    const tokenButton = (_token) => {
        token.set(_token);
        loadToken();
    }

    let wallet = walletStore;

    const loadToken = async () => {
     //   await walletStore.fill();
     //   await token.tokenBalance();
    }

    onMounted(async ()=> {
       // token.set('Swappy1');
        await loadToken();
        isLoading.value=false;
    })
  
  import { ArrowPathIcon, CloudArrowUpIcon, FingerPrintIcon, Bars3Icon, LockClosedIcon, WalletIcon, CheckCircleIcon } from '@heroicons/vue/24/outline'

</script>

<template>

  


</template>

<style type="text/css">


</style>