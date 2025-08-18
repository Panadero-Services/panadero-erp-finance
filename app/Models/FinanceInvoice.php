<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FinanceInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'section',
        'type',
        'category',
        'description',
        'status',
        'amount',
        'metadata',
        'reference_id',
        'reference_type',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'approved_at' => 'datetime',
    ];

    protected $dates = [
        'approved_at'
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeBySection($query, $section)
    {
        return $query->where('section', $section);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // Methods
    public function approve($userId)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now()
        ]);
    }

    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    // Static methods for invoice number generation
    public static function generateInvoiceNumber($section): string
    {
        $prefixes = [
            'AP' => 'AP-INV',
            'AR' => 'AR-INV',
            'GL' => 'GL-JE',
            'CF' => 'CF-TXN',
            'TX' => 'TX-REC',
            'FA' => 'FA-ASST',
            'BD' => 'BD-BGT',
            'AU' => 'AU-LOG'
        ];

        $prefix = $prefixes[$section] ?? 'INV';
        
        // Get the last invoice number for this section
        $lastInvoice = static::where('section', $section)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            // Extract the number part and increment
            $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastInvoice->invoice_number);
            $nextNumber = $lastNumber + 1;
        } else {
            // Start with 1 for new sections
            $nextNumber = 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    // Validation rules
    public static function rules(): array
    {
        return [
            'section' => 'required|string|max:10',
            'type' => 'required|string|max:50',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'metadata' => 'nullable|array',
            'reference_id' => 'nullable|string',
            'reference_type' => 'nullable|string'
        ];
    }
}

