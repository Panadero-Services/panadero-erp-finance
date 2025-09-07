# Data Configuration Files

This directory contains JSON configuration files that separate data from logic in the ERP Finance Module.

## Files

### `versions.json`
**Purpose**: Centralized version history and release information
**Usage**: Imported by `useInfoBoxes.js` and `versionManager.js`

**Structure**:
```json
{
  "current": "1.0.9",
  "releases": [
    {
      "version": "v1.0.9",
      "date": "19 Aug 2025", 
      "status": "current|maintenance|eol",
      "features": ["feature 1", "feature 2"]
    }
  ]
}
```

**Status Types**:
- `current` - Currently supported and actively developed
- `maintenance` - Receiving security and critical bug fixes only
- `eol` - End of life, no longer supported

### `rules.json`
**Purpose**: Development rules and guidelines for Cursor/Agent instructions
**Usage**: Reference file for AI assistants and developers

**Structure**:
```json
{
  "development_rules": {
    "vue_standards": { /* Vue.js coding standards */ },
    "code_quality": { /* Code quality requirements */ },
    "styling": { /* Styling guidelines */ },
    "workflow_system": { /* Workflow-specific rules */ }
  },
  "version_management": { /* Version control procedures */ },
  "communication": { /* User interaction guidelines */ },
  "file_structure": { /* Project organization */ },
  "testing": { /* Testing requirements */ },
  "forbidden_actions": { /* Things to never do */ },
  "emergency_procedures": { /* Crisis management */ }
}
```

## Usage Examples

### In Components
```javascript
import { getCurrentVersion, getLatestFeatures } from '../utils/versionManager.js';

const currentVersion = getCurrentVersion(); // "1.0.9"
const features = getLatestFeatures(); // Array of latest features
```

### In Composables
```javascript
import versionsData from '../data/versions.json';

const versionUpdates = computed(() => versionsData.releases);
```

### For AI Assistant Instructions
```javascript
import { getDevelopmentRules, getForbiddenActions } from '../utils/versionManager.js';

const rules = getDevelopmentRules();
const forbidden = getForbiddenActions();
```

## Benefits

1. **Separation of Concerns**: Data is separate from business logic
2. **Easy Maintenance**: Update versions without touching code
3. **Consistency**: Single source of truth for version information
4. **AI Assistant Guidelines**: Clear rules for automated development
5. **Type Safety**: JSON structure validation
6. **Performance**: Static imports are optimized by bundlers

## Updating Versions

When releasing a new version:

1. Update `versions.json` with new release information
2. Set previous current version status to "maintenance" 
3. Update `current` field to new version number
4. The `versionManager.js` utility handles the rest automatically

## Rule Categories

### Vue Standards
- SFC structure requirements
- Component naming conventions  
- Props and emits patterns

### Code Quality
- No breaking changes policy
- DRY principle enforcement
- Error handling requirements

### Styling
- Tailwind CSS usage guidelines
- Dark mode implementation
- Dynamic scaling requirements

### Workflow System
- Component reusability standards
- Event handling patterns
- Modal layout specifications

### Emergency Procedures
- Breaking functionality recovery
- User frustration management
- Rollback procedures

## Integration

These files are automatically imported by:
- `src/composables/useInfoBoxes.js` - Version display
- `src/utils/versionManager.js` - Version utilities
- Development tools and AI assistants

No manual synchronization required - updates to JSON files are reflected immediately.
