import { ref, computed } from 'vue';
import axios from 'axios';

export function useInvoiceApi() {
  const invoices = ref([]);
  const isLoading = ref(false);
  const error = ref(null);
  
  // API base URL
  const API_BASE = '/finance/invoices';
  
  // Helper function to get cookie value
  function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
  }

  // Ensure CSRF token is available
  async function ensureCsrfToken() {
    try {
      // Check if we already have a CSRF token
      const xsrfToken = getCookie('XSRF-TOKEN');
      if (xsrfToken) {
        return xsrfToken;
      }
      
      // Fetch CSRF cookie if we don't have one
      await axios.get('/sanctum/csrf-cookie', { withCredentials: true });
      const newXsrfToken = getCookie('XSRF-TOKEN');
      if (newXsrfToken) {
        return newXsrfToken;
      }
      
      throw new Error('Failed to obtain CSRF token');
    } catch (error) {
      console.error('Error ensuring CSRF token:', error);
      throw error;
    }
  }

  // Invoice counter for auto-generation (fallback)
  const invoiceCounters = ref({
    AP: 1000, // Accounts Payable - starts from 1000
    AR: 2000, // Accounts Receivable - starts from 2000
    GL: 3000, // General Ledger - starts from 3000
    CF: 4000, // Cash Flow - starts from 4000
    TX: 5000, // Tax - starts from 5000
    FA: 6000, // Fixed Assets - starts from 6000
    BD: 7000, // Budget - starts from 7000
    AU: 8000  // Audit - starts from 8000
  });

  // Invoice templates for different types
  const invoiceTemplates = {
    AP: {
      prefix: 'AP-INV',
      description: 'Vendor Invoice',
      type: 'payable',
      category: 'expense'
    },
    AR: {
      prefix: 'AR-INV',
      description: 'Customer Invoice',
      type: 'receivable',
      category: 'revenue'
    },
    GL: {
      prefix: 'GL-JE',
      description: 'Journal Entry',
      type: 'journal',
      category: 'adjustment'
    },
    CF: {
      prefix: 'CF-TXN',
      description: 'Cash Transaction',
      type: 'cashflow',
      category: 'cash'
    },
    TX: {
      prefix: 'TX-REC',
      description: 'Tax Record',
      type: 'tax',
      category: 'tax'
    },
    FA: {
      prefix: 'FA-ASST',
      description: 'Asset Transaction',
      type: 'asset',
      category: 'asset'
    },
    BD: {
      prefix: 'BD-BGT',
      description: 'Budget Entry',
      type: 'budget',
      category: 'budget'
    },
    AU: {
      prefix: 'AU-LOG',
      description: 'Audit Log',
      type: 'audit',
      category: 'audit'
    }
  };

  // Generate next invoice number for a section (fallback)
  function generateInvoiceNumber(section) {
    if (!invoiceCounters.value[section]) {
      invoiceCounters.value[section] = 1000;
    }
    
    const counter = invoiceCounters.value[section]++;
    const template = invoiceTemplates[section];
    
    return `${template.prefix}-${counter.toString().padStart(6, '0')}`;
  }

  // Fetch invoices from API
  async function fetchInvoices(params = {}) {
    try {
      isLoading.value = true;
      error.value = null;
      
      // Ensure CSRF token is available
      await ensureCsrfToken();
      
      const response = await axios.get(API_BASE, { 
        params,
        withCredentials: true,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      });
      invoices.value = response.data.data || response.data;
      
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
      console.error('Error fetching invoices:', err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  // Create invoice via API
  async function createInvoice(section, data) {
    try {
      isLoading.value = true;
      error.value = null;
      
      // Ensure CSRF token is available
      await ensureCsrfToken();
      
      const invoiceData = {
        section,
        type: invoiceTemplates[section].type,
        category: invoiceTemplates[section].category,
        description: data.description || invoiceTemplates[section].description,
        amount: data.amount || 0,
        metadata: data,
        ...data
      };
      
      console.log('🚀 Attempting to create invoice:', invoiceData);
      
      const response = await axios.post(API_BASE, invoiceData, {
        withCredentials: true,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      });
      
      const invoice = response.data.invoice;
      console.log('✅ Invoice created successfully:', invoice);
      
      // Add to local state
      invoices.value.unshift(invoice);
      
      return invoice;
    } catch (err) {
      console.error('❌ Error creating invoice:', err);
      
      if (err.response?.status === 401) {
        // Authentication failed - show clear message
        error.value = 'Authentication failed. Please check if you are logged in and try again.';
        throw new Error('Authentication failed. Please check if you are logged in and try again.');
      } else if (err.response?.status === 403) {
        error.value = 'Access denied. You may not have permission to create invoices.';
        throw new Error('Access denied. You may not have permission to create invoices.');
      } else if (err.response?.status === 422) {
        error.value = 'Validation error: ' + (err.response.data.message || 'Please check your input data.');
        throw new Error('Validation error: ' + (err.response.data.message || 'Please check your input data.'));
      } else {
        error.value = err.response?.data?.message || err.message || 'Unknown error occurred';
        throw err;
      }
    } finally {
      isLoading.value = false;
    }
  }

  // Get invoice by ID
  async function getInvoice(id) {
    try {
      isLoading.value = true;
      error.value = null;
      
      const response = await axios.get(`${API_BASE}/${id}`);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
      console.error('Error fetching invoice:', err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  // Update invoice
  async function updateInvoice(id, data) {
    try {
      isLoading.value = true;
      error.value = null;
      
      const response = await axios.put(`${API_BASE}/${id}`, data, {
        withCredentials: true,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      });
      const updatedInvoice = response.data.invoice;
      
      // Update local state
      const index = invoices.value.findIndex(inv => inv.id === id);
      if (index !== -1) {
        invoices.value[index] = updatedInvoice;
      }
      
      return updatedInvoice;
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
      console.error('Error updating invoice:', err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  // Delete invoice
  async function deleteInvoice(id) {
    try {
      isLoading.value = true;
      error.value = null;
      
      await axios.delete(`${API_BASE}/${id}`, {
        withCredentials: true,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      });
      
      // Remove from local state
      const index = invoices.value.findIndex(inv => inv.id === id);
      if (index !== -1) {
        invoices.value.splice(index, 1);
      }
      
      return true;
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
      console.error('Error deleting invoice:', err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  // Approve invoice
  async function approveInvoice(id) {
    try {
      isLoading.value = true;
      error.value = null;
      
      const response = await axios.post(`${API_BASE}/${id}/approve`, {}, {
        withCredentials: true,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      });
      const updatedInvoice = response.data.invoice;
      
      // Update local state
      const index = invoices.value.findIndex(inv => inv.id === id);
      if (index !== -1) {
        invoices.value[index] = updatedInvoice;
      }
      
      return updatedInvoice;
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
      console.error('Error approving invoice:', err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  // Complete invoice
  async function completeInvoice(id) {
    try {
      isLoading.value = true;
      error.value = null;
      
      const response = await axios.post(`${API_BASE}/${id}/complete`, {}, {
        withCredentials: true,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      });
      const updatedInvoice = response.data.invoice;
      
      // Update local state
      const index = invoices.value.findIndex(inv => inv.id === id);
      if (index !== -1) {
        invoices.value[index] = updatedInvoice;
      }
      
      return updatedInvoice;
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
      console.error('Error completing invoice:', err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  // Cancel invoice
  async function cancelInvoice(id) {
    try {
      isLoading.value = true;
      error.value = null;
      
      const response = await axios.post(`${API_BASE}/${id}/cancel`, {}, {
        withCredentials: true,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      });
      const updatedInvoice = response.data.invoice;
      
      // Update local state
      const index = invoices.value.findIndex(inv => inv.id === id);
      if (index !== -1) {
        invoices.value[index] = updatedInvoice;
      }
      
      return updatedInvoice;
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
      console.error('Error cancelling invoice:', err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  // Get invoice statistics
  async function getInvoiceStats(section = null) {
    try {
      isLoading.value = true;
      error.value = null;
      
      const params = section ? { section } : {};
      const response = await axios.get(`${API_BASE}/stats/summary`, { 
        params,
        withCredentials: true,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      });
      
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
      console.error('Error fetching invoice stats:', err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  // Export invoices to CSV
  async function exportInvoicesToCSV(section = null, status = null) {
    try {
      isLoading.value = true;
      error.value = null;
      
      const params = {};
      if (section) params.section = section;
      if (status) params.status = status;
      
      const response = await axios.get(`${API_BASE}/export/csv`, { 
        params,
        withCredentials: true,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      });
      const csvData = response.data.data;
      const filename = response.data.filename;
      
      // Create and download CSV file
      const csvContent = csvData.map(row => row.join(',')).join('\n');
      const blob = new Blob([csvContent], { type: 'text/csv' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = filename;
      a.click();
      URL.revokeObjectURL(url);
      
      return true;
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
      console.error('Error exporting invoices:', err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  // Search invoices
  async function searchInvoices(query, section = null, status = null) {
    try {
      isLoading.value = true;
      error.value = null;
      
      const params = {};
      if (query) params.search = query;
      if (section) params.section = section;
      if (status) params.status = status;
      
      const response = await axios.get(API_BASE, { 
        params,
        withCredentials: true,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      });
      invoices.value = response.data.data || response.data;
      
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
      console.error('Error searching invoices:', err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  // Get invoices by section
  async function getInvoicesBySection(section) {
    return searchInvoices(null, section);
  }

  // Get invoices by status
  async function getInvoicesByStatus(status) {
    return searchInvoices(null, null, status);
  }

  // Check if user is authenticated
  function isAuthenticated() {
    // Debug logging
    console.log('🔍 Checking authentication status...');
    
    // Check if we have a valid session cookie
    const cookies = document.cookie.split(';');
    const hasSession = cookies.some(cookie => 
      cookie.trim().startsWith('laravel_session=') || 
      cookie.trim().startsWith('XSRF-TOKEN=')
    );
    
    console.log('🍪 Session cookies found:', hasSession);
    console.log('🍪 All cookies:', cookies.map(c => c.trim()));
    
    // Check if there's a user object in the page (Inertia.js)
    let hasUser = false;
    let userInfo = null;
    
    if (window.Inertia && window.Inertia.page && window.Inertia.page.props) {
      userInfo = window.Inertia.page.props.auth;
      hasUser = userInfo && userInfo.user;
      console.log('👤 Inertia user found:', hasUser);
      console.log('👤 User info:', userInfo);
    }
    
    // Also check for other common authentication patterns
    if (!hasUser) {
      // Check if we're in a Laravel app with user data
      if (window.Laravel && window.Laravel.user) {
        hasUser = true;
        console.log('👤 Laravel user found:', window.Laravel.user);
      }
      
      // Check if there's a user in the global scope
      if (window.user) {
        hasUser = true;
        console.log('👤 Global user found:', window.user);
      }
    }
    
    // Check if we're on a page that requires authentication
    const isOnAuthPage = window.location.pathname === '/login' || 
                        window.location.pathname === '/register' ||
                        window.location.pathname === '/forgot-password';
    
    console.log('📍 Current path:', window.location.pathname);
    console.log('📍 Is on auth page:', isOnAuthPage);
    
    const isAuth = hasSession && !isOnAuthPage && hasUser;
    console.log('✅ Final authentication result:', isAuth);
    
    return isAuth;
  }

  // Get authentication status with details
  function getAuthStatus() {
    const cookies = document.cookie.split(';');
    const hasSession = cookies.some(cookie => 
      cookie.trim().startsWith('laravel_session=') || 
      cookie.trim().startsWith('XSRF-TOKEN=')
    );
    
    const hasUser = window.Inertia && window.Inertia.page && window.Inertia.page.props && window.Inertia.page.props.auth && window.Inertia.page.props.auth.user;
    
    return {
      hasSession,
      hasUser,
      isAuthenticated: hasSession && hasUser,
      currentPath: window.location.pathname,
      needsLogin: !hasSession || !hasUser
    };
  }

  // Test authentication by making a simple API call
  async function testAuthentication() {
    try {
      console.log('🧪 Testing authentication with API call...');
      const response = await axios.get('/api/test-auth', {
        withCredentials: true,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      });
      console.log('✅ Authentication test successful:', response.data);
      return true;
    } catch (error) {
      console.log('❌ Authentication test failed:', error.response?.status, error.response?.data);
      return false;
    }
  }

  // Handle authentication errors
  function handleAuthError(error) {
    if (error.response?.status === 401) {
      error.value = 'Authentication required. Please log in.';
      // Redirect to login if not authenticated
      if (window.location.pathname !== '/login') {
        window.location.href = '/login';
      }
    } else {
      error.value = error.response?.data?.message || error.message;
    }
  }

  // Computed properties
  const totalInvoices = computed(() => invoices.value.length);
  const draftInvoices = computed(() => invoices.value.filter(inv => inv.status === 'draft'));
  const pendingInvoices = computed(() => invoices.value.filter(inv => inv.status === 'pending'));
  const approvedInvoices = computed(() => invoices.value.filter(inv => inv.status === 'approved'));
  const completedInvoices = computed(() => invoices.value.filter(inv => inv.status === 'completed'));

  return {
    // State
    invoices,
    isLoading,
    error,
    
    // Methods
    generateInvoiceNumber,
    fetchInvoices,
    createInvoice,
    getInvoice,
    updateInvoice,
    deleteInvoice,
    approveInvoice,
    completeInvoice,
    cancelInvoice,
    getInvoiceStats,
    exportInvoicesToCSV,
    searchInvoices,
    getInvoicesBySection,
    getInvoicesByStatus,
    isAuthenticated,
    getAuthStatus,
    testAuthentication,
    handleAuthError,
    
    // Computed
    totalInvoices,
    draftInvoices,
    pendingInvoices,
    approvedInvoices,
    completedInvoices,
    
    // Templates
    invoiceTemplates
  };
}
