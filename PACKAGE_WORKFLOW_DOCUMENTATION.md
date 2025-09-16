# Package Workflow Documentation

## Overview

This documentation explains the complete package management system for the i3v2-bots project. The system provides smooth development workflows with dual-mode switching (local development vs GitHub production) and comprehensive branch management.

## 🚀 Quick Start

### Prerequisites
- All packages are located in `packages/` directory
- Each package has its own Git repository
- Main project uses npm for package management

### Available Scripts

| Script | Purpose | Usage |
|--------|---------|-------|
| `switch-all-packages.sh` | Switch all packages between dev/prod modes | `./switch-all-packages.sh [dev\|prod\|status]` |
| `package-workflow.sh` | Complete workflow for individual packages | `./package-workflow.sh <package> <action> [options]` |
| `package-branch.sh` | Basic branch operations | `./package-branch.sh <package> <action> [branch]` |
| `fix-peer-deps.sh` | Fix peer dependencies across all packages | `./fix-peer-deps.sh` |

## 📦 Package Management

### Dual-Mode System

The system operates in two modes:

#### Development Mode (Local)
- Uses `file:packages/package-name` in package.json
- Changes appear instantly with hot reload
- No commits needed for testing
- Perfect for rapid development

#### Production Mode (GitHub)
- Uses `git+https://github.com/Panadero-Services/package-name.git`
- Stable, deployed versions
- Used for production and sharing

### Switching Modes

#### Switch All Packages
```bash
# Switch all packages to development mode
./switch-all-packages.sh dev

# Switch all packages to production mode
./switch-all-packages.sh prod

# Check current status of all packages
./switch-all-packages.sh status
```

#### Current Package Status
```bash
./switch-all-packages.sh status
```

**Output:**
```
📊 Current package status:
  📦 panadero-erp-inventory: LOCAL (dev mode)
  📦 panadero-erp-finance: GITHUB (prod mode)
  📦 panadero-erp-hr: LOCAL (dev mode)
  📦 panadero-erp-compliance: LOCAL (dev mode)
  📦 panadero-workflow: LOCAL (dev mode)
```

## 🌿 Branch Management

### Complete Workflow for Individual Packages

#### 1. Start Development
```bash
./package-workflow.sh panadero-erp-inventory dev feature/new-feature
```

**What this does:**
- Creates new branch `feature/new-feature` (or switches to existing)
- Switches package to development mode (local files)
- Installs local package
- Ready for instant development

#### 2. Make Changes
- Edit files in `packages/panadero-erp-inventory/src/`
- Changes appear instantly in browser (hot reload)
- No commits needed for testing

#### 3. Commit Changes
```bash
./package-workflow.sh panadero-erp-inventory commit "Add new button feature"
```

**What this does:**
- Adds all changes to Git
- Commits with provided message
- Keeps you on development branch

#### 4. Push to GitHub
```bash
./package-workflow.sh panadero-erp-inventory push
```

**What this does:**
- Pushes current branch to GitHub
- Creates pull request link (if new branch)
- Preserves development mode

#### 5. Deploy to Production
```bash
./package-workflow.sh panadero-erp-inventory merge feature/new-feature
```

**What this does:**
- Switches to main branch
- Merges feature branch to main
- Pushes main to GitHub
- Switches package to production mode
- Installs from GitHub

### Basic Branch Operations

#### Create New Branch
```bash
./package-branch.sh panadero-erp-inventory create feature/new-button
```

#### Switch to Existing Branch
```bash
./package-branch.sh panadero-erp-inventory switch feature/new-button
```

#### Merge Branch to Main
```bash
./package-branch.sh panadero-erp-inventory merge feature/new-button
```

#### Check Branch Status
```bash
./package-branch.sh panadero-erp-inventory status
```

#### List All Branches
```bash
./package-branch.sh panadero-erp-inventory list
```

## 🔧 Package Configuration

### Managed Packages

The system currently manages these packages:

- `panadero-erp-inventory`
- `panadero-erp-finance`
- `panadero-erp-hr`
- `panadero-erp-compliance`
- `panadero-workflow`

### Peer Dependencies

#### Fix Peer Dependencies
```bash
./fix-peer-deps.sh
```

**What this does:**
- Installs common peer dependencies (vue, pinia, axios, decimal.js)
- Updates each package's package.json with consistent peer dependencies
- Prevents peer dependency warnings

#### Common Peer Dependencies
- `vue@^3.0.0`
- `pinia@^2.0.0`
- `axios@^1.0.0`
- `decimal.js@^10.0.0`

## 📋 Complete Development Workflow

### Example: Adding a New Feature

1. **Start Development**
   ```bash
   ./package-workflow.sh panadero-erp-inventory dev feature/settings-button
   ```

2. **Make Changes**
   - Edit files in `packages/panadero-erp-inventory/src/`
   - Test changes instantly in browser

3. **Commit Progress**
   ```bash
   ./package-workflow.sh panadero-erp-inventory commit "Add settings button component"
   ```

4. **Push to GitHub**
   ```bash
   ./package-workflow.sh panadero-erp-inventory push
   ```

5. **Continue Development**
   - Make more changes
   - Commit as needed
   - Push updates

6. **Deploy When Ready**
   ```bash
   ./package-workflow.sh panadero-erp-inventory merge feature/settings-button
   ```

### Example: Quick Fix

1. **Switch to Dev Mode**
   ```bash
   ./switch-all-packages.sh dev
   ```

2. **Make Quick Fix**
   - Edit files directly
   - Test immediately

3. **Deploy Fix**
   ```bash
   cd packages/panadero-erp-inventory
   git add .
   git commit -m "Fix button styling"
   git push origin main
   cd ../..
   ./switch-all-packages.sh prod
   ```

## 🛠️ Troubleshooting

### Common Issues

#### Package Not Found
```
❌ Package panadero-erp-inventory not found in packages/
```
**Solution:** Ensure package exists in `packages/` directory

#### Git Repository Not Found
```
ERROR: Repository not found.
```
**Solution:** Ensure GitHub repository exists and is accessible

#### Changes Not Appearing
**Solution:** 
1. Check if in development mode: `./switch-all-packages.sh status`
2. Restart dev server if needed
3. Clear browser cache

#### Peer Dependency Warnings
**Solution:** Run `./fix-peer-deps.sh`

### Debug Commands

#### Check Package Status
```bash
./switch-all-packages.sh status
```

#### Check Individual Package
```bash
./package-workflow.sh panadero-erp-inventory status
```

#### Check Git Status
```bash
cd packages/panadero-erp-inventory
git status
git log --oneline -5
```

## 📁 File Structure

```
i3v2-bots/
├── packages/
│   ├── panadero-erp-inventory/
│   ├── panadero-erp-finance/
│   ├── panadero-erp-hr/
│   ├── panadero-erp-compliance/
│   └── panadero-workflow/
├── switch-all-packages.sh
├── package-workflow.sh
├── package-branch.sh
├── fix-peer-deps.sh
└── package.json
```

## 🎯 Best Practices

### Development
1. Always start with `./package-workflow.sh <package> dev <branch>`
2. Make small, focused commits
3. Test changes before committing
4. Use descriptive branch names

### Production
1. Only merge tested features
2. Use meaningful commit messages
3. Keep main branch stable
4. Tag releases when appropriate

### Git Workflow
1. Create feature branches for new work
2. Commit frequently with clear messages
3. Push branches for backup
4. Merge when feature is complete

## 🔄 Migration from Old Workflow

### Before (Complex)
1. Edit files
2. Update package.json version
3. Commit to GitHub
4. Update main project package.json
5. npm install
6. Restart dev server
7. Test changes

### After (Simple)
1. `./package-workflow.sh <package> dev <branch>`
2. Edit files (instant feedback)
3. `./package-workflow.sh <package> commit "message"`
4. `./package-workflow.sh <package> merge <branch>`

## 📊 Benefits

### Development Speed
- ✅ Instant feedback with hot reload
- ✅ No complex version management
- ✅ One-command operations
- ✅ Clean Git workflow

### Reliability
- ✅ Consistent package management
- ✅ Automatic mode switching
- ✅ Peer dependency handling
- ✅ Error prevention

### Collaboration
- ✅ Standardized workflow
- ✅ Clear branch management
- ✅ Easy deployment process
- ✅ Comprehensive documentation

---

**Last Updated:** September 13, 2025  
**Version:** 1.0.0  
**Author:** AI Assistant





