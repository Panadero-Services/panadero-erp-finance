<script setup>
import { ref, onMounted } from 'vue';
import SolarSysGame, { GameServerSelector, MasterGameServerPanel, useMasterGameServerStore } from 'panadero-solarsysinvaders';
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';

// Get the game server URL from environment - use network URL for all clients
const defaultServerUrl = import.meta.env.VITE_GAME_SERVER_URL_NETWORK || import.meta.env.VITE_GAME_SERVER_URL;
const gameServerUrl = ref(defaultServerUrl);

console.log('SolarSys loading...');
console.log('Default Game Server URL: ', defaultServerUrl);

const settingsStore = useSettingsStore();
const contractStore = useContractStore();
const masterGameServerStore = useMasterGameServerStore();
const mounted = ref(false);

const handleServerChange = (newServerUrl) => {
    console.log('Switching to server:', newServerUrl);
    gameServerUrl.value = newServerUrl;
    // Force game component to reconnect
    mounted.value = false;
    setTimeout(() => {
        mounted.value = true;
    }, 100);
};

onMounted(() => {
    console.log('SolarSys mounted');
    mounted.value = true;
    
    // Initialize Master Game Server
    masterGameServerStore.initialize();
    console.log('whatever')
    console.log('whatever')
    console.log('whatever')
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
                <!-- Server Selector -->

                <div class="server-selector-container">
                        <GameServerSelector 
                            :currentServer="gameServerUrl"
                            @server-changed="handleServerChange"
                        />
                </div>
                <!-- Master Game Server Panel -->
                <div class="master-server-panel-container">
                        <MasterGameServerPanel />
                </div>

                <div v-if="!mounted" class="loading">
                    Loading game...
                </div>
                <div v-else class="game-wrapper">
                    <SolarSysGame 
                        :multiplayer="true"
                        :serverUrl="gameServerUrl"
                        :self="settingsStore.self || 'nope'"  
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
    position: relative;
}

.server-selector-container {
    width: 350px;
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 9999;
}

.master-server-panel-container {
    width: 350px;
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 9999;
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