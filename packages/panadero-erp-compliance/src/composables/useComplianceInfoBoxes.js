import { ref, computed } from 'vue'

export function useComplianceInfoBoxes() {
  const infoBoxes = ref([
    {
      id: 'policies',
      title: 'Policy Management',
      description: 'Manage compliance policies and procedures with version control and approval workflows',
      icon: 'fas fa-file-contract',
      color: 'teal',
      count: 0,
      status: 'active',
      features: [
        'Policy creation and editing',
        'Version control and approval',
        'Automated compliance checking',
        'Policy distribution tracking'
      ]
    },
    {
      id: 'audits',
      title: 'Audit Management',
      description: 'Conduct and track compliance audits with automated scheduling and reporting',
      icon: 'fas fa-clipboard-check',
      color: 'blue',
      count: 0,
      status: 'active',
      features: [
        'Audit planning and scheduling',
        'Automated audit workflows',
        'Real-time audit tracking',
        'Comprehensive audit reports'
      ]
    },
    {
      id: 'risks',
      title: 'Risk Assessment',
      description: 'Identify and manage compliance risks with AI-powered risk analysis',
      icon: 'fas fa-exclamation-triangle',
      color: 'red',
      count: 0,
      status: 'active',
      features: [
        'Risk identification and assessment',
        'AI-powered risk analysis',
        'Risk mitigation tracking',
        'Risk reporting and dashboards'
      ]
    },
    {
      id: 'reports',
      title: 'Compliance Reports',
      description: 'Generate and manage comprehensive compliance reports with real-time data',
      icon: 'fas fa-chart-line',
      color: 'green',
      count: 0,
      status: 'active',
      features: [
        'Automated report generation',
        'Real-time compliance metrics',
        'Custom report templates',
        'Scheduled report delivery'
      ]
    },
    {
      id: 'rca',
      title: 'Root Cause Analysis',
      description: 'AI-powered root cause analysis for compliance issues and incidents',
      icon: 'fas fa-search',
      color: 'purple',
      count: 0,
      status: 'active',
      features: [
        'AI-powered analysis',
        'Incident investigation',
        'Root cause identification',
        'Preventive action planning'
      ]
    },
    {
      id: 'agent',
      title: 'AI Agent Portal',
      description: 'AI-powered compliance assistance with intelligent recommendations',
      icon: 'fas fa-robot',
      color: 'orange',
      count: 0,
      status: 'active',
      features: [
        'AI-powered assistance',
        'Intelligent recommendations',
        'Automated compliance checks',
        'Natural language queries'
      ]
    }
  ])

  return {
    infoBoxes
  }
}
