import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useMasterGameServerStore = defineStore('masterGameServer', () => {
    // State
    const gameState = ref({
        server: {
            status: 'offline',
            uptime: 0,
            active_players: 0,
            total_games_played: 0,
            current_round: 1
        },
        player: null,
        leaderboard: [],
        global_stats: {
            total_players: 0,
            total_games: 0,
            average_score: 0,
            highest_score: 0,
            most_active_player: null
        }
    });

    const isLoading = ref(false);
    const error = ref(null);
    const lastUpdate = ref(null);

    // Computed
    const isOnline = computed(() => gameState.value.server.status === 'online');
    const playerRank = computed(() => gameState.value.player?.rank || null);
    const playerHighScore = computed(() => gameState.value.player?.high_score || 0);
    const topPlayers = computed(() => gameState.value.leaderboard.slice(0, 5));

    // Actions
    const fetchGameState = async () => {
        try {
            isLoading.value = true;
            error.value = null;
            
            const response = await axios.get('/master/game-state', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            gameState.value = response.data;
            lastUpdate.value = new Date();
            
            console.log('Master Game Server state updated:', gameState.value);
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch game state';
            console.error('Error fetching game state:', err);
        } finally {
            isLoading.value = false;
        }
    };

    const updateScore = async (scoreData) => {
        try {
            isLoading.value = true;
            error.value = null;
            
            const response = await axios.post('/master/update-score', scoreData, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            // Refresh game state after score update
            await fetchGameState();
            
            console.log('Score updated successfully:', response.data);
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to update score';
            console.error('Error updating score:', err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    const fetchLeaderboard = async (limit = 10, timeframe = 'all') => {
        try {
            const response = await axios.get('/master/leaderboard', {
                params: { limit, timeframe },
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            gameState.value.leaderboard = response.data;
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch leaderboard';
            console.error('Error fetching leaderboard:', err);
            throw err;
        }
    };

    const fetchPlayerStats = async () => {
        try {
            const response = await axios.get('/master/player-stats', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            gameState.value.player = response.data;
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch player stats';
            console.error('Error fetching player stats:', err);
            throw err;
        }
    };

    const checkServerHealth = async () => {
        try {
            const response = await axios.get('/master/health', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            return response.data;
        } catch (err) {
            console.error('Error checking server health:', err);
            return { status: 'unhealthy', error: err.message };
        }
    };

    const submitGameResult = async (gameData) => {
        const scoreData = {
            score: gameData.score || 0,
            stage: gameData.stage || 0,
            bonus: gameData.bonus || 0,
            game_data: {
                ship_pattern: gameData.ship_pattern,
                collections: gameData.collections,
                streak: gameData.streak,
                server_id: gameData.server_id,
                game_duration: gameData.game_duration,
                final_position: gameData.final_position
            },
            nft_earned: gameData.nft_earned || 'none'
        };

        return await updateScore(scoreData);
    };

    // Initialize store
    const initialize = async () => {
        console.log('Initializing Master Game Server store...');
        await fetchGameState();
        
        // Set up periodic refresh
        setInterval(async () => {
            if (isOnline.value) {
                await fetchGameState();
            }
        }, 30000); // Refresh every 30 seconds
    };

    return {
        // State
        gameState,
        isLoading,
        error,
        lastUpdate,
        
        // Computed
        isOnline,
        playerRank,
        playerHighScore,
        topPlayers,
        
        // Actions
        fetchGameState,
        updateScore,
        fetchLeaderboard,
        fetchPlayerStats,
        checkServerHealth,
        submitGameResult,
        initialize
    };
}); 