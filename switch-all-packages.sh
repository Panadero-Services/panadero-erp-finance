#!/bin/bash

# Comprehensive dual mode switcher for ALL ERP packages
MODE=$1

# All ERP packages that are actually in package.json
PACKAGES=(
    "panadero-erp-inventory"
    "panadero-erp-finance" 
    "panadero-erp-hr"
    "panadero-erp-compliance"
    "panadero-workflow"
)

if [ "$MODE" = "dev" ]; then
    echo "🔄 Switching ALL packages to DEVELOPMENT mode (local)..."
    
    for package in "${PACKAGES[@]}"; do
        echo "  📦 Switching $package to local..."
        
        # Switch to local file path
        sed -i '' "s|\"$package\": \"git+https://github.com/.*\"|\"$package\": \"file:packages/$package\"|g" package.json
        sed -i '' "s|\"$package\": \"github:.*\"|\"$package\": \"file:packages/$package\"|g" package.json
    done
    
    # Install all local packages
    echo "📥 Installing all local packages..."
    npm install
    
    echo "✅ DEVELOPMENT mode active for ALL packages!"
    echo "   Changes appear instantly - no commits needed!"
    
elif [ "$MODE" = "prod" ]; then
    echo "🔄 Switching ALL packages to PRODUCTION mode (GitHub)..."
    
    for package in "${PACKAGES[@]}"; do
        echo "  📦 Switching $package to GitHub..."
        
        # Switch to GitHub (assuming all packages follow same naming)
        sed -i '' "s|\"$package\": \"file:packages/$package\"|\"$package\": \"git+https://github.com/Panadero-Services/$package.git\"|g" package.json
    done
    
    # Install all GitHub packages
    echo "📥 Installing all GitHub packages..."
    npm install
    
    echo "✅ PRODUCTION mode active for ALL packages!"
    echo "   Using stable GitHub versions!"
    
elif [ "$MODE" = "status" ]; then
    echo "📊 Current package status:"
    for package in "${PACKAGES[@]}"; do
        line=$(grep "\"$package\":" package.json)
        if echo "$line" | grep -q "file:packages/$package"; then
            echo "  📦 $package: LOCAL (dev mode)"
        elif echo "$line" | grep -q "github:"; then
            echo "  📦 $package: GITHUB (prod mode)"
        elif echo "$line" | grep -q "git+https://github.com"; then
            echo "  📦 $package: GITHUB (prod mode)"
        else
            echo "  📦 $package: UNKNOWN - $line"
        fi
    done
    
else
    echo "Usage: ./switch-all-packages.sh [dev|prod|status]"
    echo ""
    echo "  dev     - Switch ALL packages to local development"
    echo "  prod    - Switch ALL packages to GitHub production"  
    echo "  status  - Show current mode for all packages"
    echo ""
    echo "Packages managed:"
    for package in "${PACKAGES[@]}"; do
        echo "  - $package"
    done
fi
