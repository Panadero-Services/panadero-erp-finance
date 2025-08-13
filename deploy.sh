#!/bin/bash

# Panadero ERP Finance Package Deployment Script
# This script helps deploy updates to the finance package

set -e

echo "🚀 Deploying Panadero ERP Finance Package..."

# Check if we're in the right directory
if [ ! -f "package.json" ]; then
    echo "❌ Error: package.json not found. Please run this from the package directory."
    exit 1
fi

# Get current version
CURRENT_VERSION=$(node -p "require('./package.json').version")
echo "📦 Current version: $CURRENT_VERSION"

# Build the package
echo "🔨 Building package..."
npm run build

# Run tests if available
if [ -f "package.json" ] && grep -q "test" package.json; then
    echo "🧪 Running tests..."
    npm test
fi

# Create git tag for release
echo "🏷️  Creating git tag v$CURRENT_VERSION..."
git add .
git commit -m "Release v$CURRENT_VERSION" || echo "No changes to commit"
git tag "v$CURRENT_VERSION" || echo "Tag already exists"

echo "✅ Package v$CURRENT_VERSION deployed successfully!"
echo "📝 Next steps:"
echo "   1. Push to GitHub: git push origin master --tags"
echo "   2. Create release on GitHub"
echo "   3. Update main project if needed"
