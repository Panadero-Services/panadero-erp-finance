# What's New

This document tracks significant changes and updates to the Solar System Invaders game.

## What's Changed

### Version 1.0.2 (Current)
- Added tablet support for game controls
- Enhanced touch interface for mobile gameplay
- Improved responsive design for various screen sizes

### Version 1.0.1 (August 2025)
- **Server Architecture**
  - Implemented robust Master Server <> Game Server heartbeat system
  - Added player session monitoring and recovery system
  - Enhanced server health checks with automatic status updates
  - Fixed AWS deployment issues with proper Nginx WebSocket proxy configuration
  - Added support for both local and production server URLs

- **Connection Handling**
  - Smart URL parsing: strips ports for production domains, keeps for local IPs
  - Improved WebSocket connection stability with proper timeout settings
  - Enhanced CORS configuration for secure cross-origin communication
  - Added version tracking (v1.0.1) in client-server communication
  - Optimized Socket.IO transport configuration

- **Development & Debugging**
  - Added version-aware logging system
  - Improved error handling for connection failures
  - Enhanced server status visualization in GameServerSelector
  - Added proper environment variable documentation
  - Streamlined local development setup

- **Infrastructure**
  - Added player_sessions table for better state management
  - Implemented scheduled health monitoring commands
  - Enhanced server registration and discovery
  - Improved black hole initialization and management
  - Added proper WebSocket proxy settings for production deployment

### Version 1.0.0 (Initial Release)
- Initial release of Solar System Invaders
- Basic game functionality
- Multiplayer support
- Real-time space combat
- Basic server infrastructure
