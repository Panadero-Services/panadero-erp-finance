# ERP HR Module

A comprehensive human resources management system with AI-powered recruitment and employee management capabilities.

## Features

- **Employee Management**: Complete employee lifecycle management
- **Recruitment System**: AI-powered resume analysis and job matching
- **Vacancy Management**: Create and manage job postings
- **Department Management**: Organize company structure
- **HR Reports**: Comprehensive analytics and reporting
- **Agent Portal**: AI integration for HR insights
- **Dynamic Font Sizing**: Responsive typography system
- **Workflow Integration**: Built-in approval processes

## Installation

```bash
npm install @panadero/erp-hr
```

## Usage

### Basic Setup

```vue
<template>
  <HrWrapper />
</template>

<script setup>
import { HrWrapper } from '@panadero/erp-hr';
</script>
```

### Individual Components

```vue
<template>
  <div>
    <HrDashboard />
    <EmployeeManagement />
    <VacancyManagement />
    <AgentPortal />
  </div>
</template>

<script setup>
import { 
  HrDashboard, 
  EmployeeManagement, 
  VacancyManagement, 
  AgentPortal 
} from '@panadero/erp-hr';
</script>
```

### Store Usage

```javascript
import { useHrStore } from '@panadero/erp-hr';

const store = useHrStore();

// Access data
const employees = store.employees;
const vacancies = store.vacancies;

// Actions
await store.addEmployee(employeeData);
await store.addVacancy(vacancyData);
```

## Database Setup

Run the included migrations to set up the required database tables:

```bash
php artisan migrate --path=packages/panadero-erp-hr/database/migrations
```

Seed the database with sample data:

```bash
php artisan db:seed --class=Panadero\\Erp\\Hr\\Database\\Seeders\\HrDatabaseSeeder
```

## Configuration

The module includes a comprehensive configuration system:

```json
{
  "name": "panadero-erp-hr",
  "version": "1.0.0",
  "tabs": [
    {
      "name": "Dashboard",
      "url": "/home/dashboard",
      "route": "home.dashboard"
    },
    {
      "name": "Employee Management",
      "url": "/erp/hr",
      "route": "erp.hr",
      "tab": "employees"
    }
  ],
  "features": [
    "Employee Management",
    "Recruitment System",
    "Vacancy Management",
    "AI Integration"
  ]
}
```

## AI Integration

The Agent Portal provides AI-powered features:

- Resume analysis and scoring
- Job matching algorithms
- Performance review insights
- HR analytics and recommendations

Configure your AI provider in the Agent Portal settings.

## Components

### Core Components

- `HrWrapper`: Main wrapper component
- `HrDashboard`: Overview dashboard
- `EmployeeManagement`: Employee CRUD operations
- `VacancyManagement`: Job posting management
- `Recruitment`: Application management
- `DepartmentManagement`: Organizational structure
- `HrReporting`: Analytics and reports
- `AgentPortal`: AI integration interface

### UI Components

- `HrButton`: Styled button component
- `HrInput`: Form input component
- `HrDropdown`: Select dropdown component
- `HrValueCard`: Metric display card
- `StatusBadge`: Status indicator

## Styling

The module uses Tailwind CSS with a consistent design system:

- Dynamic font sizing based on user preferences
- Dark mode support
- Responsive design
- Consistent color scheme

## Workflow Integration

Built-in workflow templates:

- Employee onboarding
- Vacancy approval
- Recruitment approval
- Performance review
- Resume analysis

## License

MIT License - see LICENSE file for details.

## Support

For support and questions, please contact the development team or create an issue in the repository.
