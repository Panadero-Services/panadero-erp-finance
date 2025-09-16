#!/bin/bash

# Dual mode package switcher
MODE=$1

if [ "$MODE" = "dev" ]; then
    echo "🔄 Switching to DEVELOPMENT mode (local packages)..."
    
    # Update package.json to use local files
    sed -i '' 's|"panadero-erp-inventory": "git+https://github.com/.*"|"panadero-erp-inventory": "file:packages/panadero-erp-inventory"|g' package.json
    sed -i '' 's|"panadero-shared-styling": "git+https://github.com/.*"|"panadero-shared-styling": "file:packages/panadero-shared-styling"|g' package.json
    sed -i '' 's|"panadero-shared-components": "git+https://github.com/.*"|"panadero-shared-components": "file:packages/panadero-shared-components"|g' package.json
    
    # Install local packages
    npm install
    
    echo "✅ DEVELOPMENT mode active - changes appear instantly!"
    
elif [ "$MODE" = "prod" ]; then
    echo "🔄 Switching to PRODUCTION mode (GitHub packages)..."
    
    # Update package.json to use GitHub
    sed -i '' 's|"panadero-erp-inventory": "file:packages/panadero-erp-inventory"|"panadero-erp-inventory": "git+https://github.com/Panadero-Services/panadero-erp-inventory.git"|g' package.json
    sed -i '' 's|"panadero-shared-styling": "file:packages/panadero-shared-styling"|"panadero-shared-styling": "git+https://github.com/Panadero-Services/panadero-shared-styling.git"|g' package.json
    sed -i '' 's|"panadero-shared-components": "file:packages/panadero-shared-components"|"panadero-shared-components": "git+https://github.com/Panadero-Services/panadero-shared-components.git"|g' package.json
    
    # Install GitHub packages
    npm install
    
    echo "✅ PRODUCTION mode active - using GitHub packages!"
    
else
    echo "Usage: ./switch-mode.sh [dev|prod]"
    echo "  dev  - Use local packages for development"
    echo "  prod - Use GitHub packages for production"
fi





