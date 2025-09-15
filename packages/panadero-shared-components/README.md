# Panadero Shared Components Package

**Version:** 1.0.1 bots
**Release Date:** 15 September 2025  
**Status:** Production Ready

## 🚀 Overview

The Panadero Shared Components Package provides reusable UI components that are used across all Panadero packages. It includes framework settings panels and other shared UI elements.

## ✨ Features

### Core Components
- **FrameworkSettingsPanel** - Global settings management component
- **Consistent Styling** - Uses panadero-shared-styling for consistent appearance
- **Event Handling** - Proper event emission for parent components

### Component Features
- Font size adjustment
- Theme selection (light/dark/auto)
- Language selection
- Animation toggle
- Compact mode toggle
- Settings reset functionality

## 📦 Installation

```bash
npm install panadero-shared-components
```

## 📖 Usage

### Basic Import
```javascript
import { FrameworkSettingsPanel } from 'panadero-shared-components'
```

### Component Usage
```vue
<template>
  <FrameworkSettingsPanel @settingsChanged="handleSettingsChanged" />
</template>

<script setup>
import { FrameworkSettingsPanel } from 'panadero-shared-components'

const handleSettingsChanged = (settings) => {
  console.log('Settings changed:', settings)
}
</script>
```

## 🎨 Styling

The component automatically uses the panadero-shared-styling package for consistent appearance and responsive scaling.

## 📝 License

MIT License - see LICENSE file for details





