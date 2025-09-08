<?php

namespace Panadero\Erp\Hr\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Panadero\Erp\Hr\Models\Employee;
use Panadero\Erp\Hr\Models\Vacancy;
use Panadero\Erp\Hr\Models\Application;
use Panadero\Erp\Hr\Models\Department;
use Panadero\Erp\Hr\Services\AIService;
use Panadero\Erp\Hr\Services\ReportService;

class HrController extends Controller
{
    protected $aiService;
    protected $reportService;

    public function __construct(AIService $aiService, ReportService $reportService)
    {
        $this->aiService = $aiService;
        $this->reportService = $reportService;
    }

    /**
     * Display the HR dashboard
     */
    public function index(): View
    {
        $stats = [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::where('status', 'active')->count(),
            'total_vacancies' => Vacancy::where('status', 'published')->count(),
            'total_applications' => Application::count(),
            'pending_applications' => Application::whereIn('status', ['submitted', 'screening'])->count(),
        ];

        return view('hr.dashboard', compact('stats'));
    }

    /**
     * Display employee management page
     */
    public function employees(): View
    {
        $employees = Employee::with('department')->paginate(25);
        $departments = Department::all();

        return view('hr.employees', compact('employees', 'departments'));
    }

    /**
     * Store a new employee
     */
    public function storeEmployee(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:hr_employees,email',
            'phone' => 'nullable|string|max:20',
            'department_id' => 'required|exists:hr_departments,id',
            'position' => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,contract,intern,consultant',
            'salary' => 'nullable|numeric|min:0',
        ]);

        $employee = Employee::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully',
            'employee' => $employee
        ]);
    }

    /**
     * Update an employee
     */
    public function updateEmployee(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:hr_employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'department_id' => 'required|exists:hr_departments,id',
            'position' => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,contract,intern,consultant',
            'salary' => 'nullable|numeric|min:0',
        ]);

        $employee->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully',
            'employee' => $employee
        ]);
    }

    /**
     * Delete an employee
     */
    public function deleteEmployee(Employee $employee): JsonResponse
    {
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully'
        ]);
    }

    /**
     * Display vacancy management page
     */
    public function vacancies(): View
    {
        $vacancies = Vacancy::with('department')->paginate(25);
        $departments = Department::all();

        return view('hr.vacancies', compact('vacancies', 'departments'));
    }

    /**
     * Store a new vacancy
     */
    public function storeVacancy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'department_id' => 'required|exists:hr_departments,id',
            'position_type' => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,contract,intern,consultant',
            'experience_level' => 'required|in:entry,mid,senior,executive',
            'location' => 'nullable|string|max:255',
            'remote_allowed' => 'boolean',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,published,closed,cancelled',
        ]);

        $vacancy = Vacancy::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vacancy created successfully',
            'vacancy' => $vacancy
        ]);
    }

    /**
     * Update a vacancy
     */
    public function updateVacancy(Request $request, Vacancy $vacancy): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'department_id' => 'required|exists:hr_departments,id',
            'position_type' => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,contract,intern,consultant',
            'experience_level' => 'required|in:entry,mid,senior,executive',
            'location' => 'nullable|string|max:255',
            'remote_allowed' => 'boolean',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,published,closed,cancelled',
        ]);

        $vacancy->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vacancy updated successfully',
            'vacancy' => $vacancy
        ]);
    }

    /**
     * Publish a vacancy
     */
    public function publishVacancy(Vacancy $vacancy): JsonResponse
    {
        $vacancy->update(['status' => 'published']);

        return response()->json([
            'success' => true,
            'message' => 'Vacancy published successfully'
        ]);
    }

    /**
     * Close a vacancy
     */
    public function closeVacancy(Vacancy $vacancy): JsonResponse
    {
        $vacancy->update(['status' => 'closed']);

        return response()->json([
            'success' => true,
            'message' => 'Vacancy closed successfully'
        ]);
    }

    /**
     * Display recruitment page
     */
    public function recruitment(): View
    {
        $applications = Application::with('vacancy')->paginate(25);
        $vacancies = Vacancy::where('status', 'published')->get();

        return view('hr.recruitment', compact('applications', 'vacancies'));
    }

    /**
     * Display applications
     */
    public function applications(): JsonResponse
    {
        $applications = Application::with('vacancy')->paginate(25);

        return response()->json($applications);
    }

    /**
     * Store a new application
     */
    public function storeApplication(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vacancy_id' => 'required|exists:hr_vacancies,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'cover_letter' => 'nullable|string',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($request->hasFile('resume')) {
            $validated['resume_path'] = $request->file('resume')->store('resumes', 'public');
        }

        $application = Application::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully',
            'application' => $application
        ]);
    }

    /**
     * Update application status
     */
    public function updateApplicationStatus(Request $request, Application $application): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:submitted,screening,interview,offer,rejected,withdrawn'
        ]);

        $application->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Application status updated successfully'
        ]);
    }

    /**
     * Display department management page
     */
    public function departments(): View
    {
        $departments = Department::withCount('employees')->paginate(25);

        return view('hr.departments', compact('departments'));
    }

    /**
     * Store a new department
     */
    public function storeDepartment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:10|unique:hr_departments,code',
            'budget' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,archived',
        ]);

        $department = Department::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Department created successfully',
            'department' => $department
        ]);
    }

    /**
     * Update a department
     */
    public function updateDepartment(Request $request, Department $department): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:10|unique:hr_departments,code,' . $department->id,
            'budget' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,archived',
        ]);

        $department->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully',
            'department' => $department
        ]);
    }

    /**
     * Display reports page
     */
    public function reports(): View
    {
        return view('hr.reports');
    }

    /**
     * Generate a report
     */
    public function generateReport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'report_type' => 'required|string',
            'period' => 'required|string',
            'filters' => 'nullable|array'
        ]);

        $report = $this->reportService->generate($validated['report_type'], $validated['period'], $validated['filters'] ?? []);

        return response()->json([
            'success' => true,
            'report' => $report
        ]);
    }

    /**
     * Export a report
     */
    public function exportReport(Request $request, $reportId): JsonResponse
    {
        $format = $request->get('format', 'pdf');
        $report = $this->reportService->export($reportId, $format);

        return response()->json([
            'success' => true,
            'download_url' => $report['url']
        ]);
    }

    /**
     * Display agent portal
     */
    public function agentPortal(): View
    {
        return view('hr.agent-portal');
    }

    /**
     * Execute AI prompt
     */
    public function executeAIPrompt(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
            'context' => 'nullable|array'
        ]);

        $result = $this->aiService->executePrompt($validated['prompt'], $validated['context'] ?? []);

        return response()->json([
            'success' => true,
            'result' => $result
        ]);
    }

    /**
     * Analyze resume with AI
     */
    public function analyzeResume(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'resume_path' => 'required|string',
            'vacancy_id' => 'nullable|exists:hr_vacancies,id'
        ]);

        $analysis = $this->aiService->analyzeResume($validated['resume_path'], $validated['vacancy_id'] ?? null);

        return response()->json([
            'success' => true,
            'analysis' => $analysis
        ]);
    }

    /**
     * Match candidates with AI
     */
    public function matchCandidates(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vacancy_id' => 'required|exists:hr_vacancies,id',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        $matches = $this->aiService->matchCandidates($validated['vacancy_id'], $validated['limit'] ?? 10);

        return response()->json([
            'success' => true,
            'matches' => $matches
        ]);
    }

    /**
     * Display careers page (public)
     */
    public function careers(): View
    {
        $vacancies = Vacancy::where('status', 'published')->paginate(12);

        return view('hr.careers', compact('vacancies'));
    }

    /**
     * Show specific vacancy (public)
     */
    public function showVacancy(Vacancy $vacancy): View
    {
        return view('hr.vacancy-detail', compact('vacancy'));
    }

    /**
     * Apply for job (public)
     */
    public function applyForJob(Request $request, Vacancy $vacancy): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'cover_letter' => 'nullable|string',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $validated['vacancy_id'] = $vacancy->id;
        $validated['resume_path'] = $request->file('resume')->store('resumes', 'public');

        $application = Application::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully',
            'application' => $application
        ]);
    }
}
