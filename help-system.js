#!/usr/bin/env node

// Help System for i3v2-bots Package Management
console.log(`
🚀 i3v2-bots Package Management Help System
==========================================

📦 NPM SCRIPTS (run with: npm run <command>)
--------------------------------------------
  help           Show this help message
  dev            Start development server
  build          Build for production
  packages:dev   Switch all packages to development mode
  packages:prod  Switch all packages to production mode
  packages:status Check status of all packages
  deps:fix       Fix peer dependencies for all packages

🌿 PACKAGE WORKFLOW (run with: ./package-workflow.sh <package> <action>)
------------------------------------------------------------------------
  dev <branch>   Start development on new branch (auto-switches to dev mode)
  commit <msg>   Commit changes with message
  push           Push current branch to GitHub
  merge <branch> Merge branch to main and deploy (auto-switches to prod mode)
  status         Check package status

📋 BRANCH MANAGEMENT (run with: ./package-branch.sh <package> <action>)
------------------------------------------------------------------------
  create <branch> Create new branch
  switch <branch> Switch to existing branch
  merge <branch>  Merge branch to main
  status          Check branch status
  list            List all branches

🔄 MODE SWITCHING (run with: ./switch-all-packages.sh <mode>)
-------------------------------------------------------------
  dev            Switch all packages to development mode (local files)
  prod           Switch all packages to production mode (GitHub)
  status         Check current mode of all packages

📦 MANAGED PACKAGES
------------------
  • panadero-erp-inventory
  • panadero-erp-finance
  • panadero-erp-hr
  • panadero-erp-compliance
  • panadero-workflow

🎯 QUICK EXAMPLES
-----------------
  # Start new feature
  npm run packages:dev
  ./package-workflow.sh panadero-erp-inventory dev feature/new-button
  
  # Make changes (instant feedback)
  # ... edit files ...
  
  # Commit and deploy
  ./package-workflow.sh panadero-erp-inventory commit "Add new button"
  ./package-workflow.sh panadero-erp-inventory push
  ./package-workflow.sh panadero-erp-inventory merge feature/new-button
  
  # Quick fix
  npm run packages:dev
  # ... make changes ...
  cd packages/panadero-erp-inventory && git add . && git commit -m "Fix" && git push origin main
  cd ../.. && npm run packages:prod
  
  # Check everything
  npm run packages:status
  ./package-workflow.sh panadero-erp-inventory status

📚 DOCUMENTATION
----------------
  • PACKAGE_WORKFLOW_DOCUMENTATION.md - Complete documentation
  • COMMANDS_CHEAT_SHEET.md - Quick reference

💡 TIPS
-------
  • Always use 'dev' mode for development (instant feedback)
  • Use 'prod' mode for stable deployments
  • Create feature branches for new work
  • Make small, focused commits
  • Check status before making changes

🔧 TROUBLESHOOTING
------------------
  • Script not found? Make sure you're in the root directory
  • Changes not appearing? Check if in dev mode
  • Peer dependency warnings? Run: npm run deps:fix
  • Git issues? Check branch status first

For detailed documentation, see: PACKAGE_WORKFLOW_DOCUMENTATION.md
`);

// Check if specific help requested
const args = process.argv.slice(2);
if (args.length > 0) {
    const command = args[0];
    
    switch (command) {
        case 'workflow':
            console.log(`
🌿 PACKAGE WORKFLOW DETAILED HELP
=================================
Usage: ./package-workflow.sh <package> <action> [options]

Examples:
  ./package-workflow.sh panadero-erp-inventory dev feature/new-button
  ./package-workflow.sh panadero-erp-inventory commit "Add new feature"
  ./package-workflow.sh panadero-erp-inventory push
  ./package-workflow.sh panadero-erp-inventory merge feature/new-button
  ./package-workflow.sh panadero-erp-inventory status
            `);
            break;
            
        case 'branch':
            console.log(`
📋 BRANCH MANAGEMENT DETAILED HELP
==================================
Usage: ./package-branch.sh <package> <action> [branch]

Examples:
  ./package-branch.sh panadero-erp-inventory create feature/new-button
  ./package-branch.sh panadero-erp-inventory switch feature/new-button
  ./package-branch.sh panadero-erp-inventory merge feature/new-button
  ./package-branch.sh panadero-erp-inventory status
  ./package-branch.sh panadero-erp-inventory list
            `);
            break;
            
        case 'packages':
            console.log(`
🔄 PACKAGE MODE SWITCHING DETAILED HELP
=======================================
Usage: ./switch-all-packages.sh <mode>

Modes:
  dev     - Switch all packages to development mode (local files)
  prod    - Switch all packages to production mode (GitHub)
  status  - Check current mode of all packages

Examples:
  ./switch-all-packages.sh dev
  ./switch-all-packages.sh prod
  ./switch-all-packages.sh status
            `);
            break;
            
        default:
            console.log(`\n❌ Unknown help topic: ${command}`);
            console.log(`Available topics: workflow, branch, packages`);
            console.log(`Use: npm run help <topic>`);
    }
}





