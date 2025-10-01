# Cascading Filters Guide

## Overview

The cascading filters system provides **intelligent, dependent filtering** where selecting a parent filter automatically resets and filters child filters. This creates a logical, progressive filtering experience without hardcoding entity-specific logic.

## Key Features

- ✅ **Generic & Reusable** - Works for any entity (products, orders, customers, etc.)
- ✅ **Configurable Dependencies** - Define filter relationships via configuration
- ✅ **Automatic Reset Logic** - Parent filters reset dependent filters
- ✅ **Smart Filtering** - Child filters only show options available for current parent selection
- ✅ **No Hardcoding** - All logic is driven by configuration objects

## Architecture

### 1. **Core Composable** (`useCascadingFilters.js`)
- Generic filtering logic that works with any entity
- Handles dependency resolution and cascading resets
- Provides filtered data and filter options

### 2. **Entity-Specific Wrappers** (Optional)
- `useProductCascadingFilters.js` - Product-specific wrapper
- Any other entity-specific wrappers as needed

### 3. **Filter Dependencies Configuration**
- Defines which filters depend on which others
- Specifies which filters to reset when parent changes
- Completely configurable per entity

## Usage Patterns

### Pattern 1: Direct Generic Usage (Most Flexible)

```javascript
import { useCascadingFilters } from '../composables/useCascadingFilters.js'

// Define filter configurations
const filterConfigs = [
  {
    key: 'status',
    field: 'status',
    label: 'Status',
    pluralLabel: 'Statuses',
    searchable: true,
    getValue: (item) => item.status,
    getIcon: (status) => getStatusIcon(status)
  },
  {
    key: 'priority',
    field: 'priority',
    label: 'Priority',
    pluralLabel: 'Priorities',
    searchable: true,
    getValue: (item) => item.priority,
    getIcon: (priority) => getPriorityIcon(priority)
  },
  {
    key: 'assignee',
    field: 'assignee.name',
    label: 'Assignee',
    pluralLabel: 'Assignees',
    searchable: true,
    getValue: (item) => item.assignee?.name,
    getIcon: () => 'fas fa-user'
  }
]

// Define filter dependencies
const dependencies = [
  {
    filterKey: 'status',        // When Status changes
    dependsOn: [],              // It doesn't depend on any other filter
    resets: ['priority', 'assignee'] // Reset Priority and Assignee
  },
  {
    filterKey: 'priority',      // When Priority changes
    dependsOn: ['status'],      // It depends on Status selection
    resets: ['assignee']        // Reset Assignee only
  },
  {
    filterKey: 'assignee',      // When Assignee changes
    dependsOn: ['status', 'priority'], // It depends on both Status and Priority
    resets: []                  // No dependent filters to reset
  }
]

// Use the composable
const {
  filterStates,
  allFilterItems,
  filteredData,
  selectFilter
} = useCascadingFilters(data, filterConfigs, dependencies)
```

### Pattern 2: Entity-Specific Wrapper (Convenience)

```javascript
import { useProductCascadingFilters } from '../composables/useProductCascadingFilters.js'

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
} = useProductCascadingFilters(erpProducts, productTypes, productGroups, brands)
```

## Filter Dependencies Configuration

### Dependency Object Structure

```javascript
{
  filterKey: 'childFilter',     // The filter that has dependencies
  dependsOn: ['parent1', 'parent2'], // Filters this one depends on
  resets: ['grandchild1', 'grandchild2'] // Filters to reset when this changes
}
```

### Common Dependency Patterns

#### **Linear Chain** (A → B → C)
```javascript
const dependencies = [
  { filterKey: 'category', dependsOn: [], resets: ['subcategory', 'brand'] },
  { filterKey: 'subcategory', dependsOn: ['category'], resets: ['brand'] },
  { filterKey: 'brand', dependsOn: ['category', 'subcategory'], resets: [] }
]
```

#### **Tree Structure** (A → B, A → C)
```javascript
const dependencies = [
  { filterKey: 'status', dependsOn: [], resets: ['priority', 'assignee'] },
  { filterKey: 'priority', dependsOn: ['status'], resets: [] },
  { filterKey: 'assignee', dependsOn: ['status'], resets: [] }
]
```

#### **Complex Dependencies** (A → B → C, A → D)
```javascript
const dependencies = [
  { filterKey: 'region', dependsOn: [], resets: ['country', 'city', 'department'] },
  { filterKey: 'country', dependsOn: ['region'], resets: ['city', 'department'] },
  { filterKey: 'city', dependsOn: ['region', 'country'], resets: ['department'] },
  { filterKey: 'department', dependsOn: ['region'], resets: [] }
]
```

## How Cascading Works

### 1. **Filter Selection**
When a user selects a filter value:
1. The filter state is updated
2. The system checks for dependencies
3. Dependent filters are automatically reset to 'all'
4. Filter options are recalculated based on new context

### 2. **Option Filtering**
Child filter options are automatically filtered to show only values that exist in the current parent context:
- If Product Type = 'bulk', Product Groups only shows groups that have bulk products
- If Product Group = 'Steel', Brands only shows brands that have steel products

### 3. **Visual Feedback**
- Disabled options are marked with `disabled: true`
- Counts reflect available options in current context
- Empty states are handled gracefully

## Template Usage

```vue
<template>
  <!-- Parent filter - resets children when changed -->
  <FilterSection
    title="Product Type"
    :items="productTypeItems"
    :selected-value="selectedProductType"
    @select="selectProductType"
  />

  <!-- Child filter - only shows options available for selected type -->
  <FilterSection
    title="Product Group"
    :items="productGroupItems"
    :selected-value="selectedProductGroup"
    @select="selectProductGroup"
  />

  <!-- Grandchild filter - only shows options available for selected type + group -->
  <FilterSection
    title="Brand"
    :items="brandItems"
    :selected-value="selectedBrand"
    @select="selectBrand"
  />
</template>
```

## Advanced Examples

### E-commerce Product Filters

```javascript
const productFilterConfigs = [
  {
    key: 'category',
    field: 'category.name',
    label: 'Category',
    pluralLabel: 'Categories',
    searchable: true,
    getValue: (product) => product.category?.name,
    getIcon: (category) => getCategoryIcon(category)
  },
  {
    key: 'subcategory',
    field: 'subcategory.name',
    label: 'Subcategory',
    pluralLabel: 'Subcategories',
    searchable: true,
    getValue: (product) => product.subcategory?.name,
    getIcon: () => 'fas fa-tag'
  },
  {
    key: 'brand',
    field: 'brand.name',
    label: 'Brand',
    pluralLabel: 'Brands',
    searchable: true,
    getValue: (product) => product.brand?.name,
    getIcon: () => 'fas fa-tag'
  },
  {
    key: 'priceRange',
    field: 'price',
    label: 'Price Range',
    pluralLabel: 'Price Ranges',
    searchable: false,
    getValue: (product) => getPriceRange(product.price),
    getIcon: (range) => getPriceRangeIcon(range)
  }
]

const productDependencies = [
  {
    filterKey: 'category',
    dependsOn: [],
    resets: ['subcategory', 'brand', 'priceRange']
  },
  {
    filterKey: 'subcategory',
    dependsOn: ['category'],
    resets: ['brand', 'priceRange']
  },
  {
    filterKey: 'brand',
    dependsOn: ['category', 'subcategory'],
    resets: ['priceRange']
  },
  {
    filterKey: 'priceRange',
    dependsOn: ['category', 'subcategory', 'brand'],
    resets: []
  }
]
```

### Project Management Filters

```javascript
const projectFilterConfigs = [
  {
    key: 'status',
    field: 'status',
    label: 'Status',
    pluralLabel: 'Statuses',
    searchable: true,
    getValue: (project) => project.status,
    getIcon: (status) => getStatusIcon(status)
  },
  {
    key: 'priority',
    field: 'priority',
    label: 'Priority',
    pluralLabel: 'Priorities',
    searchable: true,
    getValue: (project) => project.priority,
    getIcon: (priority) => getPriorityIcon(priority)
  },
  {
    key: 'assignee',
    field: 'assignee.name',
    label: 'Assignee',
    pluralLabel: 'Assignees',
    searchable: true,
    getValue: (project) => project.assignee?.name,
    getIcon: () => 'fas fa-user'
  },
  {
    key: 'dueDate',
    field: 'due_date',
    label: 'Due Date',
    pluralLabel: 'Due Dates',
    searchable: false,
    getValue: (project) => getDateRange(project.due_date),
    getIcon: (range) => getDateRangeIcon(range)
  }
]

const projectDependencies = [
  {
    filterKey: 'status',
    dependsOn: [],
    resets: ['priority', 'assignee', 'dueDate']
  },
  {
    filterKey: 'priority',
    dependsOn: ['status'],
    resets: ['assignee', 'dueDate']
  },
  {
    filterKey: 'assignee',
    dependsOn: ['status', 'priority'],
    resets: ['dueDate']
  },
  {
    filterKey: 'dueDate',
    dependsOn: ['status', 'priority', 'assignee'],
    resets: []
  }
]
```

## Benefits

### ✅ **Logical Filtering**
- Users can't select invalid combinations
- Clear progression from broad to specific
- Prevents empty result sets

### ✅ **Better UX**
- Automatic reset prevents confusion
- Visual feedback on available options
- Intuitive filter flow

### ✅ **Data Integrity**
- Ensures filter combinations make sense
- Prevents impossible filter states
- Maintains data consistency

### ✅ **Reusability**
- Same system works for any entity
- Configurable dependencies
- No hardcoded logic

### ✅ **Maintainability**
- Single source of truth for filter logic
- Easy to add new filters or dependencies
- Clear separation of concerns

## Migration Guide

### From Simple Filters to Cascading Filters

1. **Identify Dependencies**
   - Which filters should reset others?
   - Which filters depend on others for their options?

2. **Define Dependencies Array**
   ```javascript
   const dependencies = [
     { filterKey: 'parent', dependsOn: [], resets: ['child1', 'child2'] },
     { filterKey: 'child1', dependsOn: ['parent'], resets: [] },
     { filterKey: 'child2', dependsOn: ['parent'], resets: [] }
   ]
   ```

3. **Update Composable Usage**
   ```javascript
   // Old
   const { ... } = useGenericFilters(data, filterConfigs)
   
   // New
   const { ... } = useCascadingFilters(data, filterConfigs, dependencies)
   ```

4. **Test Filter Combinations**
   - Verify parent filters reset children
   - Check that child options are filtered correctly
   - Ensure no invalid combinations are possible

## Best Practices

1. **Keep Dependencies Simple** - Avoid overly complex dependency chains
2. **Provide Clear Labels** - Users should understand the filter hierarchy
3. **Handle Empty States** - Show helpful messages when no options are available
4. **Test All Combinations** - Ensure every filter combination works correctly
5. **Consider Performance** - For large datasets, consider debouncing or pagination
6. **Document Dependencies** - Keep dependency configuration well-documented

## Troubleshooting

### Common Issues

1. **Filters not resetting** - Check dependency configuration
2. **Empty filter options** - Verify `dependsOn` configuration
3. **Performance issues** - Consider optimizing `getValue` functions
4. **Infinite loops** - Avoid circular dependencies

### Debug Tips

```javascript
// Add debugging to see filter states
console.log('Filter States:', filterStates.value)
console.log('Filter Items:', allFilterItems.value)
console.log('Dependencies:', dependencies)
```
