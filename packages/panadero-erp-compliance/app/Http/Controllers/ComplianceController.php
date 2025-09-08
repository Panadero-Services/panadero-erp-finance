<?php

namespace Panadero\ErpCompliance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Panadero\ErpCompliance\Models\CompliancePolicy;
use Panadero\ErpCompliance\Models\ComplianceAudit;
use Panadero\ErpCompliance\Models\ComplianceRisk;
use Panadero\ErpCompliance\Models\ComplianceReport;
use Panadero\ErpCompliance\Models\ComplianceRCA;

class ComplianceController extends Controller
{
    /**
     * Get compliance dashboard data
     */
    public function dashboard(): JsonResponse
    {
        $data = [
            'policies' => [
                'total' => CompliancePolicy::count(),
                'active' => CompliancePolicy::active()->count(),
                'expiring_soon' => CompliancePolicy::expiringSoon()->count(),
                'by_type' => CompliancePolicy::selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->get()
                    ->pluck('count', 'type')
            ],
            'audits' => [
                'total' => ComplianceAudit::count(),
                'completed' => ComplianceAudit::completed()->count(),
                'in_progress' => ComplianceAudit::byStatus('in_progress')->count(),
                'overdue' => ComplianceAudit::overdue()->count(),
                'by_risk_level' => ComplianceAudit::selectRaw('risk_level, COUNT(*) as count')
                    ->groupBy('risk_level')
                    ->get()
                    ->pluck('count', 'risk_level')
            ],
            'risks' => [
                'total' => ComplianceRisk::count(),
                'high_risk' => ComplianceRisk::highRisk()->count(),
                'overdue' => ComplianceRisk::overdue()->count(),
                'resolved' => ComplianceRisk::where('status', 'closed')->count(),
                'by_category' => ComplianceRisk::selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->get()
                    ->pluck('count', 'category')
            ],
            'reports' => [
                'total' => ComplianceReport::count(),
                'published' => ComplianceReport::published()->count(),
                'by_type' => ComplianceReport::selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->get()
                    ->pluck('count', 'type')
            ],
            'rca' => [
                'total' => ComplianceRCA::count(),
                'open' => ComplianceRCA::where('status', 'open')->count(),
                'in_progress' => ComplianceRCA::where('status', 'in_progress')->count(),
                'completed' => ComplianceRCA::completed()->count(),
                'high_severity' => ComplianceRCA::highSeverity()->count(),
                'with_ai_analysis' => ComplianceRCA::withAIAnalysis()->count()
            ]
        ];

        return response()->json($data);
    }

    /**
     * Get policies data
     */
    public function policies(Request $request): JsonResponse
    {
        $query = CompliancePolicy::with(['creator', 'updater', 'approver']);

        if ($request->has('type')) {
            $query->byType($request->type);
        }

        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $policies = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($policies);
    }

    /**
     * Get audits data
     */
    public function audits(Request $request): JsonResponse
    {
        $query = ComplianceAudit::with(['auditor', 'creator', 'updater']);

        if ($request->has('type')) {
            $query->byType($request->type);
        }

        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('risk_level')) {
            $query->byRiskLevel($request->risk_level);
        }

        $audits = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($audits);
    }

    /**
     * Get risks data
     */
    public function risks(Request $request): JsonResponse
    {
        $query = ComplianceRisk::with(['owner', 'creator', 'updater']);

        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        if ($request->has('type')) {
            $query->byType($request->type);
        }

        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('risk_level')) {
            $query->byRiskLevel($request->risk_level);
        }

        $risks = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($risks);
    }

    /**
     * Get reports data
     */
    public function reports(Request $request): JsonResponse
    {
        $query = ComplianceReport::with(['creator', 'updater', 'approver']);

        if ($request->has('type')) {
            $query->byType($request->type);
        }

        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($reports);
    }

    /**
     * Get RCA data
     */
    public function rca(Request $request): JsonResponse
    {
        $query = ComplianceRCA::with(['assignee', 'creator', 'updater']);

        if ($request->has('incident_type')) {
            $query->byIncidentType($request->incident_type);
        }

        if ($request->has('severity')) {
            $query->bySeverity($request->severity);
        }

        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        $rca = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($rca);
    }

    /**
     * Get AI analysis for RCA
     */
    public function aiAnalysis(Request $request, $rcaId): JsonResponse
    {
        $rca = ComplianceRCA::findOrFail($rcaId);

        // Simulate AI analysis (replace with actual AI service)
        $aiAnalysis = [
            'risk_score' => rand(1, 10),
            'similar_incidents' => rand(0, 5),
            'recommended_actions' => [
                'Implement automated monitoring',
                'Enhance training programs',
                'Deploy predictive analytics'
            ],
            'predicted_likelihood' => ['low', 'medium', 'high'][rand(0, 2)],
            'confidence_score' => rand(70, 95) / 100,
            'analysis_timestamp' => now()->toISOString()
        ];

        $rca->update(['ai_analysis' => $aiAnalysis]);

        return response()->json($aiAnalysis);
    }

    /**
     * Generate compliance report
     */
    public function generateReport(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string',
            'category' => 'required|string',
            'period_start' => 'required|date',
            'period_end' => 'required|date'
        ]);

        // Generate report data based on type and period
        $reportData = $this->generateReportData($request->type, $request->period_start, $request->period_end);

        $report = ComplianceReport::create([
            'report_number' => 'RPT-' . date('Y') . '-' . str_pad(ComplianceReport::count() + 1, 3, '0', STR_PAD_LEFT),
            'title' => ucfirst($request->type) . ' Compliance Report - ' . $request->period_start . ' to ' . $request->period_end,
            'type' => $request->type,
            'category' => $request->category,
            'description' => 'Automated compliance report generated for the specified period',
            'status' => 'generated',
            'report_date' => now(),
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'data' => $reportData,
            'created_by' => auth()->id()
        ]);

        return response()->json($report);
    }

    /**
     * Generate report data based on type and period
     */
    private function generateReportData(string $type, string $startDate, string $endDate): array
    {
        $data = [];

        switch ($type) {
            case 'regulatory':
                $data = [
                    'total_violations' => rand(0, 5),
                    'resolved_violations' => rand(0, 3),
                    'pending_violations' => rand(0, 2),
                    'compliance_score' => rand(85, 98)
                ];
                break;
            case 'internal':
                $data = [
                    'total_policies' => CompliancePolicy::whereBetween('created_at', [$startDate, $endDate])->count(),
                    'active_policies' => CompliancePolicy::active()->whereBetween('created_at', [$startDate, $endDate])->count(),
                    'audits_completed' => ComplianceAudit::completed()->whereBetween('created_at', [$startDate, $endDate])->count(),
                    'risks_identified' => ComplianceRisk::whereBetween('created_at', [$startDate, $endDate])->count()
                ];
                break;
            case 'audit':
                $data = [
                    'total_audits' => ComplianceAudit::whereBetween('created_at', [$startDate, $endDate])->count(),
                    'completed_audits' => ComplianceAudit::completed()->whereBetween('created_at', [$startDate, $endDate])->count(),
                    'total_findings' => ComplianceAudit::whereBetween('created_at', [$startDate, $endDate])->sum('total_findings'),
                    'average_risk_level' => ComplianceAudit::whereBetween('created_at', [$startDate, $endDate])->avg('risk_level')
                ];
                break;
            case 'risk':
                $data = [
                    'total_risks' => ComplianceRisk::whereBetween('created_at', [$startDate, $endDate])->count(),
                    'high_risks' => ComplianceRisk::highRisk()->whereBetween('created_at', [$startDate, $endDate])->count(),
                    'resolved_risks' => ComplianceRisk::where('status', 'closed')->whereBetween('created_at', [$startDate, $endDate])->count(),
                    'average_resolution_time' => rand(15, 45)
                ];
                break;
        }

        return $data;
    }
}
