import { ref, computed } from 'vue';
import { useFinanceStore } from '../stores/financeStore';

export function useInvoiceSystem() {
  const store = useFinanceStore();
  
  // Verify store methods are available
  if (!store.addPayable || !store.addReceivable) {
    console.warn('Store methods not available:', {
      addPayable: typeof store.addPayable,
      addReceivable: typeof store.addReceivable
    });
  }
  
  // Invoice counter for auto-generation
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

  // Generate next invoice number for a section
  function generateInvoiceNumber(section) {
    if (!invoiceCounters.value[section]) {
      invoiceCounters.value[section] = 1000;
    }
    
    const counter = invoiceCounters.value[section]++;
    const template = invoiceTemplates[section];
    
    return `${template.prefix}-${counter.toString().padStart(6, '0')}`;
  }

  // Create invoice with auto-generated number
  async function createInvoice(section, data) {
    const invoiceNumber = generateInvoiceNumber(section);
    const template = invoiceTemplates[section];
    
    const invoice = {
      id: Date.now(),
      invoice_number: invoiceNumber,
      section: section,
      type: template.type,
      category: template.category,
      description: template.description,
      created_at: new Date().toISOString(),
      status: 'draft',
      ...data
    };

    // Add to appropriate store section
    try {
      switch (section) {
        case 'AP':
          if (store.addPayable) {
            await store.addPayable(invoice);
          }
          break;
        case 'AR':
          if (store.addReceivable) {
            await store.addReceivable(invoice);
          }
          break;
        case 'GL':
          if (store.addJournalEntry) {
            await store.addJournalEntry(invoice);
          }
          break;
        case 'CF':
          if (store.addCashFlowTransaction) {
            await store.addCashFlowTransaction(invoice);
          }
          break;
        case 'TX':
          if (store.addTaxRecord) {
            await store.addTaxRecord(invoice);
          }
          break;
        case 'FA':
          if (store.addFixedAsset) {
            await store.addFixedAsset(invoice);
          }
          break;
        case 'BD':
          if (store.addBudget) {
            await store.addBudget(invoice);
          }
          break;
        case 'AU':
          if (store.addAuditLog) {
            await store.addAuditLog(invoice);
          }
          break;
      }
    } catch (error) {
      console.warn(`Could not add invoice to store for section ${section}:`, error);
    }

    return invoice;
  }

  // Get invoice by number
  function getInvoiceByNumber(invoiceNumber) {
    // Search across all store sections
    const allInvoices = [
      ...store.payables,
      ...store.receivables,
      ...store.journalEntries,
      ...store.cashFlowTransactions,
      ...store.taxRecords,
      ...store.fixedAssets,
      ...store.budgets,
      ...store.auditLogs
    ];
    
    return allInvoices.find(inv => inv.invoice_number === invoiceNumber);
  }

  // Update invoice status
  function updateInvoiceStatus(invoiceNumber, status) {
    const invoice = getInvoiceByNumber(invoiceNumber);
    if (invoice) {
      invoice.status = status;
      invoice.updated_at = new Date().toISOString();
    }
  }

  // Get invoice history for a section
  function getInvoiceHistory(section, limit = 10) {
    let invoices = [];
    
    switch (section) {
      case 'AP':
        invoices = store.payables;
        break;
      case 'AR':
        invoices = store.receivables;
        break;
      case 'GL':
        invoices = store.journalEntries;
        break;
      case 'CF':
        invoices = store.cashFlowTransactions;
        break;
      case 'TX':
        invoices = store.taxRecords;
        break;
      case 'FA':
        invoices = store.fixedAssets;
        break;
      case 'BD':
        invoices = store.budgets;
        break;
      case 'AU':
        invoices = store.auditLogs;
        break;
    }
    
    return invoices
      .filter(inv => inv.invoice_number)
      .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
      .slice(0, limit);
  }

  // Get invoice statistics
  function getInvoiceStats(section) {
    const invoices = getInvoiceHistory(section);
    
    return {
      total: invoices.length,
      draft: invoices.filter(inv => inv.status === 'draft').length,
      pending: invoices.filter(inv => inv.status === 'pending').length,
      approved: invoices.filter(inv => inv.status === 'approved').length,
      completed: invoices.filter(inv => inv.status === 'completed').length,
      cancelled: invoices.filter(inv => inv.status === 'cancelled').length
    };
  }

  // Search invoices
  function searchInvoices(query, section = null) {
    const allInvoices = [
      ...store.payables,
      ...store.receivables,
      ...store.journalEntries,
      ...store.cashFlowTransactions,
      ...store.taxRecords,
      ...store.fixedAssets,
      ...store.budgets,
      ...store.auditLogs
    ];
    
    let filtered = allInvoices.filter(inv => inv.invoice_number);
    
    if (section) {
      filtered = filtered.filter(inv => inv.section === section);
    }
    
    if (query) {
      const searchTerm = query.toLowerCase();
      filtered = filtered.filter(inv => 
        inv.invoice_number.toLowerCase().includes(searchTerm) ||
        (inv.description && inv.description.toLowerCase().includes(searchTerm)) ||
        (inv.vendor_name && inv.vendor_name.toLowerCase().includes(searchTerm)) ||
        (inv.customer_name && inv.customer_name.toLowerCase().includes(searchTerm))
      );
    }
    
    return filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
  }

  // Export invoices to CSV
  function exportInvoicesToCSV(section, status = null) {
    let invoices = getInvoiceHistory(section);
    
    if (status) {
      invoices = invoices.filter(inv => inv.status === status);
    }
    
    const headers = ['Invoice Number', 'Section', 'Type', 'Description', 'Amount', 'Status', 'Created Date'];
    const rows = invoices.map(inv => [
      inv.invoice_number,
      inv.section,
      inv.type,
      inv.description || '',
      inv.amount || 0,
      inv.status,
      new Date(inv.created_at).toLocaleDateString()
    ]);
    
    const csvContent = [headers, ...rows]
      .map(row => row.map(cell => `"${cell}"`).join(','))
      .join('\n');
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${section}-invoices-${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    URL.revokeObjectURL(url);
  }

  return {
    // Core functions
    generateInvoiceNumber,
    createInvoice,
    getInvoiceByNumber,
    updateInvoiceStatus,
    
    // Query functions
    getInvoiceHistory,
    getInvoiceStats,
    searchInvoices,
    
    // Export functions
    exportInvoicesToCSV,
    
    // Data
    invoiceTemplates,
    invoiceCounters
  };
}
