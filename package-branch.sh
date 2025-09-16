#!/bin/bash

# Package branch management script
PACKAGE=$1
ACTION=$2
BRANCH=$3

if [ -z "$PACKAGE" ] || [ -z "$ACTION" ]; then
    echo "Usage: ./package-branch.sh <package> <action> [branch]"
    echo ""
    echo "Actions:"
    echo "  create <branch>  - Create new branch for package"
    echo "  switch <branch>  - Switch to existing branch"
    echo "  merge <branch>   - Merge branch to main"
    echo "  status           - Show current branch status"
    echo "  list             - List all branches"
    echo ""
    echo "Example:"
    echo "  ./package-branch.sh panadero-erp-inventory create feature/new-button"
    echo "  ./package-branch.sh panadero-erp-inventory switch feature/new-button"
    echo "  ./package-branch.sh panadero-erp-inventory merge feature/new-button"
    exit 1
fi

PACKAGE_DIR="packages/$PACKAGE"

if [ ! -d "$PACKAGE_DIR" ]; then
    echo "❌ Package $PACKAGE not found in packages/"
    exit 1
fi

cd "$PACKAGE_DIR"

case "$ACTION" in
    "create")
        if [ -z "$BRANCH" ]; then
            echo "❌ Please specify branch name"
            exit 1
        fi
        echo "🌿 Creating new branch: $BRANCH"
        git checkout -b "$BRANCH"
        echo "✅ Branch $BRANCH created and switched to"
        ;;
        
    "switch")
        if [ -z "$BRANCH" ]; then
            echo "❌ Please specify branch name"
            exit 1
        fi
        echo "🔄 Switching to branch: $BRANCH"
        git checkout "$BRANCH"
        echo "✅ Switched to branch $BRANCH"
        ;;
        
    "merge")
        if [ -z "$BRANCH" ]; then
            echo "❌ Please specify branch name to merge"
            exit 1
        fi
        echo "🔀 Merging branch $BRANCH to main..."
        git checkout main
        git merge "$BRANCH"
        git push origin main
        echo "✅ Branch $BRANCH merged to main and pushed"
        ;;
        
    "status")
        echo "📊 Current branch status for $PACKAGE:"
        echo "  Current branch: $(git branch --show-current)"
        echo "  Status: $(git status --porcelain | wc -l) files changed"
        ;;
        
    "list")
        echo "🌿 Available branches for $PACKAGE:"
        git branch -a
        ;;
        
    *)
        echo "❌ Unknown action: $ACTION"
        exit 1
        ;;
esac

cd - > /dev/null





