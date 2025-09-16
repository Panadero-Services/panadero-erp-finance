# Commands Cheat Sheet - Created Today

## 🚀 Quick Reference - All Commands

### Package Mode Switching
```bash
# Switch ALL packages to development mode (local files)
./switch-all-packages.sh dev

# Switch ALL packages to production mode (GitHub)
./switch-all-packages.sh prod

# Check status of all packages
./switch-all-packages.sh status
```

### Individual Package Workflow
```bash
# Start development on new branch (auto-switches to dev mode)
./package-workflow.sh panadero-erp-inventory dev feature/new-feature

# Commit changes with message
./package-workflow.sh panadero-erp-inventory commit "Add new button feature"

# Push current branch to GitHub
./package-workflow.sh panadero-erp-inventory push

# Merge branch to main and deploy (auto-switches to prod mode)
./package-workflow.sh panadero-erp-inventory merge feature/new-feature

# Check package status
./package-workflow.sh panadero-erp-inventory status
```

### Basic Branch Operations
```bash
# Create new branch
./package-branch.sh panadero-erp-inventory create feature/new-button

# Switch to existing branch
./package-branch.sh panadero-erp-inventory switch feature/new-button

# Merge branch to main
./package-branch.sh panadero-erp-inventory merge feature/new-button

# Check branch status
./package-branch.sh panadero-erp-inventory status

# List all branches
./package-branch.sh panadero-erp-inventory list
```

### Peer Dependencies
```bash
# Fix peer dependencies for all packages
./fix-peer-deps.sh
```

### Legacy Commands (from earlier)
```bash
# Simple package update (legacy)
./update-package.sh

# Simple mode switching (3 packages only)
./switch-mode.sh dev
./switch-mode.sh prod
```

## 📦 Managed Packages

- `panadero-erp-inventory`
- `panadero-erp-finance`
- `panadero-erp-hr`
- `panadero-erp-compliance`
- `panadero-workflow`

## 🎯 Most Common Workflows

### 1. Start New Feature
```bash
./package-workflow.sh panadero-erp-inventory dev feature/my-feature
# Make changes (instant feedback)
./package-workflow.sh panadero-erp-inventory commit "Description"
./package-workflow.sh panadero-erp-inventory push
./package-workflow.sh panadero-erp-inventory merge feature/my-feature
```

### 2. Quick Fix
```bash
./switch-all-packages.sh dev
# Make changes (instant feedback)
cd packages/panadero-erp-inventory
git add . && git commit -m "Fix" && git push origin main
cd ../..
./switch-all-packages.sh prod
```

### 3. Check Everything
```bash
./switch-all-packages.sh status
./package-workflow.sh panadero-erp-inventory status
```

## 🔧 File Locations

- **Scripts:** Root directory (`/Users/lib/Dropbox/wwwdevelop/sites/i3v2-bots/`)
- **Packages:** `packages/` directory
- **Documentation:** `PACKAGE_WORKFLOW_DOCUMENTATION.md`

## ⚡ Quick Tips

- **Development:** Always use `dev` mode for instant feedback
- **Production:** Use `prod` mode for stable deployments
- **Branches:** Create feature branches for new work
- **Commits:** Make small, focused commits
- **Status:** Check status before making changes

---

**Created:** September 13, 2025  
**Total Scripts:** 6  
**Total Commands:** 20+





