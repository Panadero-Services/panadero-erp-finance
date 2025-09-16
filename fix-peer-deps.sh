#!/bin/bash

# Fix peer dependencies for all ERP packages
echo "🔧 Fixing peer dependencies for all packages..."

# Common peer dependencies that all packages need
COMMON_PEER_DEPS=(
    "vue@^3.0.0"
    "pinia@^2.0.0" 
    "axios@^1.0.0"
    "decimal.js@^10.0.0"
)

# Install common peer dependencies
echo "📥 Installing common peer dependencies..."
npm install --save-dev ${COMMON_PEER_DEPS[@]}

# Update each package's peerDependencies
for package_dir in packages/panadero-*; do
    if [ -d "$package_dir" ] && [ -f "$package_dir/package.json" ]; then
        package_name=$(basename "$package_dir")
        echo "  📦 Updating $package_name peer dependencies..."
        
        # Add common peer dependencies to each package
        cd "$package_dir"
        
        # Update peerDependencies in package.json
        node -e "
            const fs = require('fs');
            const pkg = JSON.parse(fs.readFileSync('package.json', 'utf8'));
            
            pkg.peerDependencies = {
                'vue': '^3.0.0',
                'pinia': '^2.0.0', 
                'axios': '^1.0.0',
                'decimal.js': '^10.0.0'
            };
            
            fs.writeFileSync('package.json', JSON.stringify(pkg, null, 2));
        "
        
        cd - > /dev/null
    fi
done

echo "✅ Peer dependencies fixed for all packages!"





