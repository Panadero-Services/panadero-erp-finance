/**
 * Centralized Dark Mode Utility System
 * Contains all dark mode classes organized by component type for consistent theming
 */

export const darkMode = {
  // Main Container Classes
  container: {
    main: 'dark:bg-gray-900',
    secondary: 'dark:bg-gray-800',
    tertiary: 'dark:bg-gray-700',
    card: 'dark:bg-gray-800 dark:shadow-gray-900/50',
    modal: 'dark:bg-gray-800 dark:shadow-gray-900/50'
  },

  // Text Classes
  text: {
    primary: 'dark:text-white',
    secondary: 'dark:text-gray-100',
    tertiary: 'dark:text-gray-200',
    muted: 'dark:text-gray-300',
    disabled: 'dark:text-gray-400',
    placeholder: 'dark:text-gray-500'
  },

  // Border Classes
  border: {
    primary: 'dark:border-gray-700',
    secondary: 'dark:border-gray-600',
    subtle: 'dark:border-gray-500',
    accent: 'dark:border-gray-400'
  },

  // Form Input Classes
  input: {
    background: 'dark:bg-gray-700',
    text: 'dark:text-gray-100',
    border: 'dark:border-gray-600',
    placeholder: 'dark:text-gray-400',
    focus: 'dark:focus:border-blue-400 dark:focus:ring-blue-400/20'
  },

  // Button Classes
  button: {
    primary: 'dark:bg-indigo-600 dark:text-white dark:hover:bg-indigo-700',
    secondary: 'dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600',
    success: 'dark:bg-green-600 dark:text-white dark:hover:bg-green-700',
    warning: 'dark:bg-yellow-600 dark:text-white dark:hover:bg-yellow-700',
    danger: 'dark:bg-red-600 dark:text-white dark:hover:bg-red-700',
    info: 'dark:bg-blue-600 dark:text-white dark:hover:bg-blue-700'
  },

  // Table Classes
  table: {
    container: 'dark:bg-gray-800 dark:border-gray-700',
    header: 'dark:bg-gray-700 dark:border-gray-600',
    headerText: 'dark:text-gray-200',
    row: 'dark:text-gray-100 dark:border-gray-600',
    rowHover: 'dark:hover:bg-gray-700',
    empty: 'dark:text-gray-400'
  },

  // Status Badge Classes
  status: {
    success: 'dark:bg-green-900 dark:text-green-200',
    warning: 'dark:bg-yellow-900 dark:text-yellow-200',
    error: 'dark:bg-red-900 dark:text-red-200',
    info: 'dark:bg-blue-900 dark:text-blue-200',
    neutral: 'dark:bg-gray-700 dark:text-gray-200'
  },

  // Summary Card Classes
  summary: {
    container: 'dark:bg-gray-800 dark:shadow-gray-900/50',
    title: 'dark:text-gray-300',
    value: 'dark:text-white',
    positive: 'dark:text-green-400',
    negative: 'dark:text-red-400',
    warning: 'dark:text-yellow-400',
    neutral: 'dark:text-gray-100'
  },

  // Navigation Classes
  navigation: {
    tab: 'dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700',
    tabActive: 'dark:text-white dark:bg-gray-700',
    menu: 'dark:bg-gray-800 dark:border-gray-700',
    menuItem: 'dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white'
  },

  // Info Box Classes
  infoBox: {
    container: 'dark:bg-gray-800 dark:border-gray-700',
    header: 'dark:text-white',
    content: 'dark:text-gray-200',
    item: 'dark:hover:bg-gray-700',
    button: 'dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600'
  },

  // Modal Classes
  modal: {
    overlay: 'dark:bg-black dark:opacity-75',
    container: 'dark:bg-gray-900 dark:from-gray-600/50 dark:to-gray-900/50',
    header: 'dark:text-gray-300',
    body: 'dark:text-gray-100',
    footer: 'dark:border-gray-700'
  },

  // Filter Classes
  filter: {
    container: 'dark:bg-gray-800 dark:border-gray-700',
    label: 'dark:text-gray-200',
    input: 'dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600',
    select: 'dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600'
  },

  // Dashboard Classes
  dashboard: {
    container: 'dark:bg-gray-900',
    card: 'dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/50',
    metric: 'dark:text-white',
    label: 'dark:text-gray-300',
    chart: 'dark:bg-gray-800 dark:border-gray-700'
  }
};

// Utility functions for common combinations
export const darkModeUtils = {
  // Get complete container classes
  getContainer: (type = 'main') => darkMode.container[type],
  
  // Get complete text classes
  getText: (type = 'primary') => darkMode.text[type],
  
  // Get complete border classes
  getBorder: (type = 'primary') => darkMode.border[type],
  
  // Get complete input classes
  getInput: () => `${darkMode.input.background} ${darkMode.input.text} ${darkMode.input.border} ${darkMode.input.placeholder}`,
  
  // Get complete button classes
  getButton: (type = 'primary') => darkMode.button[type],
  
  // Get complete table classes
  getTable: () => ({
    container: darkMode.table.container,
    header: darkMode.table.header,
    headerText: darkMode.table.headerText,
    row: darkMode.table.row,
    rowHover: darkMode.table.rowHover,
    empty: darkMode.table.empty
  }),
  
  // Get complete summary card classes
  getSummary: () => ({
    container: darkMode.summary.container,
    title: darkMode.summary.title,
    value: darkMode.summary.value,
    positive: darkMode.summary.positive,
    negative: darkMode.summary.negative,
    warning: darkMode.summary.warning,
    neutral: darkMode.summary.neutral
  }),
  
  // Get complete modal classes
  getModal: () => ({
    overlay: darkMode.modal.overlay,
    container: darkMode.modal.container,
    header: darkMode.modal.header,
    body: darkMode.modal.body,
    footer: darkMode.modal.footer
  }),
  
  // Get complete filter classes
  getFilter: () => ({
    container: darkMode.filter.container,
    label: darkMode.filter.label,
    input: darkMode.filter.input,
    select: darkMode.filter.select
  }),
  
  // Get complete dashboard classes
  getDashboard: () => ({
    container: darkMode.dashboard.container,
    card: darkMode.dashboard.card,
    metric: darkMode.dashboard.metric,
    label: darkMode.dashboard.label,
    chart: darkMode.dashboard.chart
  })
};

// Pre-built class combinations for common use cases
export const darkModeClasses = {
  // Main page container
  pageContainer: `${darkMode.container.main} ${darkMode.text.primary}`,
  
  // Card container
  cardContainer: `${darkMode.container.card} ${darkMode.border.primary}`,
  
  // Table container
  tableContainer: `${darkMode.table.container} ${darkMode.border.primary}`,
  
  // Form input
  formInput: `${darkMode.input.background} ${darkMode.input.text} ${darkMode.input.border} ${darkMode.input.placeholder}`,
  
  // Button primary
  buttonPrimary: darkMode.button.primary,
  
  // Button secondary
  buttonSecondary: darkMode.button.secondary,
  
  // Status success
  statusSuccess: darkMode.status.success,
  
  // Status warning
  statusWarning: darkMode.status.warning,
  
  // Status error
  statusError: darkMode.status.error,
  
  // Summary card
  summaryCard: `${darkMode.summary.container} ${darkMode.border.primary}`,
  
  // Modal
  modalContainer: `${darkMode.modal.container} ${darkMode.border.primary}`,
  
  // Filter section
  filterSection: `${darkMode.filter.container} ${darkMode.border.primary}`,
  
  // Dashboard card
  dashboardCard: `${darkMode.dashboard.card} ${darkMode.border.primary}`
};

export default darkMode;

