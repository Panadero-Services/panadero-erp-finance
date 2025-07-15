// socket-server.js
import { Server } from 'socket.io';
import express from 'express';
import { createServer } from 'http';
import cors from 'cors';

const app = express();
const PORT = 3000;

// More permissive CORS for Express
app.use(cors({
    origin: ["http://localhost:8000", "http://localhost:5173", "http://127.0.0.1:8000"],
    methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
    credentials: true
}));

const httpServer = createServer(app);

// More detailed Socket.IO configuration
const io = new Server(httpServer, {
    cors: {
        origin: ["http://localhost:8000", "http://localhost:5173", "http://127.0.0.1:8000"],
        methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
        allowedHeaders: ["Content-Type", "Authorization", "X-Requested-With"],
        credentials: true,
        transports: ['websocket', 'polling']
    },
    pingTimeout: 60000,
    pingInterval: 25000
});

// Debug middleware
io.use((socket, next) => {
    console.log('Connection attempt from:', socket.handshake.headers.origin);
    console.log('Transport:', socket.conn.transport.name);
    next();
});

io.on('connection', (socket) => {
    console.log('Player connected:', socket.id);
    console.log('Origin:', socket.handshake.headers.origin);
    
    // Send initial game state
    socket.emit('game_state', {
        id: socket.id,
        status: 'init',
        players: {}
    });

    // Add ping handler
    socket.on('ping', () => {
        socket.emit('pong');
    });

    // Handle player state more robustly
    socket.on('join_game', () => {
        console.log('Player joined game:', socket.id);
        
        // Initialize player in game state if not exists
        if (!gameState.players.has(socket.id)) {
            gameState.players.set(socket.id, {
                id: socket.id,
                x: Math.random() * GAME_WIDTH,
                y: Math.random() * GAME_HEIGHT,
                rotation: 0,
                velocity: { x: 0, y: 0 },
                thrust: false
            });
        }
        
        // Send initial state to the joining player
        socket.emit('game_state', {
            id: socket.id,
            status: 'running',
            players: Object.fromEntries(gameState.players)
        });
        
        // Broadcast updated player list to all clients
        io.emit('game_state', {
            players: Object.fromEntries(gameState.players),
            status: 'running'
        });
    });

    socket.on('player_input', (input) => {
        console.log('Player input:', socket.id, input);
    });

    socket.on('disconnect', (reason) => {
        console.log('Player disconnected:', socket.id, 'Reason:', reason);
    });

    socket.on('error', (error) => {
        console.error('Socket error:', error);
    });
});

// Start the server
httpServer.listen(PORT, () => {
    console.log(`Socket.IO server running on port ${PORT}`);
    console.log('Allowed origins:', ["http://localhost:8000", "http://localhost:5173", "http://127.0.0.1:8000"]);
});