<template>
    <div class="master-server-selector">
        <!-- Current Server Display -->
        <div class="current-server" @click="toggleSelector">
            <div class="server-info">
                <span class="server-name">Master Server</span>
                <span class="server-url">{{ gameState.server.status }} - {{ gameState.server.active_players }} players</span>
            </div>
            <div class="server-status">
                <span class="status-dot" :class="{ 'online': isOnline, 'offline': !isOnline }"></span>
                <span class="status-text">{{ isOnline ? 'Online' : 'Offline' }}</span>
            </div>
            <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': showSelector }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>

        <!-- Server Details Dropdown -->
        <div v-if="showSelector" class="server-dropdown">
            <div class="dropdown-header">
                <span class="text-sm font-medium">Master Server Status</span>
            </div>
            
            <!-- Server Status -->
            <div class="status-section">
                <div class="section-header">
                    <span class="text-sm font-medium">Server Info</span>
                </div>
                <div class="status-grid">
                    <div class="status-item">
                        <span class="status-label">Status:</span>
                        <span class="status-value" :class="{ 'online': isOnline, 'offline': !isOnline }">
                            {{ gameState.server.status }}
                        </span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Uptime:</span>
                        <span class="status-value">{{ formatUptime(gameState.server.uptime) }}</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Active Players:</span>
                        <span class="status-value">{{ gameState.server.active_players }}</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Total Games:</span>
                        <span class="status-value">{{ gameState.server.total_games_played }}</span>
                    </div>
                </div>
            </div>

            <!-- Player Stats -->
            <div v-if="gameState.player" class="player-stats-section">
                <div class="section-header">
                    <span class="text-sm font-medium">Your Stats</span>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-label">Rank:</span>
                        <span class="stat-value">#{{ playerRank || 'N/A' }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">High Score:</span>
                        <span class="stat-value">{{ playerHighScore.toLocaleString() }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Games Played:</span>
                        <span class="stat-value">{{ gameState.player.total_games }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Average Score:</span>
                        <span class="stat-value">{{ Math.round(gameState.player.average_score).toLocaleString() }}</span>
                    </div>
                </div>
            </div>

            <!-- Leaderboard -->
            <div class="leaderboard-section">
                <div class="section-header">
                    <span class="text-sm font-medium">Top Players</span>
                </div>
                <div class="leaderboard-list">
                    <div 
                        v-for="player in topPlayers" 
                        :key="player.player_name"
                        class="leaderboard-item"
                        :class="{ 'current-player': player.player_name === gameState.player?.player_name }"
                    >
                        <span class="rank">#{{ player.rank }}</span>
                        <span class="player-name">{{ player.player_name }}</span>
                        <span class="score">{{ player.high_score.toLocaleString() }}</span>
                        <span class="games">({{ player.games_played }} games)</span>
                    </div>
                </div>
            </div>

            <!-- Global Stats -->
            <div class="global-stats-section">
                <div class="section-header">
                    <span class="text-sm font-medium">Global Statistics</span>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-label">Total Players:</span>
                        <span class="stat-value">{{ gameState.global_stats.total_players }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Highest Score:</span>
                        <span class="stat-value">{{ gameState.global_stats.highest_score.toLocaleString() }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Average Score:</span>
                        <span class="stat-value">{{ Math.round(gameState.global_stats.average_score).toLocaleString() }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Most Active:</span>
                        <span class="stat-value">{{ gameState.global_stats.most_active_player?.self || 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Last Update -->
            <div class="last-update">
                <small>Last updated: {{ formatLastUpdate() }}</small>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useMasterGameServerStore } from '@/stores/masterGameServer';

const masterGameServerStore = useMasterGameServerStore();

// State
const showSelector = ref(false);

// Computed properties
const gameState = computed(() => masterGameServerStore.gameState);
const isOnline = computed(() => masterGameServerStore.isOnline);
const playerRank = computed(() => masterGameServerStore.playerRank);
const playerHighScore = computed(() => masterGameServerStore.playerHighScore);
const topPlayers = computed(() => masterGameServerStore.topPlayers);

// Methods
const toggleSelector = () => {
    showSelector.value = !showSelector.value;
};

// Helper functions
const formatUptime = (seconds) => {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    
    if (hours > 0) {
        return `${hours}h ${minutes}m ${secs}s`;
    } else if (minutes > 0) {
        return `${minutes}m ${secs}s`;
    } else {
        return `${secs}s`;
    }
};

const formatLastUpdate = () => {
    if (!masterGameServerStore.lastUpdate) return 'Never';
    return new Date(masterGameServerStore.lastUpdate).toLocaleTimeString();
};
</script>

<style scoped>
.master-server-selector {
    position: relative;
    z-index: 1000;
}

.current-server {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    background: rgba(0, 0, 0, 0.8);
    border: 1px solid #333;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    min-width: 400px;
}

.current-server:hover {
    background: rgba(0, 0, 0, 0.9);
    border-color: #555;
}

.server-info {
    display: flex;
    align-items: center;
    flex: 1;
    gap: 12px;
}

.server-name {
    font-weight: 500;
    color: #fff;
    font-size: 11px;
    white-space: nowrap;
}

.server-url {
    font-size: 10px;
    color: #888;
    white-space: nowrap;
}

.server-status {
    display: flex;
    align-items: center;
    margin-right: 8px;
    gap: 4px;
}

.status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    margin-right: 4px;
}

.status-dot.online {
    background: #10b981;
}

.status-dot.offline {
    background: #ef4444;
}

.status-text {
    font-size: 10px;
    white-space: nowrap;
}

.status-text.online {
    color: #10b981;
}

.status-text.offline {
    color: #ef4444;
}

.server-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.95);
    border: 1px solid #333;
    border-radius: 6px;
    margin-top: 4px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
    max-height: 300px;
    overflow-y: auto;
}

.dropdown-header {
    padding: 4px;
    border-bottom: 1px solid #333;
    color: #fff;
}

.status-section,
.player-stats-section,
.leaderboard-section,
.global-stats-section {
    padding: 6px;
    border-bottom: 1px solid #333;
}

.section-header {
    margin-bottom: 8px;
    color: #fff;
}

.status-grid,
.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.status-item,
.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11px;
}

.status-label,
.stat-label {
    color: #888;
}

.status-value,
.stat-value {
    font-weight: bold;
    color: #fff;
}

.status-value.online {
    color: #10b981;
}

.status-value.offline {
    color: #ef4444;
}

.leaderboard-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.leaderboard-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    padding: 6px;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.05);
    border-bottom: 1px solid #222;
}

.leaderboard-item.current-player {
    background: rgba(59, 130, 246, 0.2);
    border-left: 3px solid #3b82f6;
}

.rank {
    font-weight: bold;
    color: #3b82f6;
    min-width: 20px;
}

.player-name {
    flex: 1;
    color: #fff;
}

.score {
    font-weight: bold;
    color: #ffff00;
}

.games {
    color: #888;
    font-size: 10px;
}

.last-update {
    padding: 6px;
    text-align: center;
    color: #666;
    font-size: 10px;
}
</style> 