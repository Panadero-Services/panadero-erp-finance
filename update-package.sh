#!/bin/bash

# Simple package update script
echo "🔄 Updating panadero-erp-inventory package..."

# Go to package directory
cd packages/panadero-erp-inventory

# Add all changes
git add .

# Commit with timestamp
git commit -m "Update: $(date '+%Y-%m-%d %H:%M:%S')"

# Push to GitHub
git push origin main

# Go back to main project
cd ../..

# Remove old package
rm -rf node_modules/panadero-erp-inventory

# Install fresh from GitHub
npm install

echo "✅ Package updated and installed!"
echo "🔄 Restart your dev server to see changes"





