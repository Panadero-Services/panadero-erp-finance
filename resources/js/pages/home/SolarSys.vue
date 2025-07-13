<script setup>
import { ref, onMounted } from 'vue';
import SolarSysGame from 'panadero-solarsysinvaders';
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';

console.log('SolarSys loading, SolarSysGame:', SolarSysGame);

const settingsStore = useSettingsStore();
const contractStore = useContractStore();
const mounted = ref(false);

onMounted(() => {
    console.log('SolarSys mounted');
    mounted.value = true;
});

defineProps({
    page: {
        type: Object,
        required: true
    },
    baseSections: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <AppToolbarLayout 
        :title="page.title" 
        :baseSections="baseSections" 
        :set="settingsStore"
        :contract="contractStore"
        :page="page"
    >
      <template #default>
            <div class="game-page">
                <div v-if="!mounted" class="loading">
                    Loading game...
                </div>
                <div v-else class="game-wrapper">
                    <SolarSysGame :multiplayer="true" />
                </div>
         </div>
      </template>
   </AppToolbarLayout>
</template>

<style scoped>
.game-page {
    min-height: 100vh;
    background: #000;
    display: flex;
    justify-content: center;
    align-items: center;
}

.game-wrapper {
    width: 800px;
    height: 600px;
    border: 1px solid #333;
    position: relative;
}

.loading {
    color: white;
    font-family: monospace;
    text-align: center;
    padding: 20px;
}
</style>
