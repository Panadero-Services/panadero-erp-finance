# ERP Module Standardization Guide

This document defines the standard approach, structure, and styling for ALL ERP modules in the Panadero system. Follow these guidelines to ensure consistency across all modules.

## 🏗️ Module Architecture

### Standard Module Structure
```
packages/panadero-erp-[module-name]/
├── src/
│   ├── components/           # Vue components
│   ├── composables/          # Vue composables
│   ├── stores/               # Pinia stores
│   └── types/                # TypeScript definitions
├── tests/                    # Test files
├── package.json              # Package configuration
├── index.js                  # Main exports
├── README.md                 # Module documentation
├── HowTo.md                  # User guide
├── StandardizeInstructions.md # This file (copy to each module)
├── .gitignore                # Git exclusions
└── deploy.sh                 # Deployment script
```

### Naming Convention
- **Package name**: `panadero-erp-[module-name]` (kebab-case)
- **Repository**: `https://github.com/Panadero-Services/panadero-erp-[module-name].git`
- **Component files**: PascalCase (e.g., `GeneralLedger.vue`)
- **Composable files**: camelCase with `use` prefix (e.g., `useGeneralLedger.js`)

## 🎨 UI/UX Standards

### Title Structure (REQUIRED)
Every submodule MUST use this exact title structure:

```vue
<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-semibold">Module Name</h2>
      <div class="flex gap-2">
        <!-- Action buttons go here -->
        <button class="px-3 py-2 rounded bg-indigo-600 text-white">Primary Action</button>
        <button class="px-3 py-2 rounded bg-gray-100">Secondary Action</button>
      </div>
    </div>
    
    <!-- Module content goes here -->
  </div>
</template>
```

### Button Standards
```vue
<!-- Primary actions (main functionality) -->
<button class="px-3 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">
  Primary Action
</button>

<!-- Secondary actions (supporting functionality) -->
<button class="px-3 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200">
  Secondary Action
</button>

<!-- Destructive actions (delete, remove) -->
<button class="px-3 py-2 rounded bg-red-600 text-white hover:bg-red-700">
  Delete
</button>

<!-- Disabled state -->
<button class="px-3 py-2 rounded bg-gray-300 text-gray-500 cursor-not-allowed" disabled>
  Disabled Action
</button>
```

### Card Standards
```vue
<!-- Standard card container -->
<div class="bg-white rounded border p-4">
  <h3 class="font-semibold text-gray-900 mb-3">Card Title</h3>
  <!-- Card content -->
</div>

<!-- Card with hover effect -->
<div class="bg-white rounded border p-4 hover:shadow-md transition-shadow">
  <!-- Card content -->
</div>
```

### Table Standards
```vue
<table class="w-full text-sm">
  <thead>
    <tr class="bg-gray-50 text-left">
      <th class="p-2 border">Header 1</th>
      <th class="p-2 border">Header 2</th>
    </tr>
  </thead>
  <tbody>
    <tr v-for="item in items" :key="item.id">
      <td class="p-2 border">{{ item.field1 }}</td>
      <td class="p-2 border">{{ item.field2 }}</td>
    </tr>
    <tr v-if="items.length === 0">
      <td class="p-6 text-center text-gray-500 italic border" colspan="2">
        No data available
      </td>
    </tr>
  </tbody>
</table>
```

### Form Standards
```vue
<form @submit.prevent="handleSubmit" class="space-y-4">
  <div class="form-group">
    <label class="block text-sm font-medium text-gray-700 mb-1">Field Label</label>
    <input 
      v-model="form.field" 
      type="text" 
      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
      required 
    />
  </div>
  
  <div class="flex gap-3">
    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
      Submit
    </button>
    <button type="button" @click="cancel" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
      Cancel
    </button>
  </div>
</form>
```

## 📱 Responsive Design Standards

### Grid Layouts
```vue
<!-- Responsive grid that works on all screen sizes -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
  <!-- Grid items -->
</div>

<!-- Dashboard-style layout -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
  <!-- KPI cards -->
</div>
```

### Responsive Text
```vue
<!-- Responsive heading sizes -->
<h3 class="text-sm md:text-base font-semibold text-gray-900">Responsive Title</h3>

<!-- Responsive button text -->
<button class="text-xs md:text-sm text-indigo-600">Responsive Button</button>
```

## 🔧 Technical Standards

### Vue 3 Composition API
```vue
<script setup>
import { ref, computed, onMounted } from 'vue';
import { useModuleStore } from '../stores/moduleStore';

// Always use composables for business logic
const { data, actions } = useModuleStore();

// Reactive data
const isLoading = ref(false);
const formData = ref({});

// Computed properties
const filteredData = computed(() => {
  return data.value.filter(item => item.active);
});

// Lifecycle
onMounted(async () => {
  await actions.loadData();
});

// Methods
async function handleSubmit() {
  try {
    isLoading.value = true;
    await actions.saveData(formData.value);
    // Success handling
  } catch (error) {
    // Error handling
    console.error('Error:', error);
  } finally {
    isLoading.value = false;
  }
}
</script>
```

### Pinia Store Pattern
```javascript
// stores/moduleStore.js
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useModuleStore = defineStore('module', () => {
  // State
  const items = ref([]);
  const isLoading = ref(false);
  const error = ref(null);

  // Getters
  const activeItems = computed(() => items.value.filter(item => item.active));
  const itemCount = computed(() => items.value.length);

  // Actions
  async function loadItems() {
    try {
      isLoading.value = true;
      error.value = null;
      // API call or data loading logic
      items.value = await fetchItems();
    } catch (err) {
      error.value = err.message;
    } finally {
      isLoading.value = false;
    }
  }

  async function saveItem(item) {
    // Save logic
  }

  async function deleteItem(id) {
    // Delete logic
  }

  return {
    // State
    items,
    isLoading,
    error,
    
    // Getters
    activeItems,
    itemCount,
    
    // Actions
    loadItems,
    saveItem,
    deleteItem
  };
});
```

### Composable Pattern
```javascript
// composables/useModule.js
import { computed } from 'vue';
import { useModuleStore } from '../stores/moduleStore';

export function useModule() {
  const store = useModuleStore();

  // Expose store state and actions
  return {
    // State
    items: computed(() => store.items),
    isLoading: computed(() => store.isLoading),
    error: computed(() => store.error),
    
    // Actions
    loadItems: store.loadItems,
    saveItem: store.saveItem,
    deleteItem: store.deleteItem
  };
}
```

## 📦 Package Standards

### package.json Template
```json
{
  "name": "panadero-erp-[module-name]",
  "version": "1.0.0",
  "description": "ERP [Module Name] Module for comprehensive [functionality] management",
  "main": "index.js",
  "type": "module",
  "scripts": {
    "test": "echo \"Error: no test specified\" && exit 1",
    "dev": "echo \"Development mode\"",
    "build": "echo \"Build complete\"",
    "lint": "echo \"Linting complete\""
  },
  "keywords": [
    "erp",
    "[module-name]",
    "[functionality]"
  ],
  "author": "JaWsome.Orbit",
  "license": "MIT",
  "dependencies": {
    "vue": "^3.0.0",
    "pinia": "^2.0.0"
  },
  "peerDependencies": {
    "vue": "^3.0.0"
  },
  "repository": {
    "type": "git",
    "url": "https://github.com/Panadero-Services/panadero-erp-[module-name].git"
  },
  "bugs": {
    "url": "https://github.com/Panadero-Services/panadero-erp-[module-name]/issues"
  },
  "homepage": "https://github.com/Panadero-Services/panadero-erp-[module-name]#readme"
}
```

### index.js Export Pattern
```javascript
// Main entry point - export all components, composables, and stores
export { default as ModuleName } from './src/components/ModuleName.vue';
export { default as SubModule1 } from './src/components/SubModule1.vue';
export { default as SubModule2 } from './src/components/SubModule2.vue';

// Export composables
export { useModuleName } from './src/composables/useModuleName.js';
export { useSubModule1 } from './src/composables/useSubModule1.js';

// Export stores
export { useModuleStore } from './src/stores/moduleStore.js';

// Package information
export const packageInfo = {
  name: 'panadero-erp-[module-name]',
  version: '1.0.0',
  description: 'ERP [Module Name] Module for comprehensive [functionality] management',
  author: 'JaWsome.Orbit',
  components: [
    'ModuleName',
    'SubModule1',
    'SubModule2'
  ]
};
```

## 📚 Documentation Standards

### README.md Structure
```markdown
# Panadero ERP [Module Name] Module

Brief description of what the module does.

## 🚀 Quick Start

### Installation
```bash
# Clone or copy to your project's packages directory
packages/panadero-erp-[module-name]/
```

### Usage
```vue
<script setup>
import ModuleName from '../../../../packages/panadero-erp-[module-name]/src/components/ModuleName.vue';
</script>

<template>
  <ModuleName />
</template>
```

## ✨ Features
- Feature 1: Description
- Feature 2: Description
- Feature 3: Description

## 📁 Package Structure
```
packages/panadero-erp-[module-name]/
├── src/
│   ├── components/           # Vue components
│   ├── composables/          # Vue composables
│   ├── stores/               # Pinia stores
│   └── types/                # TypeScript definitions
├── tests/                    # Test files
├── package.json              # Package configuration
├── index.js                  # Main exports
├── README.md                 # This file
└── HowTo.md                  # User guide
```

## 🔧 Development

### Prerequisites
- Vue.js 3.x
- Pinia 2.x
- Tailwind CSS (for styling)

### Local Development
```bash
cd packages/panadero-erp-[module-name]
npm install
npm run dev
```

## 📄 License
MIT License - see LICENSE file for details

## 🆘 Support
- Create an issue on GitHub
- Check the HowTo.md for user guides
```

## 🚀 Deployment Standards

### deploy.sh Template
```bash
#!/bin/bash

# Panadero ERP [Module Name] Package Deployment Script
set -e

echo "🚀 Deploying Panadero ERP [Module Name] Package..."

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
```

## ✅ Checklist for New Modules

Before creating a new ERP module, ensure you have:

- [ ] **Package Structure**: All required directories and files
- [ ] **Title Standardization**: All submodules use the exact title structure
- [ ] **Button Standards**: Consistent button styling and behavior
- [ ] **Card Standards**: Consistent card layouts and styling
- [ ] **Table Standards**: Consistent table structure and styling
- [ ] **Form Standards**: Consistent form layouts and validation
- [ ] **Responsive Design**: Mobile-first approach with proper breakpoints
- [ ] **Vue 3 Composition API**: Modern Vue patterns
- [ ] **Pinia Store**: Proper state management
- [ ] **Composables**: Business logic separation
- [ ] **Package.json**: Correct metadata and dependencies
- [ ] **Index.js**: Proper exports
- [ ] **Documentation**: README.md and HowTo.md
- [ ] **Deployment**: deploy.sh script
- [ ] **Git Setup**: Proper .gitignore and repository structure

## 🔄 Module Integration

### Main Project Import Pattern
```vue
<!-- In your main module wrapper -->
<script setup>
import ModuleName from '../../../../packages/panadero-erp-[module-name]/src/components/ModuleName.vue';
</script>

<template>
  <ModuleName />
</template>
```

### Tab Integration
```javascript
// Add to your main module tabs array
const tabs = [
  { id: 'dashboard', label: 'Dashboard', icon: 'fas fa-gauge', component: null },
  { id: 'module-name', label: 'Module Name', icon: 'fas fa-icon', component: ModuleName },
  // ... other tabs
];
```

## 📝 Notes

- **Always copy this file** to new modules as `StandardizeInstructions.md`
- **Follow the exact patterns** shown above - no deviations
- **Test responsive design** on multiple screen sizes
- **Ensure accessibility** with proper ARIA labels and keyboard navigation
- **Use consistent error handling** across all modules
- **Maintain consistent loading states** and user feedback

---

**Remember**: Consistency is key to a professional ERP system. Every module should look and feel like it belongs to the same system.
