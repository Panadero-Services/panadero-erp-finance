#!/bin/bash

# GitHub Package Verification Script
# Verifies that all packages are deployed to GitHub before switching to external mode

echo "🔍 Verifying GitHub packages for AWS deployment..."
echo ""

# Packages that will be switched to external mode
PACKAGES=(
    "panadero-erp-inventory"
    "panadero-erp-finance" 
    "panadero-erp-hr"
    "panadero-erp-compliance"
    "panadero-workflow"
)

# Results storage
declare -A results
all_good=true

echo "Testing GitHub repositories..."
echo "================================"

for package in "${PACKAGES[@]}"; do
    echo -n "Testing $package... "
    
    # Test repository access
    status_code=$(curl -s -o /dev/null -w "%{http_code}" "https://github.com/Panadero-Services/$package")
    results[$package]=$status_code
    
    case $status_code in
        200)
            echo "✅ EXISTS"
            ;;
        404)
            echo "❌ NOT FOUND"
            all_good=false
            ;;
        403)
            echo "🔒 PRIVATE/ACCESS DENIED"
            all_good=false
            ;;
        *)
            echo "⚠️  ERROR ($status_code)"
            all_good=false
            ;;
    esac
done

echo ""
echo "================================"
echo "VERIFICATION RESULTS:"
echo ""

if [ "$all_good" = true ]; then
    echo "✅ ALL PACKAGES VERIFIED!"
    echo "   All repositories exist and are accessible."
    echo ""
    echo "OPTIONS:"
    echo "1) Switch to external mode (recommended for AWS deployment)"
    echo "2) Show detailed results"
    echo "3) Test package installation"
    echo "4) Exit"
    echo ""
    read -p "Choose option (1-4): " choice
    
    case $choice in
        1)
            echo ""
            echo "🔄 Switching to external mode..."
            ./switch-all-packages.sh prod
            ;;
        2)
            echo ""
            echo "📊 DETAILED RESULTS:"
            for package in "${PACKAGES[@]}"; do
                echo "  $package: ${results[$package]}"
            done
            ;;
        3)
            echo ""
            echo "🧪 Testing package installation..."
            echo "Testing panadero-erp-inventory installation..."
            npm install git+https://github.com/Panadero-Services/panadero-erp-inventory.git --dry-run
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
else
    echo "❌ VERIFICATION FAILED!"
    echo "   Some packages are not accessible on GitHub."
    echo ""
    echo "DETAILED RESULTS:"
    for package in "${PACKAGES[@]}"; do
        case ${results[$package]} in
            200)
                echo "  ✅ $package: EXISTS"
                ;;
            404)
                echo "  ❌ $package: NOT FOUND - Repository doesn't exist"
                ;;
            403)
                echo "  🔒 $package: PRIVATE - Repository is private or access denied"
                ;;
            *)
                echo "  ⚠️  $package: ERROR (${results[$package]})"
                ;;
        esac
    done
    echo ""
    echo "OPTIONS:"
    echo "1) Deploy missing packages to GitHub first"
    echo "2) Switch anyway (may cause deployment issues)"
    echo "3) Stay in development mode"
    echo "4) Exit"
    echo ""
    read -p "Choose option (1-4): " choice
    
    case $choice in
        1)
            echo ""
            echo "📦 You need to deploy these packages to GitHub first:"
            for package in "${PACKAGES[@]}"; do
                if [ "${results[$package]}" != "200" ]; then
                    echo "  - $package (Status: ${results[$package]})"
                fi
            done
            echo ""
            echo "After deploying, run this script again."
            ;;
        2)
            echo ""
            echo "⚠️  WARNING: Switching with missing packages may cause deployment issues!"
            read -p "Are you sure? (y/N): " confirm
            if [[ $confirm =~ ^[Yy]$ ]]; then
                ./switch-all-packages.sh prod
            else
                echo "Cancelled."
            fi
            ;;
        3)
            echo "Staying in development mode. No changes made."
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
fi
