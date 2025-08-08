import { io } from 'socket.io-client';

const socket = io('http://localhost:3001', {
    transports: ['websocket', 'polling'],
    reconnection: true,
    reconnectionAttempts: 5
});

socket.on('connect', () => {
    console.log('Connected!', socket.id);
});

socket.on('connect_error', (error) => {
    console.log('Connection error:', error.message);
});

socket.on('disconnect', (reason) => {
    console.log('Disconnected:', reason);
});
