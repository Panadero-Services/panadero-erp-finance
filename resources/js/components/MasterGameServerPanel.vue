<template>
    <div class="master-server-panel">
        <div class="current-server" @click="toggleDropdown">
            <div class="server-info">
                <span class="server-name">Master Server</span>
                <span class="server-url">{{ gameState.server.status }}</span>
            </div>
            <div class="status-indicator">
                <span class="status-text">{{ gameState.server.active_players }} players</span>
                <span class="dropdown-arrow">{{ isDropdownOpen ? '▼' : '▶' }}</span>
            </div>
        </div>

        <div v-if="isDropdownOpen" class="server-dropdown">
            <!-- Server Info Section -->
            <div class="dropdown-section">
                <div class="section-header">Server Info</div>
                <div class="status-item">
                    <span>Status:</span>
                    <span :class="['status-badge', gameState.server.status]">{{ gameState.server.status }}</span>
                </div>
                <div class="status-item">
                    <span>Uptime:</span>
                    <span>{{ formatUptime(gameState.server.uptime) }}</span>
                </div>
                <div class="status-item">
                    <span>Active Players:</span>
                    <span>{{ gameState.server.active_players }}</span>
                </div>
                <div class="status-item">
                    <span>Total Games:</span>
                    <span>{{ gameState.server.total_games_played }}</span>
                </div>
            </div>

            <!-- Player Stats Section -->
            <div v-if="gameState.player" class="dropdown-section">
                <div class="section-header">Your Stats</div>
                <div class="status-item">
                    <span>Rank:</span>
                    <span>#{{ gameState.player.rank || 'N/A' }}</span>
                </div>
                <div class="status-item">
                    <span>High Score:</span>
                    <span>{{ gameState.player.high_score }}</span>
                </div>
                <div class="status-item">
                    <span>Current World:</span>
                    <span>{{ gameState.player.current_world || 'None' }}</span>
                </div>
                <div class="status-item">
                    <span>Resources:</span>
                    <span>{{ formatResources(gameState.player.resources) }}</span>
                </div>
            </div>

            <!-- Worlds Section -->
            <div class="dropdown-section">
                <div class="section-header">Available Worlds</div>
                <div v-for="world in availableWorlds" :key="world.id" class="world-item">
                    <div class="world-info">
                        <span class="world-name">{{ world.name }}</span>
                        <span class="world-players">{{ world.current_players }}/{{ world.max_players }}</span>
                    </div>
                    <div class="world-status">
                        <span :class="['status-badge', world.status]">{{ world.status }}</span>
                    </div>
                </div>
            </div>

            <!-- Top Players Section -->
            <div class="dropdown-section">
                <div class="section-header">Top Players</div>
                <div v-for="player in topPlayers" :key="player.rank" class="leaderboard-item">
                    <span class="rank">#{{ player.rank }}</span>
                    <span class="player-name">{{ player.player_name }}</span>
                    <span class="score">{{ player.high_score }}</span>
                </div>
            </div>

            <!-- Global Stats Section -->
            <div class="dropdown-section">
                <div class="section-header">Global Statistics</div>
                <div class="status-item">
                    <span>Total Players:</span>
                    <span>{{ gameState.global_stats.total_players }}</span>
                </div>
                <div class="status-item">
                    <span>Active Players:</span>
                    <span>{{ gameState.global_stats.active_players }}</span>
                </div>
                <div class="status-item">
                    <span>Worlds Online:</span>
                    <span>{{ gameState.global_stats.worlds_online }}/{{ gameState.global_stats.total_worlds }}</span>
                </div>
                <div class="status-item">
                    <span>Highest Score:</span>
                    <span>{{ gameState.global_stats.highest_score }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useMasterGameServerStore } from '@/stores/masterGameServer';

const masterGameServerStore = useMasterGameServerStore();
const isDropdownOpen = ref(false);

const gameState = computed(() => masterGameServerStore.gameState);
const availableWorlds = computed(() => masterGameServerStore.availableWorlds);
const topPlayers = computed(() => masterGameServerStore.topPlayers);

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

const formatUptime = (seconds) => {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    return `${hours}h ${minutes}m`;
};

const formatResources = (resources) => {
    if (!resources || Object.keys(resources).length === 0) {
        return 'None';
    }
    return Object.entries(resources)
        .map(([type, amount]) => `${type}: ${amount}`)
        .join(', ');
};

onMounted(async () => {
    await masterGameServerStore.initialize();
});
</script>

<style scoped>
.master-server-panel {
    position: relative;
    font-family: 'Courier New', monospace;
    font-size: 10px;
    color: #fff;
}

.current-server {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(0, 20, 40, 0.9);
    border: 1px solid #30a0ff;
    border-radius: 4px;
    padding: 6px 12px;
    cursor: pointer;
    min-width: 400px;
    transition: all 0.2s;
}

.current-server:hover {
    background: rgba(0, 30, 60, 0.9);
    border-color: #40b0ff;
}

.server-info {
    display: flex;
    align-items: center;
    gap: 12px;
    white-space: nowrap;
}

.server-name {
    font-size: 11px;
    font-weight: bold;
    color: #30a0ff;
}

.server-url {
    font-size: 10px;
    color: #ccc;
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: 8px;
}

.status-text {
    font-size: 10px;
    color: #ccc;
}

.dropdown-arrow {
    font-size: 8px;
    color: #30a0ff;
}

.server-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: rgba(0, 20, 40, 0.95);
    border: 1px solid #30a0ff;
    border-top: none;
    border-radius: 0 0 4px 4px;
    padding: 8px;
    z-index: 1000;
    max-height: 400px;
    overflow-y: auto;
}

.dropdown-section {
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid rgba(48, 160, 255, 0.2);
}

.dropdown-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.section-header {
    font-size: 10px;
    font-weight: bold;
    color: #30a0ff;
    margin-bottom: 6px;
    padding: 4px 0;
}

.status-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2px 0;
    font-size: 10px;
}

.status-badge {
    padding: 2px 6px;
    border-radius: 2px;
    font-size: 9px;
    font-weight: bold;
}

.status-badge.online {
    background: rgba(0, 255, 0, 0.2);
    color: #0f0;
}

.status-badge.offline {
    background: rgba(255, 0, 0, 0.2);
    color: #f00;
}

.status-badge.maintenance {
    background: rgba(255, 165, 0, 0.2);
    color: #ffa500;
}

.world-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 0;
    font-size: 10px;
}

.world-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.world-name {
    font-weight: bold;
    color: #30a0ff;
}

.world-players {
    color: #ccc;
    font-size: 9px;
}

.leaderboard-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 2px 0;
    font-size: 10px;
}

.rank {
    color: #30a0ff;
    font-weight: bold;
    min-width: 20px;
}

.player-name {
    flex: 1;
    color: #fff;
}

.score {
    color: #ccc;
    font-weight: bold;
}
</style> 