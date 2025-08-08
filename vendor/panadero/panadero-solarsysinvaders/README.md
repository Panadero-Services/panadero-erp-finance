# Panadero SolarSys Invaders

A space-themed NFT game built by Panadero Services with real-time multiplayer capabilities and cross-server portals.

## Description

SolarSys Invaders is an open-world strategy 2D space game that combines NFT exploration, economic simulation, and team combat gameplay. Players can explore the solar system to collect NFTs and resources while engaging in tactical space battles. The game features cross-server travel through black holes and a sophisticated particle system for visual effects.

## Features

### Core Systems
- Multi-layer canvas rendering system with optimized performance
- Real-time multiplayer capabilities via Socket.IO
- Cross-server travel through black hole portals
- MetaMask wallet integration
- NFT support
- Resource management system

### Visual Effects
- Dynamic black holes with:
  - Pulsing event horizons
  - Orbiting debris and asteroids
  - Accretion disk effects
  - Gravitational distortion (coming soon)
- Multi-layered parallax starfield
- Particle system for explosions and effects
- Three visual themes: space, atompad, moonwealth

### Game Elements
- Resource fields for gathering materials
- Safe zones for player protection
- Collectible items and power-ups
- Combat system with projectiles
- Ship customization options

### Technical Architecture
- Modular drawing system with specialized composables:
  - Black hole rendering
  - Starfield generation
  - Ship management
  - Collectibles and resources
  - Effects and particles
  - Safe zones
- Server-side game state management
- Client-side performance optimizations
- Cross-server communication protocol

### Multiplayer Features
- Real-time player synchronization
- Resource sharing capabilities
- Team-based gameplay
- Cross-server travel via black holes
- Shared game state management

## Installation

```bash
npm install panadero-solarsysinvaders
# or
yarn add panadero-solarsysinvaders
```

## Environment Configuration

The game supports multiple deployment configurations:
```env
VITE_WEBSOCKET_SERVER=http://localhost:3001
VITE_GAME_SERVER_URL=http://127.0.0.1:3001
VITE_GAME_SERVER_URL_LOCAL=http://127.0.0.1:3001
VITE_GAME_SERVER_URL_NETWORK=http://192.168.2.20:3001
VITE_GAME_SERVER_URL_WW=http://192.168.2.20:3001
VITE_SERVER_HOST=127.0.0.1
VITE_SERVER_HOST_NETWORK=192.168.2.20
VITE_SERVER_PORT=5173
VITE_MASTER_SERVER_URL=http://127.0.0.1:8000
VITE_MASTER_SERVER_URL_NETWORK=http://192.168.2.9:8000
```

## Architecture

The game uses a distributed architecture with:
- Master Server: Handles authentication and global state
- Game Servers: Process game logic and local state
- Socket.IO: Manages real-time communication
- Vue.js: Powers the client-side interface
- Canvas API: Renders game graphics

## Performance Optimization

The game includes several optimizations:
- Shared particle system instance
- Modular drawing composables
- Efficient state management
- Optimized render cycles
- Smart asset loading

## Distributed Universe Architecture

### Infinite Game World Expansion
The game's revolutionary Master Server <> Game Server architecture enables unlimited universe growth through:

- **Dynamic World Creation**: Any player can spawn and host new game worlds
- **Seamless Cross-Server Travel**: Black hole portals connect different game servers
- **Distributed Processing**: Each game server handles its own sector of space
- **Automatic Discovery**: Master Server maintains a directory of all active worlds
- **Load Balancing**: Players are distributed across multiple game servers

### Master Server Capabilities
- Central authentication and player profile management
- Global leaderboard and achievement tracking
- Game server registration and discovery
- Cross-server resource and NFT verification
- Real-time server status monitoring

### Game Server Features
- Autonomous world instance management
- Local physics and game state processing
- Resource field generation and management
- Player interaction and combat handling
- Portal management for inter-server travel

### Scalability Benefits
- **Unlimited Growth**: No theoretical limit to the number of game servers
- **Community Driven**: Players can host their own worlds
- **Resource Distribution**: Each server manages its own resource economy
- **Performance Optimization**: Distributed load across multiple servers
- **Fault Tolerance**: Individual server issues don't affect the whole universe

### Inter-Server Communication
- Secure WebSocket connections between servers
- State synchronization for traveling players
- Resource and NFT transfer protocols
- Cross-server chat and trading capabilities
- Real-time server status updates

This architecture transforms SolarSys Invaders into a potentially infinite universe where:
- Communities can create and manage their own space sectors
- Players can explore an ever-expanding universe
- Resources and economies can develop organically
- New gameplay mechanics can be added server-by-server
- The game can scale to support massive player populations

## Coming Soon
- Enhanced gravitational effects
- Advanced portal mechanics
- Additional visual effects
- Extended multiplayer features
- More NFT integrations

## License

Copyright © 2024 Panadero Services. All rights reserved. 