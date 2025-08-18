# 🎨 Dark Mode Implementation Guide

## Overview

The ERP Finance Module now includes a **centralized dark mode system** that provides consistent theming across all components. This system eliminates the need to manually add dark mode classes throughout your code.

## 🚀 Quick Start

### Import the Dark Mode Utilities

```javascript
import { darkMode, darkModeUtils, darkModeClasses } from 'panadero-erp-finance';
```

### Use Pre-built Class Combinations

```vue
<template>
  <!-- Main page container with dark mode -->
  <div :class="darkModeClasses.pageContainer">
    
    <!-- Card container with dark mode -->
    <div :class="darkModeClasses.cardContainer">
      <h2 :class="darkModeClasses.text.primary">Title</h2>
    </div>
    
    <!-- Table with dark mode -->
    <div :class="darkModeClasses.tableContainer">
      <!-- Table content -->
    </div>
    
    <!-- Form input with dark mode -->
    <input :class="darkModeClasses.formInput" />
    
    <!-- Buttons with dark mode -->
    <button :class="darkModeClasses.buttonPrimary">Primary Action</button>
    <button :class="darkModeClasses.buttonSecondary">Secondary Action</button>
    
    <!-- Status badges with dark mode -->
    <span :class="darkModeClasses.statusSuccess">Success</span>
    <span :class="darkModeClasses.statusWarning">Warning</span>
    <span :class="darkModeClasses.statusError">Error</span>
  </div>
</template>
```

## 📚 Available Utilities

### 1. Pre-built Class Combinations (`darkModeClasses`)

```javascript
// Main containers
darkModeClasses.pageContainer        // Main page container
darkModeClasses.cardContainer        // Card container
darkModeClasses.tableContainer       // Table container
darkModeClasses.modalContainer       // Modal container
darkModeClasses.filterSection        // Filter section
darkModeClasses.dashboardCard        // Dashboard card

// Form elements
darkModeClasses.formInput            // Form input styling

// Buttons
darkModeClasses.buttonPrimary        // Primary button
darkModeClasses.buttonSecondary      // Secondary button

// Status indicators
darkModeClasses.statusSuccess        // Success status
darkModeClasses.statusWarning        // Warning status
darkModeClasses.statusError          // Error status

// Summary elements
darkModeClasses.summaryCard          // Summary card
```

### 2. Utility Functions (`darkModeUtils`)

```javascript
// Get specific component classes
darkModeUtils.getContainer('main')           // dark:bg-gray-900
darkModeUtils.getContainer('secondary')      // dark:bg-gray-800
darkModeUtils.getContainer('card')           // dark:bg-gray-800 dark:shadow-gray-900/50

darkModeUtils.getText('primary')             // dark:text-white
darkModeUtils.getText('secondary')           // dark:text-gray-100
darkModeUtils.getText('muted')               // dark:text-gray-300

darkModeUtils.getBorder('primary')           // dark:border-gray-700
darkModeUtils.getBorder('secondary')         // dark:border-gray-600

darkModeUtils.getInput()                     // Complete input styling
darkModeUtils.getButton('primary')           // Primary button styling
darkModeUtils.getButton('success')           // Success button styling
darkModeUtils.getButton('warning')           // Warning button styling
darkModeUtils.getButton('danger')            // Danger button styling

// Get complete component sets
darkModeUtils.getTable()                     // All table classes
darkModeUtils.getSummary()                   // All summary card classes
darkModeUtils.getModal()                     // All modal classes
darkModeUtils.getFilter()                    // All filter classes
darkModeUtils.getDashboard()                 // All dashboard classes
```

### 3. Individual Classes (`darkMode`)

```javascript
// Container backgrounds
darkMode.container.main              // dark:bg-gray-900
darkMode.container.secondary         // dark:bg-gray-800
darkMode.container.tertiary          // dark:bg-gray-700
darkMode.container.card              // dark:bg-gray-800 dark:shadow-gray-900/50

// Text colors
darkMode.text.primary                // dark:text-white
darkMode.text.secondary              // dark:text-gray-100
darkMode.text.tertiary               // dark:text-gray-200
darkMode.text.muted                  // dark:text-gray-300
darkMode.text.disabled               // dark:text-gray-400

// Border colors
darkMode.border.primary              // dark:border-gray-700
darkMode.border.secondary            // dark:border-gray-600
darkMode.border.subtle               // dark:border-gray-500

// Form inputs
darkMode.input.background            // dark:bg-gray-700
darkMode.input.text                  // dark:text-gray-100
darkMode.input.border                // dark:border-gray-600
darkMode.input.placeholder           // dark:text-gray-400
darkMode.input.focus                 // dark:focus:border-blue-400

// Buttons
darkMode.button.primary              // dark:bg-indigo-600 dark:text-white dark:hover:bg-indigo-700
darkMode.button.secondary            // dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600
darkMode.button.success              // dark:bg-green-600 dark:text-white dark:hover:bg-green-700
darkMode.button.warning              // dark:bg-yellow-600 dark:text-white dark:hover:bg-yellow-700
darkMode.button.danger               // dark:bg-red-600 dark:text-white dark:hover:bg-red-700

// Tables
darkMode.table.container             // dark:bg-gray-800 dark:border-gray-700
darkMode.table.header                // dark:bg-gray-700 dark:border-gray-600
darkMode.table.headerText            // dark:text-gray-200
darkMode.table.row                   // dark:text-gray-100 dark:border-gray-600
darkMode.table.rowHover              // dark:hover:bg-gray-700

// Status badges
darkMode.status.success              // dark:bg-green-900 dark:text-green-200
darkMode.status.warning              // dark:bg-yellow-900 dark:text-yellow-200
darkMode.status.error                // dark:bg-red-900 dark:text-red-200
darkMode.status.info                 // dark:bg-blue-900 dark:text-blue-200

// Summary cards
darkMode.summary.container           // dark:bg-gray-800 dark:shadow-gray-900/50
darkMode.summary.title               // dark:text-gray-300
darkMode.summary.value               // dark:text-white
darkMode.summary.positive            // dark:text-green-400
darkMode.summary.negative            // dark:text-red-400
darkMode.summary.warning             // dark:text-yellow-400

// Navigation
darkMode.navigation.tab              // dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700
darkMode.navigation.tabActive        // dark:text-white dark:bg-gray-700
darkMode.navigation.menu             // dark:bg-gray-800 dark:border-gray-700

// Info boxes
darkMode.infoBox.container           // dark:bg-gray-800 dark:border-gray-700
darkMode.infoBox.header              // dark:text-white
darkMode.infoBox.content             // dark:text-gray-200
darkMode.infoBox.item                // dark:hover:bg-gray-700
darkMode.infoBox.button              // dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600

// Modals
darkMode.modal.overlay               // dark:bg-black dark:opacity-75
darkMode.modal.container             // dark:bg-gray-900 dark:from-gray-600/50 dark:to-gray-900/50
darkMode.modal.header                // dark:text-gray-300
darkMode.modal.body                  // dark:text-gray-100
darkMode.modal.footer                // dark:border-gray-700

// Filters
darkMode.filter.container            // dark:bg-gray-800 dark:border-gray-700
darkMode.filter.label                // dark:text-gray-200
darkMode.filter.input                // dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600
darkMode.filter.select               // dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600

// Dashboard
darkMode.dashboard.container         // dark:bg-gray-900
darkMode.dashboard.card              // dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/50
darkMode.dashboard.metric            // dark:text-white
darkMode.dashboard.label             // dark:text-gray-300
darkMode.dashboard.chart             // dark:bg-gray-800 dark:border-gray-700
```

## 🎯 Common Use Cases

### 1. Component Container

```vue
<template>
  <div :class="`p-6 ${darkModeClasses.pageContainer}`">
    <!-- Component content -->
  </div>
</template>
```

### 2. Card Layout

```vue
<template>
  <div :class="`rounded-xl border p-4 ${darkModeClasses.cardContainer}`">
    <h3 :class="`text-lg font-semibold ${darkMode.text.primary}`">Title</h3>
    <p :class="`text-sm ${darkMode.text.muted}`">Description</p>
  </div>
</template>
```

### 3. Table Layout

```vue
<template>
  <div :class="`overflow-x-auto ${darkModeClasses.tableContainer}`">
    <table class="w-full border-collapse">
      <thead>
        <tr :class="darkMode.table.header">
          <th :class="`p-3 border ${darkMode.table.headerText} ${darkMode.border.secondary}`">Header</th>
        </tr>
      </thead>
      <tbody>
        <tr :class="`hover:bg-gray-50 ${darkMode.table.row}`">
          <td :class="`p-3 border ${darkMode.border.secondary}`">Content</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
```

### 4. Form Layout

```vue
<template>
  <div :class="`space-y-4 ${darkModeClasses.filterSection}`">
    <div>
      <label :class="`block text-sm font-medium ${darkMode.filter.label}`">Label</label>
      <input :class="`mt-1 block w-full rounded-md ${darkModeClasses.formInput}`" />
    </div>
  </div>
</template>
```

### 5. Button Layout

```vue
<template>
  <div class="flex gap-2">
    <button :class="darkModeClasses.buttonPrimary">Save</button>
    <button :class="darkModeClasses.buttonSecondary">Cancel</button>
    <button :class="darkModeClasses.buttonDanger">Delete</button>
  </div>
</template>
```

### 6. Status Indicators

```vue
<template>
  <div class="space-x-2">
    <span :class="`px-2 py-1 text-xs rounded ${darkModeClasses.statusSuccess}`">Active</span>
    <span :class="`px-2 py-1 text-xs rounded ${darkModeClasses.statusWarning}`">Pending</span>
    <span :class="`px-2 py-1 text-xs rounded ${darkModeClasses.statusError}`">Error</span>
  </div>
</template>
```

### 7. Summary Cards

```vue
<template>
  <div :class="`grid grid-cols-4 gap-4`">
    <div :class="`p-4 rounded-lg ${darkModeClasses.summaryCard}`">
      <h4 :class="`text-sm font-medium ${darkMode.summary.title}`">Total Revenue</h4>
      <p :class="`text-2xl font-bold ${darkMode.summary.positive}`">$50,000</p>
    </div>
  </div>
</template>
```

## 🔧 Customization

### Adding New Dark Mode Classes

```javascript
// In darkMode.js
export const darkMode = {
  // ... existing classes
  
  // Add new category
  custom: {
    special: 'dark:bg-purple-900 dark:text-purple-100',
    accent: 'dark:bg-pink-800 dark:text-pink-200'
  }
};

// Add to utility functions
export const darkModeUtils = {
  // ... existing functions
  
  getCustom: (type = 'special') => darkMode.custom[type]
};

// Add to pre-built classes
export const darkModeClasses = {
  // ... existing classes
  
  customSpecial: darkMode.custom.special,
  customAccent: darkMode.custom.accent
};
```

### Extending Existing Classes

```javascript
// Combine multiple classes
const customContainer = `${darkMode.container.main} ${darkMode.border.primary} p-6`;

// Use with conditional logic
const dynamicClasses = computed(() => {
  return [
    darkModeClasses.cardContainer,
    isActive.value ? darkMode.border.accent : darkMode.border.subtle
  ].join(' ');
});
```

## 📱 Responsive Dark Mode

### Conditional Dark Mode

```vue
<template>
  <div :class="[
    'p-6',
    isDarkMode ? darkModeClasses.pageContainer : 'bg-white'
  ]">
    <!-- Content -->
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { darkModeClasses } from 'panadero-erp-finance';

const isDarkMode = ref(false);

// Toggle dark mode
function toggleDarkMode() {
  isDarkMode.value = !isDarkMode.value;
}
</script>
```

### CSS Variables Integration

```css
/* In your CSS */
:root {
  --bg-primary: #ffffff;
  --text-primary: #111827;
  --border-primary: #d1d5db;
}

.dark {
  --bg-primary: #111827;
  --text-primary: #ffffff;
  --border-primary: #374151;
}

/* Use with dark mode utilities */
.custom-component {
  background-color: var(--bg-primary);
  color: var(--text-primary);
  border-color: var(--border-primary);
}
```

## 🎨 Color Palette Reference

### Background Colors
- **Primary**: `dark:bg-gray-900` (Very dark)
- **Secondary**: `dark:bg-gray-800` (Dark)
- **Tertiary**: `dark:bg-gray-700` (Medium dark)
- **Card**: `dark:bg-gray-800` with shadow

### Text Colors
- **Primary**: `dark:text-white` (Main text)
- **Secondary**: `dark:text-gray-100` (Body text)
- **Tertiary**: `dark:text-gray-200` (Labels)
- **Muted**: `dark:text-gray-300` (Subtle text)
- **Disabled**: `dark:text-gray-400` (Disabled text)

### Border Colors
- **Primary**: `dark:border-gray-700` (Main borders)
- **Secondary**: `dark:border-gray-600` (Subtle borders)
- **Subtle**: `dark:border-gray-500` (Very subtle)

### Status Colors
- **Success**: `dark:bg-green-900 dark:text-green-200`
- **Warning**: `dark:bg-yellow-900 dark:text-yellow-200`
- **Error**: `dark:bg-red-900 dark:text-red-200`
- **Info**: `dark:bg-blue-900 dark:text-blue-200`

## 🚀 Migration Guide

### From Manual Dark Mode Classes

**Before:**
```vue
<div class="bg-white dark:bg-gray-900 border dark:border-gray-700">
  <h2 class="text-gray-900 dark:text-white">Title</h2>
</div>
```

**After:**
```vue
<div :class="`border ${darkModeClasses.pageContainer}`">
  <h2 :class="darkMode.text.primary">Title</h2>
</div>
```

### From Inline Dark Mode

**Before:**
```vue
<button class="px-4 py-2 bg-indigo-600 text-white dark:bg-indigo-700 dark:hover:bg-indigo-800">
  Action
</button>
```

**After:**
```vue
<button :class="`px-4 py-2 ${darkModeClasses.buttonPrimary}`">
  Action
</button>
```

## ✨ Best Practices

1. **Use Pre-built Classes**: Start with `darkModeClasses` for common patterns
2. **Leverage Utility Functions**: Use `darkModeUtils` for complex combinations
3. **Access Individual Classes**: Use `darkMode` for specific styling needs
4. **Maintain Consistency**: Always use the centralized system for dark mode
5. **Document Custom Classes**: Add new classes to the system when needed
6. **Test Both Themes**: Ensure components look good in light and dark modes

## 🔍 Troubleshooting

### Common Issues

1. **Classes Not Applied**: Ensure Tailwind CSS is properly configured
2. **Dark Mode Not Working**: Check if `dark:` prefix is enabled in Tailwind
3. **Inconsistent Styling**: Use the centralized system instead of manual classes
4. **Performance Issues**: Classes are optimized and should not impact performance

### Debug Mode

```javascript
// Enable debug mode to see applied classes
console.log('Dark Mode Classes:', darkModeClasses);
console.log('Utility Functions:', darkModeUtils);
console.log('Individual Classes:', darkMode);
```

---

**🎉 Congratulations!** You now have a comprehensive, centralized dark mode system that makes implementing consistent theming across your ERP Finance Module a breeze!

