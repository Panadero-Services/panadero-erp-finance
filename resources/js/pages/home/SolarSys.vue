<script setup>
import { ref, onMounted } from 'vue';
import SolarSysGame from 'panadero-solarsysinvaders';
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';

// Get the game server URL from environment
const gameServerUrl = import.meta.env.VITE_GAME_SERVER_URL;

console.log('SolarSys loading...');

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
                    <SolarSysGame 
                        :multiplayer="true"
                        :serverUrl="gameServerUrl"
                    />
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
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.game-wrapper {
    width: 800px;
    height: 600px;
    border: 1px solid #333;
    position: relative;
}

.debug-panel {
    position: absolute;
    top: -80px;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.8);
    color: #00ff00;
    font-family: monospace;
    padding: 10px;
    border: 1px solid #333;
    border-radius: 4px;
    z-index: 1000;
}

.loading {
    color: white;
    font-family: monospace;
    text-align: center;
    padding: 20px;
}
</style>
