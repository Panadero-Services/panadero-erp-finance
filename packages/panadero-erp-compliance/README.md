# ERP Compliance Module

A comprehensive compliance management system with AI-powered analysis and automation.

## Features

### 🛡️ Regulatory Compliance
- GDPR, SOX, ISO 27001, PCI DSS, HIPAA support
- Real-time compliance monitoring
- Automated compliance scoring
- Regulatory change tracking

### 📋 Audit Management
- Internal and external audit tracking
- Audit finding management
- Risk level assessment
- Audit scheduling and planning

### ⚠️ Risk Management
- Risk identification and assessment
- Mitigation planning and tracking
- Risk scoring and prioritization
- Automated risk monitoring

### 📄 Policy Management
- Policy creation and versioning
- Approval workflows
- Expiry tracking and notifications
- Policy compliance monitoring

### 📊 Advanced Reporting
- Real-time compliance dashboards
- Automated report generation
- Custom report templates
- Export capabilities

### 🔍 Root Cause Analysis (RCA)
- AI-powered incident investigation
- Automated root cause identification
- Corrective action planning
- Preventive measure recommendations

### 🤖 AI Agent Portal
- Natural language compliance queries
- Automated compliance checking
- Predictive risk analysis
- Intelligent recommendations

## Installation

```bash
npm install panadero-erp-compliance
```

## Usage

```javascript
import ComplianceWrapper from 'panadero-erp-compliance'

// Use in your Vue application
<ComplianceWrapper />
```

## Configuration

The module supports dynamic font sizing and follows the established ERP framework patterns:

- **Dynamic Font Sizing**: All components use scale-based font sizing instead of fixed classes
- **Dark Mode Support**: Full dark mode compatibility
- **Responsive Design**: Mobile-first responsive design
- **Accessibility**: WCAG compliant components

## Database Setup

Run the migrations to set up the required database tables:

```bash
php artisan migrate --path=packages/panadero-erp-compliance/database/migrations
```

Seed the database with sample data:

```bash
php artisan db:seed --class=Panadero\\ErpCompliance\\Database\\Seeders\\ComplianceDatabaseSeeder
```

## API Endpoints

- `GET /api/compliance/dashboard` - Get dashboard metrics
- `GET /api/compliance/policies` - List policies
- `GET /api/compliance/audits` - List audits
- `GET /api/compliance/risks` - List risks
- `GET /api/compliance/reports` - List reports
- `GET /api/compliance/rca` - List RCA cases
- `POST /api/compliance/ai-analysis/{rcaId}` - Run AI analysis
- `POST /api/compliance/generate-report` - Generate compliance report

## Components

### Main Components
- `ComplianceWrapper` - Main wrapper component
- `ComplianceDashboard` - Dashboard view
- `RegulatoryCompliance` - Regulatory compliance management
- `AuditTrails` - Audit management
- `RiskManagement` - Risk management
- `PolicyManagement` - Policy management
- `ComplianceReporting` - Reporting and analytics
- `RootCauseAnalysis` - RCA with AI features
- `AgentPortal` - AI agent interface

### UI Components
- `ComplianceButton` - Styled button component
- `ComplianceValueCard` - Metric display card
- `StatusBadge` - Status indicator badge

## Styling

The module uses Tailwind CSS with dynamic font sizing based on user preferences. All components follow the established design system:

- Scale-based font sizing (no fixed text classes)
- Consistent color scheme
- Responsive grid layouts
- Dark mode support

## Workflow Integration

The module includes workflow templates for:
- Compliance approval processes
- Audit workflows
- Risk assessment workflows
- Policy review workflows
- RCA investigation workflows

## AI Features

### Root Cause Analysis
- Automated incident analysis
- Pattern recognition
- Similar incident detection
- Risk scoring and prediction

### Agent Portal
- Natural language processing
- Compliance query handling
- Automated report generation
- Predictive analytics

## Development

### Prerequisites
- Node.js 16+
- Vue 3
- Pinia
- Tailwind CSS

### Setup
```bash
npm install
npm run dev
```

### Building
```bash
npm run build
```

## License

MIT License - see LICENSE file for details.

## Support

For support and questions, please contact the development team or create an issue in the repository.
