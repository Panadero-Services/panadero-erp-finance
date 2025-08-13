# Panadero ERP Finance Module

Modern finance and accounting module for ERP apps. Includes General Ledger, AP/AR, Cash Flow, Tax Management, Fixed Assets, Reporting (IS/BS/CF), Budgeting & Forecasting, and Compliance/Audit. Ships with in-memory demo data (Pinia) and optional DB/API persistence.

## 🚀 Quick Start

### Installation
This package is designed to work as a local package within your Laravel + Vue.js project.

```bash
# Clone or copy to your project's packages directory
packages/panadero-erp-finance/
```

### Usage
```vue
<!-- In your main Finance page wrapper -->
<script setup>
import Finance from '../../../../packages/panadero-erp-finance/src/components/Finance.vue';
</script>

<template>
  <Finance />
</template>
```

## ✨ Features
- **General Ledger**: Balanced journal entries, trial balance, period close
- **Accounts Payable/Receivable**: Invoices, payments, aging summary
- **Cash Flow**: Categorized inflow/outflow with basic summaries
- **Tax Management**: Records, filing, payments
- **Fixed Assets**: Register assets, straight-line depreciation, NBV
- **Reporting**: Income Statement, Balance Sheet, Cash Flow (export CSV)
- **Budgeting & Forecasting**: Budgets by account, variance vs. actual
- **Compliance & Audit**: Audit log, simple SoD check

## 📁 Package Structure
```
packages/panadero-erp-finance/
├── src/
│   ├── components/
│   │   ├── Finance.vue          # Main shell + tabs
│   │   ├── GeneralLedger.vue    # GL management
│   │   ├── AccountsPayable.vue  # AP management
│   │   ├── AccountsReceivable.vue # AR management
│   │   ├── CashFlow.vue         # Cash flow tracking
│   │   ├── TaxManagement.vue    # Tax compliance
│   │   ├── FixedAssets.vue      # Asset management
│   │   ├── Reporting.vue        # Financial reports
│   │   ├── BudgetingForecasting.vue # Budget management
│   │   └── ComplianceAudit.vue  # Audit & compliance
│   ├── composables/
│   │   ├── useGeneralLedger.js
│   │   ├── useAccountsPayable.js
│   │   ├── useAccountsReceivable.js
│   │   ├── useCashFlow.js
│   │   ├── useTaxManagement.js
│   │   ├── useFixedAssets.js
│   │   ├── useReporting.js
│   │   ├── useBudgeting.js
│   │   └── useCompliance.js
│   ├── stores/
│   │   └── financeStore.js      # Pinia store
│   └── types/
│       └── index.js             # TypeScript definitions
├── tests/                        # Test files
├── package.json                  # Package configuration
├── index.js                      # Main exports
├── README.md                     # This file
└── HowTo.md                      # User guide
```

## 🔧 Development

### Prerequisites
- Vue.js 3.x
- Pinia 2.x
- Tailwind CSS (for styling)
- Font Awesome (for icons, optional)

### Local Development
```bash
cd packages/panadero-erp-finance
npm install
npm run dev
```

### Building
```bash
npm run build
npm run lint
```

## 📊 State & Demo Data
- The Pinia store contains demo arrays for all financial data
- GL validations enforce balancing debits/credits
- Period close enforces debits==credits for that period
- All data is in-memory by default (Pinia)

## 🌐 Optional: Persistence (DB/API)
This module can run fully in-memory. To persist, wire Laravel migrations, endpoints, and seeders.

### Database Tables
Core tables from `2025_05_15_000001_create_finance_tables.php`:
- `finance_accounts`, `finance_periods`
- `finance_journal_entries`, `finance_journal_lines`
- `finance_payables`, `finance_receivables`
- `finance_tax_records`
- `finance_cashflow_categories`, `finance_cashflow_transactions`

Additional tables:
- `finance_fixed_assets`, `finance_asset_depreciations`
- `finance_budgets`, `finance_budget_lines`
- `finance_audit_logs`

### API Endpoints
```http
# Fixed Assets
GET  /api/finance/assets
POST /api/finance/assets
POST /api/finance/assets/depreciate

# Budgets
GET  /api/finance/budgets/{period}
POST /api/finance/budgets

# Audit logs
GET  /api/finance/audit-logs
POST /api/finance/audit-logs
```

## 📈 Roadmap
- Lease accounting (ASC 842/IFRS 16)
- Bank reconciliation, feeds (CSV/API)
- Automated AR reminders and AP discounting suggestions
- Advanced SoD policies and report attestations
- PDF exports and scheduled reports

## 🤝 Contributing
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📄 License
MIT License - see LICENSE file for details

## 🆘 Support
- Create an issue on GitHub
- Check the HowTo.md for user guides
- Review the composables for implementation examples

---

**Note**: This module is UI + store first. Persistence is optional and incremental. If you see "react-refresh" dev overlay warnings, they're not module-related and can be ignored or disabled in Vite.