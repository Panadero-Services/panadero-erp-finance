import { ref, onUnmounted } from 'vue';
import { io } from 'socket.io-client';

export function useMultiplayer(config = {}) {
    const gameState = ref({
        status: 'init',
        players: {},
        myShip: null,
        blackHoles: new Map()  // Add this
    });
    
    const resources = ref({
        gold: 0,
        water: 0,
        kryptonite: 0
    });

    const isConnected = ref(false);
    const socket = ref(null);
    const lastUpdateTime = ref(Date.now());
    const serverUpdateRate = ref(50);

    // Smoothing configuration
    const SMOOTHING_ENABLED = true;
    const SMOOTHING_FACTOR = 0.2;
    const SMOOTH_CURRENT_PLAYER = false;

    // Subfunction: Setup socket connection
    const setupSocketConnection = () => {
        socket.value = io(config.serverUrl, {
            transports: ['websocket'],
            reconnection: true,
            reconnectionAttempts: 5,
            reconnectionDelay: 1000,
            query: { self: config.self }
        });
    };

    // Subfunction: Setup connection events
    const setupConnectionEvents = () => {
        socket.value.on('connect', () => {
            console.debug('=== MULTIPLAYER: Connected! ===');
            console.debug('Socket ID:', socket.value.id);
            isConnected.value = true;
            gameState.value.status = 'connected';
            
            socket.value.onAny((eventName, data) => {
                //console.debug('=== RECEIVED ANY EVENT ===', eventName, data);
            });
        });

        socket.value.on('disconnect', (reason) => {
            console.debug('=== MULTIPLAYER: Disconnected ===');
            console.debug('Reason:', reason);
            isConnected.value = false;
            gameState.value.status = 'disconnected';
        });

        socket.value.on('error', (error) => {
            console.error('=== MULTIPLAYER: Socket error ===', error);
        });
    };

    // Subfunction: Setup resource events
    const setupResourceEvents = () => {
        socket.value.on('update-player-resources', (data) => {
            console.debug('=== CLIENT RECEIVED SSOT DATA ===');
            console.debug('Data:', data);
            
            if (data.resources) {
                resources.value = data.resources;
            }
        });

        socket.value.on('collectible_collected', (data) => {
            //console.debug('Collected1:', data);
            if (data.resources) {
                // is this also updated else????
                resources.value = data.resources;
                //console.debug('Resources updated:', resources.value);
            }
            //console.debug('Collected:', data.streak[0], 'Points:', data.collections[data.streak[0]], 'New Score:', data.score);
        });
    };

    // Subfunction: Setup game state events
    const setupGameStateEvents = () => {
        socket.value.on('game_state', (state) => {
            // Add this debug log
            if (state.blackHoles) {
                //console.debug('Received black holes from server:', state.blackHoles);
            }
            const now = Date.now();
            
            if (lastUpdateTime.value) {
                const updateInterval = now - lastUpdateTime.value;
                serverUpdateRate.value = updateInterval;
            }
            lastUpdateTime.value = now;
            
            // Apply smoothing if enabled
            if (SMOOTHING_ENABLED && gameState.value.players) {
                const smoothedPlayers = {};
                
                for (const [id, newPlayer] of Object.entries(state.players)) {
                    const oldPlayer = gameState.value.players[id];
                    const shouldSmooth = SMOOTH_CURRENT_PLAYER || id !== socket.value?.id;
                    
                    if (oldPlayer && shouldSmooth) {
                        smoothedPlayers[id] = {
                            ...newPlayer,
                            x: oldPlayer.x + (newPlayer.x - oldPlayer.x) * SMOOTHING_FACTOR,
                            y: oldPlayer.y + (newPlayer.y - oldPlayer.y) * SMOOTHING_FACTOR,
                            angle: oldPlayer.angle + (newPlayer.angle - oldPlayer.angle) * SMOOTHING_FACTOR
                        };
                    } else {
                        smoothedPlayers[id] = newPlayer;
                    }
                }
                
                // Convert black holes to Map if they exist
                const blackHoles = state.blackHoles ? new Map(Object.entries(state.blackHoles)) : new Map();
                
                gameState.value = { 
                    ...state, 
                    players: smoothedPlayers,
                    blackHoles  // Add converted black holes
                };
            } else {
                // Convert black holes to Map if they exist
                const blackHoles = state.blackHoles ? new Map(Object.entries(state.blackHoles)) : new Map();
                
                gameState.value = {
                    ...state,
                    blackHoles  // Add converted black holes
                };
            }
            
            // Update resources
            const myShip = state.players[socket.value.id];
            if (myShip?.resources) {
                resources.value = myShip.resources;
            }
        });
    };

    // Main connect function
    const connect = () => {
        console.debug('=== MULTIPLAYER: Connecting to server ===');
        console.debug('Server URL:', config.serverUrl);
        console.debug('Self:', config.self);
        
        setupSocketConnection();
        setupConnectionEvents();
        setupResourceEvents();
        setupGameStateEvents();
    };

    const disconnect = () => {
        if (socket.value) {
            console.debug('=== MULTIPLAYER: Manual disconnect ===');
            socket.value.disconnect();
            socket.value = null;
        }
    };

    const sendShipState = (shipState) => {
        if (socket.value && isConnected.value) {
            socket.value.emit('ship_state', shipState);
        }
    };

    const sendInput = (inputType, value) => {
        if (socket.value && isConnected.value) {
            socket.value.emit('player_input', {
                type: inputType,
                value: value,
                timestamp: Date.now()
            });
        }
    };

    onUnmounted(() => {
        disconnect();
    });

    return {
        gameState,
        isConnected,
        connect,
        disconnect,
        sendShipState,
        sendInput,
        socket,
        resources,
        lastUpdateTime,
        serverUpdateRate
    };
} 