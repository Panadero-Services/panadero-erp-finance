#!/bin/bash

# Fix Git Access Issues for AWS Deployment
echo "🔧 Fixing Git access issues for AWS deployment..."

# Problematic packages that use SSH and might not be accessible
PROBLEMATIC_PACKAGES=(
    "panadero-contract-api"
    "panadero-minigames"
    "panadero-self"
    "panadero-server"
    "panadero-sim"
    "panadero-solarsysinvaders"
)

echo "📋 Problematic packages detected:"
for package in "${PROBLEMATIC_PACKAGES[@]}"; do
    echo "  - $package"
done

echo ""
echo "🔧 OPTIONS:"
echo "1) Switch to HTTPS URLs (recommended)"
echo "2) Remove problematic packages temporarily"
echo "3) Skip problematic packages and install others"
echo "4) Exit"
echo ""

read -p "Choose option (1-4): " choice

case $choice in
    1)
        echo ""
        echo "🔄 Switching to HTTPS URLs..."
        
        # Switch SSH URLs to HTTPS
        sed -i 's|github:lieuwebakker/|git+https://github.com/lieuwebakker/|g' package.json
        sed -i 's|github:Panadero-Services/|git+https://github.com/Panadero-Services/|g' package.json
        
        echo "✅ Switched to HTTPS URLs"
        echo "📥 Installing packages..."
        npm install
        ;;
    2)
        echo ""
        echo "🗑️  Removing problematic packages temporarily..."
        
        # Comment out problematic packages
        for package in "${PROBLEMATIC_PACKAGES[@]}"; do
            sed -i "s|\"$package\":|\"#$package\":|g" package.json
        done
        
        echo "✅ Commented out problematic packages"
        echo "📥 Installing remaining packages..."
        npm install
        ;;
    3)
        echo ""
        echo "⏭️  Skipping problematic packages..."
        
        # Install with --ignore-scripts to skip problematic packages
        npm install --ignore-scripts
        ;;
    4)
        echo "Exiting..."
        exit 0
        ;;
    *)
        echo "Invalid option. Exiting..."
        exit 1
        ;;
esac

echo ""
echo "🎉 Git access issues resolved!"
echo ""
echo "Next steps:"
echo "1. Run: ./switch-all-packages.sh prod"
echo "2. Run: npm run build"
echo "3. Deploy to AWS"
