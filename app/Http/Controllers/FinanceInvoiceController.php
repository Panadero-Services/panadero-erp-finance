<?php

namespace App\Http\Controllers;

use App\Models\FinanceInvoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FinanceInvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request): JsonResponse
    {
        $query = FinanceInvoice::query();

        // Filter by section
        if ($request->has('section')) {
            $query->bySection($request->section);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->byType($request->type);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Search by invoice number or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $invoices = $query->with(['creator', 'updater', 'approver'])
                         ->orderBy('created_at', 'desc')
                         ->paginate($perPage);

        return response()->json($invoices);
    }

    /**
     * Store a newly created invoice.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), FinanceInvoice::rules());

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $invoice = FinanceInvoice::create([
                'invoice_number' => $request->invoice_number ?? FinanceInvoice::generateInvoiceNumber($request->section),
                'section' => $request->section,
                'type' => $request->type,
                'category' => $request->category,
                'description' => $request->description,
                'status' => $request->status ?? 'draft',
                'amount' => $request->amount ?? 0,
                'metadata' => $request->metadata ?? [],
                'reference_id' => $request->reference_id,
                'reference_type' => $request->reference_type,
                'created_by' => Auth::id()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Invoice created successfully',
                'invoice' => $invoice->load(['creator'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified invoice.
     */
    public function show(string $invoice_number): JsonResponse
    {
        $invoiceModel = FinanceInvoice::where('invoice_number', $invoice_number)->firstOrFail();
        
        return response()->json([
            'invoice' => $invoiceModel->load(['creator', 'updater', 'approver'])
        ]);
    }

    /**
     * Update the specified invoice.
     */
    public function update(Request $request, string $invoice_number): JsonResponse
    {
        $invoiceModel = FinanceInvoice::where('invoice_number', $invoice_number)->firstOrFail();
        
        $validator = Validator::make($request->all(), [
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:draft,pending,approved,completed,cancelled',
            'amount' => 'nullable|numeric|min:0',
            'metadata' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $invoiceModel->update([
                'description' => $request->description ?? $invoiceModel->description,
                'status' => $request->status ?? $invoiceModel->status,
                'amount' => $request->amount ?? $invoiceModel->amount,
                'metadata' => $request->metadata ?? $invoiceModel->metadata,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Invoice updated successfully',
                'invoice' => $invoiceModel->fresh()->load(['creator', 'updater'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve the specified invoice.
     */
    public function approve(string $invoice_number): JsonResponse
    {
        $invoiceModel = FinanceInvoice::where('invoice_number', $invoice_number)->firstOrFail();
        
        if ($invoiceModel->isApproved()) {
            return response()->json([
                'message' => 'Invoice is already approved'
            ], 400);
        }

        try {
            $invoiceModel->approve(Auth::id());

            return response()->json([
                'message' => 'Invoice approved successfully',
                'invoice' => $invoiceModel->fresh()->load(['creator', 'approver'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to approve invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark invoice as completed.
     */
    public function complete(string $invoice_number): JsonResponse
    {
        $invoiceModel = FinanceInvoice::where('invoice_number', $invoice_number)->firstOrFail();
        
        if (!$invoiceModel->isApproved()) {
            return response()->json([
                'message' => 'Invoice must be approved before marking as completed'
            ], 400);
        }

        try {
            $invoiceModel->markAsCompleted();

            return response()->json([
                'message' => 'Invoice marked as completed',
                'invoice' => $invoiceModel->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to mark invoice as completed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel the specified invoice.
     */
    public function cancel(string $invoice_number): JsonResponse
    {
        $invoiceModel = FinanceInvoice::where('invoice_number', $invoice_number)->firstOrFail();
        
        if ($invoiceModel->isCompleted()) {
            return response()->json([
                'message' => 'Cannot cancel completed invoice'
            ], 400);
        }

        try {
            $invoiceModel->cancel();

            return response()->json([
                'message' => 'Invoice cancelled successfully',
                'invoice' => $invoiceModel->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to cancel invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified invoice.
     */
    public function destroy(string $invoice_number): JsonResponse
    {
        $invoiceModel = FinanceInvoice::where('invoice_number', $invoice_number)->firstOrFail();
        
        try {
            $invoiceModel->delete();

            return response()->json([
                'message' => 'Invoice deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice statistics.
     */
    public function stats(Request $request): JsonResponse
    {
        $query = FinanceInvoice::query();

        if ($request->has('section')) {
            $query->bySection($request->section);
        }

        $stats = [
            'total' => $query->count(),
            'draft' => $query->clone()->byStatus('draft')->count(),
            'pending' => $query->clone()->byStatus('pending')->count(),
            'approved' => $query->clone()->byStatus('approved')->count(),
            'completed' => $query->clone()->byStatus('completed')->count(),
            'cancelled' => $query->clone()->byStatus('cancelled')->count(),
            'total_amount' => $query->clone()->sum('amount'),
            'pending_amount' => $query->clone()->byStatus('pending')->sum('amount'),
            'approved_amount' => $query->clone()->byStatus('approved')->sum('amount')
        ];

        return response()->json($stats);
    }

    /**
     * Export invoices to CSV.
     */
    public function export(Request $request): JsonResponse
    {
        $query = FinanceInvoice::query();

        // Apply filters
        if ($request->has('section')) {
            $query->bySection($request->section);
        }
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        $invoices = $query->with(['creator'])
                         ->orderBy('created_at', 'desc')
                         ->get();

        // Generate CSV content
        $csvData = [];
        $csvData[] = ['Invoice Number', 'Section', 'Type', 'Category', 'Description', 'Amount', 'Status', 'Created By', 'Created Date'];

        foreach ($invoices as $invoice) {
            $csvData[] = [
                $invoice->invoice_number,
                $invoice->section,
                $invoice->type,
                $invoice->category,
                $invoice->description,
                $invoice->amount,
                $invoice->status,
                $invoice->creator?->name ?? 'System',
                $invoice->created_at->format('Y-m-d H:i:s')
            ];
        }

        return response()->json([
            'message' => 'CSV data generated',
            'data' => $csvData,
            'filename' => 'invoices-' . date('Y-m-d') . '.csv'
        ]);
    }
}
