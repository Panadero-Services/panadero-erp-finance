# Generic Filters Guide

## Overview

The generic filters system provides a reusable, configurable filtering solution for any entity in your application. It eliminates code duplication and provides consistent filtering behavior across all components.

## Architecture

### 1. **Generic Components** (Reusable)
- `FilterCard.vue` - Individual filter item component
- `FilterSection.vue` - Container for multiple filter cards
- `useGenericFilters.js` - Core filtering logic

### 2. **Entity-Specific Wrappers** (Optional)
- `useProductFiltersGeneric.js` - Product-specific wrapper
- `useCustomerFilters.js` - Customer-specific wrapper
- Any other entity-specific wrappers

## Usage Patterns

### Pattern 1: Direct Generic Usage (Recommended)

```javascript
import { useGenericFilters } from '../composables/useGenericFilters.js'

// Define filter configurations
const filterConfigs = [
  {
    key: 'status',                    // Unique identifier
    field: 'status',                  // Data field to filter on
    label: 'Status',                  // Display label
    pluralLabel: 'Statuses',          // Plural form for "All" option
    searchable: true,                 // Include in search
    getValue: (item) => item.status,  // Custom value extraction
    getIcon: (value) => getIcon(value) // Custom icon function
  },
  {
    key: 'category',
    field: 'category.name',
    label: 'Category',
    pluralLabel: 'Categories',
    searchable: true,
    getValue: (item) => item.category?.name,
    getIcon: () => 'fas fa-tag'
  }
]

// Use the composable
const {
  filterStates,
  searchQuery,
  allFilterItems,
  filteredData,
  stats,
  selectFilter,
  clearAllFilters
} = useGenericFilters(data, filterConfigs)
```

### Pattern 2: Entity-Specific Wrapper

```javascript
import { useProductFiltersGeneric } from '../composables/useProductFiltersGeneric.js'

// Simple usage with predefined configurations
const {
  selectedProductType,
  selectedProductGroup,
  selectedBrand,
  productTypeItems,
  productGroupItems,
  brandItems,
  filteredProducts,
  selectProductType,
  selectProductGroup,
  selectBrand
} = useProductFiltersGeneric(erpProducts, productTypes, productGroups, brands)
```

## Filter Configuration Options

| Option | Type | Required | Description |
|--------|------|----------|-------------|
| `key` | String | ✅ | Unique identifier for the filter |
| `field` | String | ✅ | Data field to filter on (dot notation supported) |
| `label` | String | ✅ | Display label for the filter |
| `pluralLabel` | String | ✅ | Plural form for "All" option |
| `searchable` | Boolean | ❌ | Include in search functionality |
| `getValue` | Function | ❌ | Custom value extraction function |
| `getIcon` | Function | ❌ | Custom icon function |

## Template Usage

```vue
<template>
  <!-- Single filter section -->
  <FilterSection
    title="Filter by Status"
    :items="statusItems"
    :selected-value="selectedStatus"
    @select="selectStatus"
  />

  <!-- Multiple filter sections -->
  <FilterSection
    title="Filter by Category"
    :items="categoryItems"
    :selected-value="selectedCategory"
    grid-class="grid grid-cols-2 md:grid-cols-4 gap-3"
    @select="selectCategory"
  />

  <!-- Custom grid layout -->
  <FilterSection
    title="Filter by Brand"
    :items="brandItems"
    :selected-value="selectedBrand"
    grid-class="grid grid-cols-1 gap-2"
    @select="selectBrand"
  />
</template>
```

## Advanced Examples

### Custom Value Extraction

```javascript
const filterConfigs = [
  {
    key: 'fullName',
    field: 'name',
    label: 'Name',
    pluralLabel: 'Names',
    searchable: true,
    getValue: (item) => `${item.first_name} ${item.last_name}`,
    getIcon: () => 'fas fa-user'
  }
]
```

### Nested Object Filtering

```javascript
const filterConfigs = [
  {
    key: 'department',
    field: 'user.department.name',
    label: 'Department',
    pluralLabel: 'Departments',
    searchable: true,
    getValue: (item) => item.user?.department?.name,
    getIcon: (dept) => getDepartmentIcon(dept)
  }
]
```

### Dynamic Icon Assignment

```javascript
const filterConfigs = [
  {
    key: 'priority',
    field: 'priority',
    label: 'Priority',
    pluralLabel: 'Priorities',
    searchable: false,
    getValue: (item) => item.priority,
    getIcon: (priority) => {
      const icons = {
        'high': 'fas fa-exclamation-triangle text-red-500',
        'medium': 'fas fa-exclamation-circle text-yellow-500',
        'low': 'fas fa-info-circle text-green-500'
      }
      return icons[priority] || 'fas fa-circle'
    }
  }
]
```

## Benefits

### ✅ **Reusability**
- Same components work for any entity
- Consistent behavior across the application
- Easy to add new filter types

### ✅ **Maintainability**
- Single source of truth for filter logic
- Changes propagate to all entities
- Reduced code duplication

### ✅ **Flexibility**
- Configurable via filter configurations
- Custom value extraction and icon functions
- Support for nested object properties

### ✅ **Performance**
- Efficient filtering with computed properties
- Minimal re-renders
- Optimized for large datasets

## Migration Guide

### From Product-Specific to Generic

1. **Replace the import:**
   ```javascript
   // Old
   import { useProductFilters } from '../composables/useProductFilters.js'
   
   // New
   import { useProductFiltersGeneric } from '../composables/useProductFiltersGeneric.js'
   ```

2. **Update the composable call:**
   ```javascript
   // Old
   const { ... } = useProductFilters(erpProducts, productTypes, productGroups, brands)
   
   // New
   const { ... } = useProductFiltersGeneric(erpProducts, productTypes, productGroups, brands)
   ```

3. **No template changes needed** - the API remains the same!

### Creating New Entity Filters

1. **Create filter configurations:**
   ```javascript
   const filterConfigs = [
     {
       key: 'status',
       field: 'status',
       label: 'Status',
       pluralLabel: 'Statuses',
       searchable: true,
       getValue: (item) => item.status,
       getIcon: (status) => getStatusIcon(status)
     }
   ]
   ```

2. **Use the generic composable:**
   ```javascript
   const {
     filterStates,
     allFilterItems,
     filteredData,
     selectFilter
   } = useGenericFilters(data, filterConfigs)
   ```

3. **Create computed properties for convenience:**
   ```javascript
   const selectedStatus = computed({
     get: () => filterStates.value.status,
     set: (value) => selectFilter('status', value)
   })
   ```

## Best Practices

1. **Use descriptive keys** - `productType` instead of `type`
2. **Provide meaningful labels** - "Product Type" instead of "Type"
3. **Include searchable filters** - Only mark filters that make sense to search
4. **Use consistent icons** - Follow the same icon pattern across entities
5. **Handle null values** - Always provide fallbacks for optional fields
6. **Test filter combinations** - Ensure multiple filters work together correctly

## Troubleshooting

### Common Issues

1. **Filters not updating** - Check if `getValue` function returns the correct value
2. **Icons not showing** - Verify `getIcon` function returns valid CSS classes
3. **Search not working** - Ensure `searchable: true` is set for relevant filters
4. **Performance issues** - Consider debouncing search input for large datasets

### Debug Tips

```javascript
// Add debugging to see filter states
console.log('Filter States:', filterStates.value)
console.log('Filter Items:', allFilterItems.value)
console.log('Filtered Data:', filteredData.value)
```
