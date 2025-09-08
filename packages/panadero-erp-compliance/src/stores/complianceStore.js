import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useComplianceStore = defineStore('compliance', () => {
  // Settings - centralized configuration
  const settings = ref({
    // Font scaling
    fontSize: 14,
    
    // Display
    darkMode: true,
    compactLayout: false,
    showDebug: false,
    
    // Tables
    rowsPerPage: 25,
    showRowNumbers: true,
    stickyHeaders: true,
    
    // Export
    defaultExportFormat: 'csv',
    includeHeaders: true,
    autoExport: false,
    
    // Data
    autoRefreshInterval: 30,
    lazyLoading: true,
    cacheData: true
  });

  // Settings actions
  const updateSetting = (key, value) => {
    settings.value[key] = value;
    localStorage.setItem('complianceSettings', JSON.stringify(settings.value));
  };

  const updateSettings = (newSettings) => {
    Object.assign(settings.value, newSettings);
    localStorage.setItem('complianceSettings', JSON.stringify(settings.value));
  };

  const resetSettings = () => {
    settings.value = {
      fontSize: 14,
      darkMode: true,
      compactLayout: false,
      showDebug: false,
      rowsPerPage: 25,
      showRowNumbers: true,
      stickyHeaders: true,
      defaultExportFormat: 'csv',
      includeHeaders: true,
      autoExport: false,
      autoRefreshInterval: 30,
      lazyLoading: true,
      cacheData: true
    };
    localStorage.setItem('complianceSettings', JSON.stringify(settings.value));
  };

  // Loading state management
  const setLoading = (key, value) => {
    if (loading.value.hasOwnProperty(key)) {
      loading.value[key] = value;
    }
  };

  // Status color helper
  const getStatusColor = (status) => {
    const statusColors = {
      'open': 'red',
      'in-progress': 'yellow',
      'resolved': 'green',
      'closed': 'gray',
      'pending': 'blue',
      'active': 'green',
      'inactive': 'gray',
      'draft': 'yellow',
      'published': 'green',
      'completed': 'green',
      'cancelled': 'red'
    };
    return statusColors[status] || 'gray';
  };

  // Load settings from localStorage
  const loadSettings = () => {
    const saved = localStorage.getItem('complianceSettings');
    if (saved) {
      try {
        settings.value = { ...settings.value, ...JSON.parse(saved) };
      } catch (e) {
        console.warn('Failed to load compliance settings:', e);
      }
    }
  };

  // Initialize settings
  loadSettings();

  // Data state
  const policies = ref([]);
  const audits = ref([]);
  const risks = ref([]);
  const reports = ref([]);
  const rcaCases = ref([]);

  // Loading states
  const loading = ref({
    policies: false,
    audits: false,
    risks: false,
    reports: false,
    rca: false
  });

  // Error states
  const errors = ref({
    policies: null,
    audits: null,
    risks: null,
    reports: null,
    rca: null
  });

  // Computed properties for statistics
  const getPolicyStats = computed(() => {
    const total = policies.value.length;
    const active = policies.value.filter(p => p.status === 'active').length;
    const complianceRate = total > 0 ? Math.round((active / total) * 100) : 0;
    
    return {
      total,
      active,
      inactive: total - active,
      complianceRate
    };
  });

  const getAuditStats = computed(() => {
    const total = audits.value.length;
    const completed = audits.value.filter(a => a.status === 'completed').length;
    const completionRate = total > 0 ? Math.round((completed / total) * 100) : 0;
    
    return {
      total,
      completed,
      pending: total - completed,
      completionRate
    };
  });

  const getRiskStats = computed(() => {
    const total = risks.value.length;
    const highRisk = risks.value.filter(r => r.level === 'high' || r.level === 'critical').length;
    const mitigated = risks.value.filter(r => r.status === 'mitigated').length;
    const mitigationRate = total > 0 ? Math.round((mitigated / total) * 100) : 0;
    
    return {
      total,
      highRisk,
      mitigated,
      mitigationRate
    };
  });

  const getReportStats = computed(() => {
    const total = reports.value.length;
    const published = reports.value.filter(r => r.status === 'published').length;
    
    return {
      total,
      published,
      draft: total - published
    };
  });

  // Actions
  const fetchPolicies = async () => {
    loading.value.policies = true;
    errors.value.policies = null;
    
    try {
      const response = await axios.get('/api/compliance/policies');
      policies.value = response.data;
    } catch (error) {
      errors.value.policies = error.message;
      console.error('Failed to fetch policies:', error);
    } finally {
      loading.value.policies = false;
    }
  };

  const fetchAudits = async () => {
    loading.value.audits = true;
    errors.value.audits = null;
    
    try {
      const response = await axios.get('/api/compliance/audits');
      audits.value = response.data;
    } catch (error) {
      errors.value.audits = error.message;
      console.error('Failed to fetch audits:', error);
    } finally {
      loading.value.audits = false;
    }
  };

  const fetchRisks = async () => {
    loading.value.risks = true;
    errors.value.risks = null;
    
    try {
      const response = await axios.get('/api/compliance/risks');
      risks.value = response.data;
    } catch (error) {
      errors.value.risks = error.message;
      console.error('Failed to fetch risks:', error);
    } finally {
      loading.value.risks = false;
    }
  };

  const fetchReports = async () => {
    loading.value.reports = true;
    errors.value.reports = null;
    
    try {
      const response = await axios.get('/api/compliance/reports');
      reports.value = response.data;
    } catch (error) {
      errors.value.reports = error.message;
      console.error('Failed to fetch reports:', error);
    } finally {
      loading.value.reports = false;
    }
  };

  const fetchRCACases = async () => {
    loading.value.rca = true;
    errors.value.rca = null;
    
    try {
      const response = await axios.get('/api/compliance/rca');
      rcaCases.value = response.data;
    } catch (error) {
      errors.value.rca = error.message;
      console.error('Failed to fetch RCA cases:', error);
    } finally {
      loading.value.rca = false;
    }
  };

  // CRUD operations for policies
  const addPolicy = async (policyData) => {
    try {
      const response = await axios.post('/api/compliance/policies', policyData);
      policies.value.push(response.data);
      return response.data;
    } catch (error) {
      console.error('Failed to add policy:', error);
      throw error;
    }
  };

  const updatePolicy = async (id, policyData) => {
    try {
      const response = await axios.put(`/api/compliance/policies/${id}`, policyData);
      const index = policies.value.findIndex(p => p.id === id);
      if (index !== -1) {
        policies.value[index] = response.data;
      }
      return response.data;
    } catch (error) {
      console.error('Failed to update policy:', error);
      throw error;
    }
  };

  const deletePolicy = async (id) => {
    try {
      await axios.delete(`/api/compliance/policies/${id}`);
      policies.value = policies.value.filter(p => p.id !== id);
    } catch (error) {
      console.error('Failed to delete policy:', error);
      throw error;
    }
  };

  // Initialize data
  const initializeData = async () => {
    await Promise.all([
      fetchPolicies(),
      fetchAudits(),
      fetchRisks(),
      fetchReports(),
      fetchRCACases()
    ]);
  };

  return {
    // State
    settings,
    policies,
    audits,
    risks,
    reports,
    rcaCases,
    loading,
    errors,
    
    // Computed
    getPolicyStats,
    getAuditStats,
    getRiskStats,
    getReportStats,
    
    // Actions
    updateSetting,
    updateSettings,
    resetSettings,
    loadSettings,
    setLoading,
    getStatusColor,
    fetchPolicies,
    fetchAudits,
    fetchRisks,
    fetchReports,
    fetchRCACases,
    addPolicy,
    updatePolicy,
    deletePolicy,
    initializeData
  };
});