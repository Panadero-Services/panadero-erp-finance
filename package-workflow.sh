#!/bin/bash

# Complete package workflow management
PACKAGE=$1
ACTION=$2
BRANCH=$3

if [ -z "$PACKAGE" ] || [ -z "$ACTION" ]; then
    echo "🚀 Package Workflow Manager"
    echo ""
    echo "Usage: ./package-workflow.sh <package> <action> [branch]"
    echo ""
    echo "Actions:"
    echo "  dev <branch>     - Start development on branch (create if needed)"
    echo "  commit <message> - Commit changes with message"
    echo "  push             - Push current branch to GitHub"
    echo "  merge <branch>   - Merge branch to main and deploy"
    echo "  status           - Show current status"
    echo ""
    echo "Examples:"
    echo "  ./package-workflow.sh panadero-erp-inventory dev feature/new-button"
    echo "  ./package-workflow.sh panadero-erp-inventory commit 'Add new button'"
    echo "  ./package-workflow.sh panadero-erp-inventory push"
    echo "  ./package-workflow.sh panadero-erp-inventory merge feature/new-button"
    exit 1
fi

PACKAGE_DIR="packages/$PACKAGE"

if [ ! -d "$PACKAGE_DIR" ]; then
    echo "❌ Package $PACKAGE not found in packages/"
    exit 1
fi

cd "$PACKAGE_DIR"

case "$ACTION" in
    "dev")
        if [ -z "$BRANCH" ]; then
            echo "❌ Please specify branch name"
            exit 1
        fi
        
        echo "🚀 Starting development on $PACKAGE..."
        
        # Switch to dev mode for this package
        cd ../..
        sed -i '' "s|\"$PACKAGE\": \"git+https://github.com/.*\"|\"$PACKAGE\": \"file:packages/$PACKAGE\"|g" package.json
        sed -i '' "s|\"$PACKAGE\": \"github:.*\"|\"$PACKAGE\": \"file:packages/$PACKAGE\"|g" package.json
        npm install
        
        # Go back to package and create/switch branch
        cd "$PACKAGE_DIR"
        git checkout -b "$BRANCH" 2>/dev/null || git checkout "$BRANCH"
        
        echo "✅ Development mode active for $PACKAGE on branch $BRANCH"
        echo "   Changes will appear instantly!"
        ;;
        
    "commit")
        if [ -z "$BRANCH" ]; then
            echo "❌ Please specify commit message"
            exit 1
        fi
        
        echo "💾 Committing changes to $PACKAGE..."
        git add .
        git commit -m "$BRANCH"
        echo "✅ Changes committed"
        ;;
        
    "push")
        echo "📤 Pushing $PACKAGE to GitHub..."
        git push origin $(git branch --show-current)
        echo "✅ Pushed to GitHub"
        ;;
        
    "merge")
        if [ -z "$BRANCH" ]; then
            echo "❌ Please specify branch to merge"
            exit 1
        fi
        
        echo "🔀 Merging $BRANCH to main and deploying..."
        
        # Merge to main
        git checkout main
        git merge "$BRANCH"
        git push origin main
        
        # Switch back to production mode
        cd ../..
        sed -i '' "s|\"$PACKAGE\": \"file:packages/$PACKAGE\"|\"$PACKAGE\": \"git+https://github.com/Panadero-Services/$PACKAGE.git\"|g" package.json
        npm install
        
        echo "✅ Branch merged and deployed to production!"
        ;;
        
    "status")
        echo "📊 Status for $PACKAGE:"
        echo "  Current branch: $(git branch --show-current)"
        echo "  Uncommitted changes: $(git status --porcelain | wc -l) files"
        echo "  Last commit: $(git log -1 --oneline)"
        ;;
        
    *)
        echo "❌ Unknown action: $ACTION"
        exit 1
        ;;
esac

cd - > /dev/null





