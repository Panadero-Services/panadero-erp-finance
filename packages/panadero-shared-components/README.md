# Panadero Shared Components Package

**Version:** 1.0.3  
**Release Date:** 16 September 2025  
**Status:** Production Ready

## 🚀 Overview

The Panadero Shared Components Package provides reusable UI components that are used across all Panadero packages. It includes framework settings panels and other shared UI elements.

## ✨ Features

### Core Components
- **FrameworkSettingsPanel** - Global settings management component
- **GenericInfoSection** - Reusable information display component with dynamic scaling
- **Consistent Styling** - Uses panadero-shared-styling for consistent appearance
- **Event Handling** - Proper event emission for parent components

### Component Features
- Font size adjustment with reactive scaling
- Theme selection (light/dark/auto)
- Language selection
- Animation toggle
- Compact mode toggle
- Settings reset functionality
- Dynamic font scaling across all text elements

## �� Installation

```bash
npm install panadero-shared-components
```

## 📖 Usage

### Basic Import
```javascript
import { FrameworkSettingsPanel, GenericInfoSection } from 'panadero-shared-components'
```

### Component Usage
```vue
<template>
  <FrameworkSettingsPanel @settingsChanged="handleSettingsChanged" />
  <GenericInfoSection 
    :package-config="packageConfig"
    :module-description="moduleDescription"
    :version-updates="versionUpdates"
    :package-tables="packageTables"
    :shared-entities="sharedEntities"
  />
</template>

<script setup>
import { FrameworkSettingsPanel, GenericInfoSection } from 'panadero-shared-components'

const handleSettingsChanged = (settings) => {
  console.log('Settings changed:', settings)
}
</script>
```

## 🎨 Styling

The component automatically uses the panadero-shared-styling package for consistent appearance and responsive scaling. All text elements dynamically scale based on the global font size setting.

## 🔄 Version History

### v1.0.3 (16 September 2025)
- ✅ Enhanced GenericInfoSection with improved reactivity
- ✅ Fixed scaling issues in shared components
- ✅ Improved dynamic font scaling performance
- ✅ Better error handling and fallbacks

### v1.0.2 (16 September 2025)
- ✅ Fixed GenericInfoSection scaling reactivity
- ✅ Corrected :style binding pattern for dynamic font scaling
- ✅ Added proper scaling support for all text elements
- ✅ Improved component consistency across packages

### v1.0.1 (15 September 2025)
- ✅ Initial release with FrameworkSettingsPanel
- ✅ Basic shared component structure

## �� License

MIT License - see LICENSE file for details