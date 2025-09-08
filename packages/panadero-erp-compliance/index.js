// ERP Compliance Module
// @version 1.0.0
// @date 15-Jan-2025

import ComplianceWrapper from './src/ComplianceWrapper.vue'

export default {
  name: 'panadero-erp-compliance',
  version: '1.0.0',
  description: 'ERP Compliance Module - Complete compliance management with regulatory oversight, audit trails, risk management, and AI-powered root cause analysis',
  
  // Main component
  ComplianceWrapper,
  
  // Individual components for external use
  components: {
    ComplianceWrapper
  },
  
  // Module configuration
  config: {
    name: 'panadero-erp-compliance',
    type: 'erp-module',
    category: 'compliance',
    color: 'gray',
    icon: 'ShieldCheckIcon',
    tabs: [
      {
        name: 'Dashboard',
        url: '/home/dashboard',
        route: 'home.dashboard'
      },
      {
        name: 'Regulatory Compliance',
        url: '/erp/compliance',
        route: 'erp.compliance',
        tab: 'regulatory'
      },
      {
        name: 'Audit Trails',
        url: '/erp/compliance',
        route: 'erp.compliance',
        tab: 'audit'
      },
      {
        name: 'Risk Management',
        url: '/erp/compliance',
        route: 'erp.compliance',
        tab: 'risk'
      },
      {
        name: 'Policy Management',
        url: '/erp/compliance',
        route: 'erp.compliance',
        tab: 'policies'
      },
      {
        name: 'Compliance Reports',
        url: '/erp/compliance',
        route: 'erp.compliance',
        tab: 'reports'
      },
      {
        name: 'Root Cause Analysis',
        url: '/erp/compliance',
        route: 'erp.compliance',
        tab: 'rca'
      },
      {
        name: 'Agent Portal',
        url: '/erp/compliance',
        route: 'erp.compliance',
        tab: 'agent-portal'
      }
    ],
    features: [
      'Regulatory Compliance',
      'Audit Trails',
      'Risk Management',
      'Policy Management',
      'Compliance Reports',
      'Root Cause Analysis',
      'AI-Powered Analysis',
      'Agent Portal',
      'Workflow Integration',
      'Dynamic Font Sizing',
      'Action Buttons'
    ]
  }
}
