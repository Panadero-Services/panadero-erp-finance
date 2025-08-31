import { ref, computed } from 'vue';

export function useGenericInfoBoxes(packageConfig) {
  const activeInfo = ref(1);
  
  // Extract configuration
  const {
    store,
    tableConfigs,
    sharedEntitiesConfig,
    versionData,
    navigationRoutes,
    packageName,
    tablePrefix
  } = packageConfig;

  // Dynamic package tables data computed from store
  const packageTables = computed(() => {
    return tableConfigs.map(config => ({
      name: config.name,
      records: config.getRecords(store),
      lastUpdated: getLastUpdated(config.getData(store), config.fallbackText)
    }));
  });

  // Total records count for module description
  const totalRecords = computed(() => {
    return tableConfigs.reduce((total, config) => {
      return total + config.getRecords(store);
    }, 0);
  });

  // Helper function to calculate last updated time
  function getLastUpdated(dataArray, fallbackText) {
    if (!dataArray || dataArray.length === 0) {
      return 'never';
    }
    
    // Find the most recent timestamp
    const timestamps = dataArray
      .map(item => {
        // Handle different timestamp fields
        const timestamp = item.timestamp || item.invoice_date || item.transaction_date || 
                         item.acquired_at || item.created_at || item.updated_at || 
                         item.started_at || item.completed_at;
        return timestamp ? new Date(timestamp) : null;
      })
      .filter(date => date && !isNaN(date.getTime()));
    
    if (timestamps.length === 0) {
      return 'never';
    }
    
    const mostRecent = new Date(Math.max(...timestamps));
    const now = new Date();
    const diffMs = now - mostRecent;
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) return 'today';
    if (diffDays === 1) return '1 day ago';
    if (diffDays < 7) return `${diffDays} days ago`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
    if (diffDays < 365) return `${Math.floor(diffDays / 30)} months ago`;
    return `${Math.floor(diffDays / 365)} years ago`;
  }

  // Shared entities data
  const sharedEntities = computed(() => sharedEntitiesConfig.map(entity => ({
    ...entity,
    records: entity.getRecords ? entity.getRecords(store) : 0,
    lastUpdated: entity.getLastUpdated ? entity.getLastUpdated(store) : 'today'
  })));

  // Dynamic version from JSON
  const versionUpdates = computed(() => versionData.releases || []);

  // Dynamic current version
  const moduleDescription = computed(() => ({
    title: `${packageConfig.title} v${versionData.current || '1.0.0'} (${totalRecords.value} records)`,
    version: `v${versionData.current || '1.0.0'}`,
    description: packageConfig.description,
    totalRecords: totalRecords.value,
    descriptions: packageConfig.descriptions || []
  }));

  // Navigation function for shared entities and package tables
  function navigateToEntity(entityType) {
    const route = navigationRoutes[entityType];
    if (route) {
      if (route.startsWith('#')) {
        // Handle hash-based navigation for tabs
        const tabId = route.substring(1);
        // Emit a custom event that the parent component can listen to
        window.dispatchEvent(new CustomEvent('switchPackageTab', { 
          detail: { tabId, packageName } 
        }));
      } else {
        // Use Inertia router if available, otherwise window.location
        if (window.Inertia) {
          window.Inertia.visit(route);
        } else {
          window.location.href = route;
        }
      }
    }
  }

  // Toggle info box
  function toggleInfoBox() {
    activeInfo.value = !activeInfo.value;
  }

  // Get color classes for shared entities and package tables
  function getEntityColorClasses(color) {
    const colorMap = {
      // Framework entity colors
      blue: 'bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:hover:bg-blue-900/30 dark:text-blue-300 dark:hover:text-blue-200',
      green: 'bg-green-50 hover:bg-green-100 text-green-700 dark:bg-green-900/20 dark:hover:bg-green-900/30 dark:text-green-300 dark:hover:text-green-200',
      purple: 'bg-purple-50 hover:bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:hover:bg-purple-900/30 dark:text-purple-300 dark:hover:text-purple-200',
      orange: 'bg-orange-50 hover:bg-orange-100 text-orange-700 dark:bg-orange-900/20 dark:hover:bg-orange-900/30 dark:text-orange-300 dark:hover:text-orange-200',
      teal: 'bg-teal-50 hover:bg-teal-100 text-teal-700 dark:bg-teal-900/20 dark:hover:bg-teal-900/30 dark:text-teal-300 dark:hover:text-teal-200',
      indigo: 'bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-900/20 dark:hover:bg-indigo-900/30 dark:text-indigo-300 dark:hover:text-indigo-200',
      
      // Package table colors
      gray: 'bg-gray-50 hover:bg-gray-100 text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-300 dark:hover:text-gray-200',
      red: 'bg-red-50 hover:bg-red-100 text-red-700 dark:bg-red-900/20 dark:hover:bg-red-900/30 dark:text-red-300 dark:hover:text-red-200',
      yellow: 'bg-yellow-50 hover:bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:hover:bg-yellow-900/30 dark:text-yellow-300 dark:hover:text-yellow-200'
    };
    
    return colorMap[color] || colorMap.blue;
  }

  return {
    // State
    activeInfo,
    
    // Data
    packageTables,
    sharedEntities,
    versionUpdates,
    moduleDescription,
    totalRecords,
    
    // Functions
    navigateToEntity,
    toggleInfoBox,
    getEntityColorClasses
  };
}
