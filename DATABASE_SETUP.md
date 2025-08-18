# Finance Invoice System - Database Setup

This document explains how to set up the database for the ERP Finance Module's invoice system.

## 📋 **Prerequisites**

- Laravel 10+ application
- MySQL/PostgreSQL database
- Composer for package management

## 🗄️ **Database Structure**

### **Main Invoice Table: `finance_invoices`**

The `finance_invoices` table stores all invoices across all finance modules:

```sql
CREATE TABLE finance_invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(255) UNIQUE NOT NULL,
    section VARCHAR(10) NOT NULL, -- AP, AR, GL, CF, TX, FA, BD, AU
    type VARCHAR(50) NOT NULL, -- payable, receivable, journal, cashflow, tax, asset, budget, audit
    category VARCHAR(50) NOT NULL, -- expense, revenue, adjustment, cash, tax, asset, budget, audit
    description TEXT NULL,
    status VARCHAR(20) DEFAULT 'draft', -- draft, pending, approved, completed, cancelled
    amount DECIMAL(15,2) DEFAULT 0.00,
    metadata JSON NULL, -- Store section-specific data
    reference_id VARCHAR(255) NULL, -- Link to original record
    reference_type VARCHAR(255) NULL, -- Model type for reference
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);
```

## 🚀 **Installation Steps**

### **Step 1: Copy Migration Files**

Copy the migration file to your Laravel project:

```bash
# Copy the migration file
cp packages/panadero-erp-finance/database/migrations/2025_05_15_000001_create_finance_invoices_table.php \
   database/migrations/2025_05_15_000001_create_finance_invoices_table.php
```

### **Step 2: Copy Model Files**

Copy the model file to your Laravel project:

```bash
# Create the models directory if it doesn't exist
mkdir -p app/Models

# Copy the model file
cp packages/panadero-erp-finance/app/Models/FinanceInvoice.php \
   app/Models/FinanceInvoice.php
```

### **Step 3: Copy Controller Files**

Copy the controller file to your Laravel project:

```bash
# Create the controllers directory if it doesn't exist
mkdir -p app/Http/Controllers

# Copy the controller file
cp packages/panadero-erp-finance/app/Http/Controllers/FinanceInvoiceController.php \
   app/Http/Controllers/FinanceInvoiceController.php
```

### **Step 4: Copy API Routes**

Copy the API routes to your Laravel project:

```bash
# Copy the routes file
cp packages/panadero-erp-finance/routes/api.php \
   routes/finance_invoices_api.php
```

Then include it in your main `routes/api.php`:

```php
// routes/api.php
require __DIR__.'/finance_invoices_api.php';
```

### **Step 5: Copy Seeder Files**

Copy the seeder file to your Laravel project:

```bash
# Create the seeders directory if it doesn't exist
mkdir -p database/seeders

# Copy the seeder file
cp packages/panadero-erp-finance/database/seeders/FinanceInvoiceSeeder.php \
   database/seeders/FinanceInvoiceSeeder.php
```

### **Step 6: Run Migrations**

```bash
# Run the migration
php artisan migrate

# Verify the table was created
php artisan migrate:status
```

### **Step 7: Seed Sample Data**

```bash
# Run the seeder
php artisan db:seed --class=FinanceInvoiceSeeder

# Or run all seeders
php artisan db:seed
```

## 🔧 **Configuration**

### **Environment Variables**

Add these to your `.env` file:

```env
# Finance Invoice System
FINANCE_INVOICE_AUTO_APPROVE=false
FINANCE_INVOICE_REQUIRE_APPROVAL=true
FINANCE_INVOICE_NOTIFICATION_EMAIL=true
```

### **Service Provider Registration**

Add the service provider to `config/app.php`:

```php
'providers' => [
    // ... other providers
    App\Providers\FinanceInvoiceServiceProvider::class,
],
```

## 📊 **Sample Data**

The seeder creates sample invoices for all sections:

- **AP-INV-000001**: Office Supplies (Approved)
- **AR-INV-000001**: Web Development (Completed)
- **GL-JE-000001**: Monthly Depreciation (Approved)
- **CF-TXN-000001**: Bank Transfer (Completed)
- **TX-REC-000001**: Q1 Income Tax (Pending)
- **FA-ASST-000001**: Computer Equipment (Approved)
- **BD-BGT-000001**: Q2 Operating Budget (Approved)
- **AU-LOG-000001**: User Permission Change (Completed)

## 🧪 **Testing the Setup**

### **Test API Endpoints**

```bash
# Get all invoices
curl -X GET "http://your-app.test/api/finance/invoices" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Get invoices by section
curl -X GET "http://your-app.test/api/finance/invoices?section=AP" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Get invoice statistics
curl -X GET "http://your-app.test/api/finance/invoices/stats/summary" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### **Test Invoice Creation**

```bash
# Create a new invoice
curl -X POST "http://your-app.test/api/finance/invoices" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "section": "AP",
    "type": "payable",
    "category": "expense",
    "description": "Test Invoice",
    "amount": 100.00
  }'
```

## 🔍 **Troubleshooting**

### **Common Issues**

1. **Migration Fails**: Check if the `users` table exists
2. **Model Not Found**: Verify the namespace in `FinanceInvoice.php`
3. **Route Not Found**: Check if the API routes are properly included
4. **Seeder Fails**: Ensure the `users` table has at least one user

### **Debug Commands**

```bash
# Check migration status
php artisan migrate:status

# Check if table exists
php artisan tinker
>>> Schema::hasTable('finance_invoices')

# Check model
php artisan tinker
>>> App\Models\FinanceInvoice::count()

# Check routes
php artisan route:list | grep finance
```

## 📚 **Next Steps**

After setting up the database:

1. **Update Frontend Components**: Modify the invoice system to use API calls instead of in-memory data
2. **Add Authentication**: Ensure proper user authentication and authorization
3. **Implement Workflows**: Add approval workflows and notifications
4. **Add Reporting**: Create invoice reports and analytics
5. **Integration**: Connect with other ERP modules

## 🆘 **Support**

If you encounter issues:

1. Check the Laravel logs: `storage/logs/laravel.log`
2. Verify database connectivity
3. Check table structure: `DESCRIBE finance_invoices;`
4. Review the migration file for syntax errors

---

**Note**: This setup creates a production-ready invoice system with proper database structure, relationships, and API endpoints.

