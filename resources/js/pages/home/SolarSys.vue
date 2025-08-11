<script setup>
import { ref, onMounted } from 'vue';
import SolarSysGame, { 
    GameServerSelector, 
    MasterGameServerPanel, 
    useMasterGameServerStore,
    SystemSettingsPanel // Add this import
} from 'panadero-solarsysinvaders';
import AppToolbarLayout from '@/layouts/AppToolbarLayout.vue';
import { useSettingsStore } from '@/stores/settings';
import { useContractStore } from '@/stores/contracts';

// Get the game server URL from environment - use network URL for all clients
const defaultServerUrl = import.meta.env.VITE_GAME_SERVER_URL_NETWORK || import.meta.env.VITE_GAME_SERVER_URL;
const gameServerUrl = ref(defaultServerUrl);

console.debug('SolarSys loading...');
console.debug('Default Game Server URL: ', defaultServerUrl);
console.debug('Environment variables:', {
    VITE_GAME_SERVER_URL: import.meta.env.VITE_GAME_SERVER_URL,
    VITE_GAME_SERVER_URL_NETWORK: import.meta.env.VITE_GAME_SERVER_URL_NETWORK,
    defaultServerUrl,
    gameServerUrl: gameServerUrl.value
});

const settingsStore = useSettingsStore();
const contractStore = useContractStore();
const masterGameServerStore = useMasterGameServerStore();
const mounted = ref(false);

// Add ref for game instance
const gameInstance = ref(null);

const handleServerChange = (newServerUrl) => {
    console.log('Switching to server:', newServerUrl);
    gameServerUrl.value = newServerUrl;
    // Force game component to reconnect
    mounted.value = false;
    setTimeout(() => {
        mounted.value = true;
    }, 100);
};

// Add settings change handler
const handleSettingsChanged = (newSettings) => {
    if (gameInstance.value) {
        gameInstance.value.updateSettings(newSettings);
    }
};

onMounted(() => {
    console.debug('SolarSys mounted');
    mounted.value = true;
    
    // Initialize Master Game Server
    masterGameServerStore.initialize();
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
        :set="{
            ...settingsStore,
            dark: true, // Force dark mode
        }"
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

                <!-- System Settings Panel -->
                <div class="settings-panel-container">
                    <SystemSettingsPanel 
                        @settings-changed="handleSettingsChanged"
                    />
                </div>

                <div v-if="!mounted" class="loading">
                    Loading game...
                </div>
                <div v-else class="game-wrapper">
                    <SolarSysGame 
                        ref="gameInstance"
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

/* Add styles for settings panel container */
.settings-panel-container {
    position: absolute;
    top: 10px;
    right: 370px; /* Position it next to the master server panel */
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