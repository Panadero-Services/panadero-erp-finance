#!/bin/bash

# Deploy Packages to GitHub Script
# Helps deploy local packages to their GitHub repositories

echo "🚀 Deploying packages to GitHub for AWS deployment..."
echo ""

# Packages that need to be deployed
PACKAGES=(
    "panadero-erp-inventory"
    "panadero-erp-hr"
    "panadero-erp-compliance"
    "panadero-workflow"
)

# GitHub organization
ORG="Panadero-Services"

echo "Packages to deploy:"
for package in "${PACKAGES[@]}"; do
    echo "  - $package"
done
echo ""

# Check if we're in the right directory
if [ ! -d "packages" ]; then
    echo "❌ Error: packages directory not found. Run this from the project root."
    exit 1
fi

echo "🔍 Checking local packages..."
echo "================================"

for package in "${PACKAGES[@]}"; do
    if [ -d "packages/$package" ]; then
        echo "✅ $package: Local package found"
    else
        echo "❌ $package: Local package NOT FOUND"
        exit 1
    fi
done

echo ""
echo "📋 DEPLOYMENT STEPS:"
echo "1. Create GitHub repositories (if they don't exist)"
echo "2. Initialize git in each package directory"
echo "3. Add remote origin"
echo "4. Push to GitHub"
echo ""

read -p "Do you want to proceed with deployment? (y/N): " confirm
if [[ ! $confirm =~ ^[Yy]$ ]]; then
    echo "Deployment cancelled."
    exit 0
fi

echo ""
echo "🚀 Starting deployment process..."
echo ""

for package in "${PACKAGES[@]}"; do
    echo "📦 Deploying $package..."
    echo "------------------------"
    
    cd "packages/$package"
    
    # Check if git is already initialized
    if [ ! -d ".git" ]; then
        echo "  Initializing git repository..."
        git init
    fi
    
    # Add all files
    echo "  Adding files to git..."
    git add .
    
    # Check if there are changes to commit
    if git diff --staged --quiet; then
        echo "  No changes to commit for $package"
    else
        # Commit changes
        echo "  Committing changes..."
        git commit -m "Deploy $package for AWS production"
        
        # Add remote origin (if not already added)
        if ! git remote get-url origin >/dev/null 2>&1; then
            echo "  Adding remote origin..."
            git remote add origin "https://github.com/$ORG/$package.git"
        fi
        
        # Push to GitHub
        echo "  Pushing to GitHub..."
        git branch -M main
        git push -u origin main
        
        if [ $? -eq 0 ]; then
            echo "  ✅ $package deployed successfully!"
        else
            echo "  ❌ Failed to deploy $package"
            echo "     You may need to create the repository on GitHub first:"
            echo "     https://github.com/$ORG/$package"
        fi
    fi
    
    echo ""
    cd ../..
done

echo "🎉 Deployment process completed!"
echo ""
echo "Next steps:"
echo "1. Verify all repositories exist on GitHub"
echo "2. Run ./verify-github-packages.sh again"
echo "3. If verification passes, run ./switch-all-packages.sh prod"
echo ""
