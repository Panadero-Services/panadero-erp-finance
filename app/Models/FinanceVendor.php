<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasPermissions;

class FinanceVendor extends BaseModel
{
    use HasFactory, SoftDeletes, HasPermissions;

    /**
     * The table associated with the model.
     */
    protected $table = 'finance_vendors';

    protected $fillable = [
        'vendor_id',
        'payment_terms',
        'payment_terms_days',
        'credit_limit',
        'current_balance',
        'preferred_payment_method',
        'bank_name',
        'bank_account_number',
        'bank_routing_number',
        'iban',
        'swift_code',
        'default_expense_account_code',
        'is_1099_vendor',
        'ytd_payments',
        'tax_classification',
        'preferred_categories',
        'minimum_order_amount',
        'requires_po',
        'lead_time_days',
        'average_rating',
        'total_orders',
        'total_spent',
        'last_order_date',
        'last_payment_date',
        'auto_approve_invoices',
        'auto_approve_limit',
        'default_gl_account',
        'cost_center',
        'department',
        'required_documents',
        'contract_start_date',
        'contract_end_date',
        'background_check_required',
        'insurance_required',
        'insurance_expiry_date',
        'finance_metadata',
        'created_by',
        'updated_by',
        'is_active'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'ytd_payments' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'auto_approve_limit' => 'decimal:2',
        'preferred_categories' => 'array',
        'required_documents' => 'array',
        'finance_metadata' => 'array',
        'is_1099_vendor' => 'boolean',
        'requires_po' => 'boolean',
        'auto_approve_invoices' => 'boolean',
        'background_check_required' => 'boolean',
        'insurance_required' => 'boolean',
        'is_active' => 'boolean',
        'last_order_date' => 'date',
        'last_payment_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'insurance_expiry_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'bank_account_number',
        'bank_routing_number',
        'iban',
        'swift_code',
        'finance_metadata'
    ];

    /**
     * Get the vendor that owns this finance vendor record.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the user who created this finance vendor record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this finance vendor record.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Validation rules for the finance vendor model
     */
    public static function validationRules(): array
    {
        return [
            'vendor_id' => 'required|exists:vendors,id|unique:finance_vendors,vendor_id',
            'payment_terms' => 'nullable|string|max:100',
            'payment_terms_days' => 'nullable|integer|min:0|max:365',
            'credit_limit' => 'nullable|numeric|min:0',
            'current_balance' => 'nullable|numeric',
            'preferred_payment_method' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_routing_number' => 'nullable|string|max:20',
            'iban' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:20',
            'default_expense_account_code' => 'nullable|string|max:20',
            'is_1099_vendor' => 'boolean',
            'ytd_payments' => 'nullable|numeric|min:0',
            'tax_classification' => 'nullable|string|max:100',
            'preferred_categories' => 'nullable|array',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'requires_po' => 'boolean',
            'lead_time_days' => 'nullable|integer|min:0|max:365',
            'average_rating' => 'nullable|numeric|min:0|max:5',
            'total_orders' => 'nullable|integer|min:0',
            'total_spent' => 'nullable|numeric|min:0',
            'last_order_date' => 'nullable|date',
            'last_payment_date' => 'nullable|date',
            'auto_approve_invoices' => 'boolean',
            'auto_approve_limit' => 'nullable|numeric|min:0',
            'default_gl_account' => 'nullable|string|max:50',
            'cost_center' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'required_documents' => 'nullable|array',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'background_check_required' => 'boolean',
            'insurance_required' => 'boolean',
            'insurance_expiry_date' => 'nullable|date|after:today',
            'finance_metadata' => 'nullable|array',
            'created_by' => 'nullable|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
            'is_active' => 'boolean'
        ];
    }

    /**
     * Get searchable columns for this model
     */
    public static function getSearchableColumns(): array
    {
        return [
            'payment_terms',
            'preferred_payment_method',
            'default_expense_account_code',
            'tax_classification',
            'default_gl_account',
            'cost_center',
            'department'
        ];
    }

    /**
     * Get the permission access configuration for this model.
     */
    public static function getPermissionAccess(): array
    {
        return [
            'globalRead' => [
                'description' => 'Full access to read all finance vendor records',
                'conditions' => []
            ],
            'canReadActive' => [
                'description' => 'Access to read active finance vendor records only',
                'conditions' => [
                    'status_filter' => 'active',
                    'respect_lock' => true
                ]
            ],
            'canReadDepartment' => [
                'description' => 'Access to read finance vendors in same department',
                'conditions' => [
                    'department_based' => true,
                    'respect_lock' => true
                ]
            ]
        ];
    }

    /**
     * Scope a query to only include active finance vendors.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include 1099 vendors.
     */
    public function scopeIs1099($query)
    {
        return $query->where('is_1099_vendor', true);
    }

    /**
     * Scope a query to filter by department.
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope a query to filter by cost center.
     */
    public function scopeByCostCenter($query, $costCenter)
    {
        return $query->where('cost_center', $costCenter);
    }

    /**
     * Check if the vendor has expired insurance.
     */
    public function hasExpiredInsurance(): bool
    {
        return $this->insurance_required && 
               $this->insurance_expiry_date && 
               $this->insurance_expiry_date->isPast();
    }

    /**
     * Check if the vendor contract has expired.
     */
    public function hasExpiredContract(): bool
    {
        return $this->contract_end_date && $this->contract_end_date->isPast();
    }

    /**
     * Check if vendor is over credit limit.
     */
    public function isOverCreditLimit(): bool
    {
        return $this->credit_limit && $this->current_balance > $this->credit_limit;
    }
}