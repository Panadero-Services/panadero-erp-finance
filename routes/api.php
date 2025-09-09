<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Models\Project;
use App\Models\Future;
use App\Models\BusinessService;
use App\Models\I1Order;
use App\Models\User;
use App\Http\Controllers\SomethingController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ModelConfigController;
use App\Http\Controllers\DynamicController;
use App\Http\Controllers\MasterGameServerController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\ResourceController;
use App\Http\Middleware\VerifyFastApiKey;
use App\Http\Middleware\EnsureTokenIsValid;
use App\Http\Resources\UserResource;
use OpenAI\Client;
use OpenAI\Transporters\HttpTransporter;
use OpenAI\ValueObjects\Transporter\BaseUri;
use OpenAI\ValueObjects\Transporter\Headers;
use OpenAI\ValueObjects\ApiKey;
use OpenAI\ValueObjects\Transporter\QueryParams;
use GuzzleHttp\Client as GuzzleClient;

use App\Http\Controllers\Finance\FixedAssetController;
use App\Http\Controllers\Finance\BudgetController;
use App\Http\Controllers\Finance\AuditLogController;




/*
|--------------------------------------------------------------------------
| SPECIFIC API ROUTES
|--------------------------------------------------------------------------
| These are dedicated routes for specific functionality that cannot be
| handled by the dynamic system
*/

// ============================================================================
// FINANCE INVOICE SYSTEM ROUTES
// ============================================================================
// Test route without authentication
Route::get('/finance/test', function () {
    return response()->json([
        'message' => 'Finance test route working',
        'timestamp' => now(),
        'cookies' => request()->cookies->all()
    ]);
});

// Test finance invoices route without authentication
Route::get('/finance/invoices/test', function () {
    return response()->json([
        'message' => 'Finance invoices test route working',
        'timestamp' => now(),
        'cookies' => request()->cookies->all(),
        'headers' => request()->headers->all()
    ]);
});

// Add this to api.php


// ============================================================================
// PROVIDER ROUTES
// ============================================================================
Route::apiResource('providers', ProviderController::class);
Route::get('/providers/{provider}/balance', [ProviderController::class, 'getContractBalance'])
    ->name('providers.balance');
Route::post('/providers/{provider}/test', [ProviderController::class, 'test'])
    ->name('providers.test');

// ============================================================================
// BOT ROUTES
// ============================================================================
Route::prefix('bots')->group(function () {
    Route::get('/', [BotController::class, 'index']);
    Route::post('/', [BotController::class, 'store']);
    Route::delete('/{id}', [BotController::class, 'destroy']);
    Route::post('/{id}/start', [BotController::class, 'start']);
    Route::post('/{id}/stop', [BotController::class, 'stop']);
    Route::get('/{id}/status', [BotController::class, 'status']);
    // Note: Bot updates now use dynamic route: PUT /api/bots/{id}
});

// ============================================================================
// AI CHAT ROUTES
// ============================================================================

// ERP Module-specific AI Chat Routes with Intelligent Model Routing
Route::post('/ai/chat/finance', function (Request $request) {
    try {
        $message = $request->input('message');
        $context = $request->input('context', []);
        $startTime = microtime(true);
        
        // Use AI Model Router to determine which model to use
        $router = new \App\Services\AIModelRouter();
        $modelDecision = $router->determineModel($message, 'finance');
        $systemPrompt = $router->getSystemPrompt($modelDecision['model'], 'finance');
        
        // Use concise system prompt for better performance
        $enhancedPrompt = $systemPrompt;
        
        // Call AI service with intelligent model selection
        $response = app()->call('App\Http\Controllers\AIServiceController@callAI', [
            'request' => new Request([
                'provider' => 'ollama',
                'prompt' => $message,
                'context' => array_merge($context, ['module' => 'finance']),
                'config' => [
                    'model' => $modelDecision['ollama_model'],
                    'baseUrl' => 'http://localhost:11434',
                    'maxTokens' => 500,
                    'temperature' => 0.7,
                    'systemPrompt' => $enhancedPrompt
                ]
            ])
        ]);
        
        // Store training data
        $responseTime = (microtime(true) - $startTime) * 1000;
        $responseData = $response->getData(true);
        
        \App\Models\AITrainingData::create([
            'user_query' => $message,
            'ai_response' => $responseData['response'] ?? 'Error: No response',
            'model_used' => $modelDecision['model'],
            'module' => 'finance',
            'confidence_score' => $modelDecision['confidence'],
            'session_id' => session()->getId(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'response_time_ms' => round($responseTime),
            'query_classification' => $modelDecision['model'],
            'training_approved' => false
        ]);
        
        // Add model decision info to response
        $responseArray = $response->getData(true);
        $responseArray['model_info'] = [
            'model_used' => $modelDecision['model'],
            'ollama_model' => $modelDecision['ollama_model'],
            'confidence' => $modelDecision['confidence'],
            'reason' => $modelDecision['reason']
        ];
        
        return response()->json($responseArray);
    } catch (\Exception $e) {
        \Log::error('Finance AI Chat Error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to generate AI response'], 500);
    }
});

Route::post('/ai/chat/hr', function (Request $request) {
    try {
        $message = $request->input('message');
        $context = $request->input('context', []);
        
        $workflowDocs = [
            'recruitment' => 'Recruitment Workflow: Complete recruitment process from job requisition to offer acceptance. Steps: Job Requisition, Posting, Application Review, Interview Process, Background Check, Offer. Time: 2-4 weeks.',
            'onboarding' => 'Employee Onboarding: Comprehensive employee onboarding from first day to full integration. Steps: Welcome Package, Documentation, System Access, Training Schedule, Orientation, Integration. Time: 1-2 weeks.',
            'performance_review' => 'Performance Review: Annual performance review cycle with continuous feedback. Steps: Goal Setting, Mid-year Check, Self Assessment, Manager Review, Calibration, Final Review. Time: 3 months.',
            'learning_development' => 'Learning & Development: Employee learning and development journey tracking. Steps: Skills Assessment, Training Plan, Course Enrollment, Progress Tracking, Certification, Career Planning. Time: 3-6 months.',
            'time_attendance' => 'Time & Attendance: Time and attendance management with automated approvals. Steps: Schedule Setup, Time Entry, Approval Process, Payroll Integration. Time: Daily.',
            'employee_relations' => 'Employee Relations: Employee relations case management and resolution. Steps: Issue Report, Investigation, Documentation, Resolution, Follow-up. Time: 1-4 weeks.'
        ];
        
        $systemPrompt = "You are an expert ERP HR AI Assistant with comprehensive knowledge of human resources processes and workflows. You help users with:

HR MODULES:
• Recruitment & Hiring - Job posting, applicant tracking, interview management
• Employee Management - Complete employee lifecycle from onboarding to offboarding
• Performance Management - Goal setting, reviews, development planning
• Learning & Development - Training programs, skill tracking, career paths
• Time & Attendance - Schedule management, time tracking, leave management
• Payroll & Benefits - Compensation management and benefits administration
• Employee Relations - Conflict resolution, disciplinary actions, engagement

WORKFLOW EXPERTISE:
{$workflowDocs['recruitment']}
{$workflowDocs['onboarding']}
{$workflowDocs['performance_review']}
{$workflowDocs['learning_development']}

CAPABILITIES:
- Guide through hiring and recruitment processes
- Explain performance management workflows
- Assist with employee onboarding procedures
- Provide HR policy and compliance guidance
- Help with workforce planning and analytics

Be helpful, professional, and provide specific guidance based on HR best practices and your workflow knowledge.";

        return app()->call('App\Http\Controllers\AIServiceController@callAI', [
            'request' => new Request([
                'provider' => 'ollama',
                'prompt' => $message,
                'context' => array_merge($context, ['module' => 'hr']),
                'config' => [
                    'model' => 'llama3.2:3b',
                    'baseUrl' => 'http://localhost:11434',
                    'maxTokens' => 500,
                    'temperature' => 0.7,
                    'systemPrompt' => $systemPrompt
                ]
            ])
        ]);
    } catch (\Exception $e) {
        \Log::error('HR AI Chat Error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to generate AI response'], 500);
    }
});

Route::post('/ai/chat/inventory', function (Request $request) {
    try {
        $message = $request->input('message');
        $context = $request->input('context', []);
        
        $workflowDocs = [
            'purchase_order' => 'Purchase Order Workflow: Complete purchase order workflow with supplier approval. Steps: PR Creation, Manager Approval, Supplier Selection, PO Generation. Time: 2-3 days.',
            'goods_receipt' => 'Goods Receipt: Complete receiving workflow with quality inspection. Steps: Delivery Notification, Physical Inspection, PO Matching, Receipt Recording, Inventory Update. Time: 1-2 days.',
            'supplier_onboarding' => 'Supplier Onboarding: New supplier registration and validation process. Steps: Supplier Registration, Document Verification, Credit Check, Quality Assessment, Procurement Approval, System Setup. Time: 1-2 weeks.',
            'pick_pack_ship' => 'Pick Pack Ship: Complete order fulfillment from picking to shipping. Steps: Pick List Generation, Order Picking, Pick Verification, Order Packing, Shipment Creation. Time: 4-6 hours.',
            'cycle_counting' => 'Cycle Counting: Systematic inventory counting and variance resolution. Steps: Count Schedule, Physical Count, Variance Analysis, Inventory Adjustment. Time: 1 day.',
            'inventory_valuation' => 'Inventory Valuation: Complete inventory valuation for financial reporting. Steps: Data Gathering, Valuation Calculation, Variance Analysis, Finance Review, Controller Approval. Time: 1-2 days.'
        ];
        
        $systemPrompt = "You are an expert ERP Inventory AI Assistant with deep knowledge of inventory management and supply chain processes. You help users with:

INVENTORY MODULES:
• Procurement - Purchase orders, supplier management, receiving
• Warehouse Management - Location tracking, putaway, picking optimization
• Order Fulfillment - Pick, pack, ship with carrier integration
• Inventory Control - Cycle counting, adjustments, ABC analysis
• Quality Control - Inspection workflows and quality assurance
• Reporting & Analytics - Real-time dashboards and inventory insights

WORKFLOW EXPERTISE:
{$workflowDocs['purchase_order']}
{$workflowDocs['goods_receipt']}
{$workflowDocs['supplier_onboarding']}
{$workflowDocs['pick_pack_ship']}

CAPABILITIES:
- Guide through procurement and purchasing processes
- Explain warehouse management workflows
- Assist with inventory optimization strategies
- Provide supplier management guidance
- Help with stock level planning and forecasting

Be specific, actionable, and provide detailed guidance based on inventory management best practices.";

        return app()->call('App\Http\Controllers\AIServiceController@callAI', [
            'request' => new Request([
                'provider' => 'ollama',
                'prompt' => $message,
                'context' => array_merge($context, ['module' => 'inventory']),
                'config' => [
                    'model' => 'llama3.2:3b',
                    'baseUrl' => 'http://localhost:11434',
                    'maxTokens' => 500,
                    'temperature' => 0.7,
                    'systemPrompt' => $systemPrompt
                ]
            ])
        ]);
    } catch (\Exception $e) {
        \Log::error('Inventory AI Chat Error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to generate AI response'], 500);
    }
});

Route::post('/ai/chat/compliance', function (Request $request) {
    try {
        $message = $request->input('message');
        $context = $request->input('context', []);
        
        $workflowDocs = [
            'policy_approval' => 'Policy Approval: Automated policy review and approval workflow with stakeholder notifications. Steps: Policy Draft, Legal Review, Stakeholder Approval, Final Approval, Employee Notification. Time: 1-2 weeks.',
            'audit_scheduling' => 'Audit Scheduling: Intelligent audit scheduling based on risk levels and compliance requirements. Steps: Risk Assessment, Auditor Assignment, Timeline Creation, Stakeholder Notification, Audit Execution. Time: 2-3 weeks.',
            'risk_assessment' => 'Risk Assessment: Automated risk assessment workflows with escalation and mitigation tracking. Steps: Risk Identification, AI-powered Scoring, Risk Owner Assignment, Mitigation Strategy, Implementation Tracking. Time: 1-2 weeks.',
            'report_generation' => 'Report Generation: Automated compliance report generation and distribution to stakeholders. Steps: Data Collection, AI Analysis, Template Selection, Report Generation, Stakeholder Distribution. Time: 1-2 days.',
            'alert_management' => 'Alert Management: Smart alerting system with customizable thresholds and notification channels. Steps: Threshold Monitoring, Severity Assessment, Recipient Identification, Alert Delivery, Escalation Monitoring. Time: Real-time.',
            'remediation_tracking' => 'Remediation Tracking: Automated tracking of remediation efforts with progress monitoring and reporting. Steps: Issue Identification, Resource Allocation, Progress Monitoring, Quality Assurance, Closure Documentation. Time: 2-8 weeks.'
        ];
        
        $systemPrompt = "You are an expert ERP Compliance AI Assistant with comprehensive knowledge of regulatory compliance, risk management, and audit processes. You help users with:

COMPLIANCE MODULES:
• Regulatory Compliance - SOX, GDPR, HIPAA, ISO standards monitoring
• Audit Trails - Tamper-proof logging, real-time monitoring, compliance reporting
• Risk Management - Automated risk scoring, mitigation strategies, continuous monitoring
• Policy Management - Centralized policy control with approval workflows
• Compliance Reporting - Automated reporting with regulatory submission capabilities
• AI-Powered Analysis - Root cause analysis and predictive compliance analytics

WORKFLOW EXPERTISE:
{$workflowDocs['policy_approval']}
{$workflowDocs['audit_scheduling']}
{$workflowDocs['risk_assessment']}
{$workflowDocs['report_generation']}

RCA AGENT CAPABILITIES:
• Automated Issue Detection - Identifies compliance violations automatically
• Pattern Recognition - Analyzes historical data for trends
• Predictive Analytics - Forecasts potential compliance issues
• Actionable Recommendations - Provides specific remediation steps
• Risk Scoring - Quantifies impact and priority levels
• Compliance Forecasting - Predicts future compliance posture

Be authoritative, detail-oriented, and provide specific guidance for regulatory compliance and risk management.";

        return app()->call('App\Http\Controllers\AIServiceController@callAI', [
            'request' => new Request([
                'provider' => 'ollama',
                'prompt' => $message,
                'context' => array_merge($context, ['module' => 'compliance']),
                'config' => [
                    'model' => 'llama3.2:3b',
                    'baseUrl' => 'http://localhost:11434',
                    'maxTokens' => 500,
                    'temperature' => 0.7,
                    'systemPrompt' => $systemPrompt
                ]
            ])
        ]);
    } catch (\Exception $e) {
        \Log::error('Compliance AI Chat Error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to generate AI response'], 500);
    }
});

// AI Feedback Route
Route::post('/ai/feedback', function (Request $request) {
    try {
        $wasHelpful = $request->input('was_helpful');
        $module = $request->input('module');
        $feedback = $request->input('feedback');
        $improvementSuggestion = $request->input('improvement_suggestion');
        
        // Find the most recent AI interaction for this session
        $recentInteraction = \App\Models\AITrainingData::where('session_id', session()->getId())
            ->where('module', $module)
            ->orderBy('created_at', 'desc')
            ->first();
            
        if ($recentInteraction) {
            $recentInteraction->update([
                'was_helpful' => $wasHelpful,
                'user_feedback' => $feedback,
                'suggested_improvement' => $improvementSuggestion
            ]);
            
            return response()->json(['message' => 'Feedback recorded successfully']);
        }
        
        return response()->json(['message' => 'No recent interaction found'], 404);
    } catch (\Exception $e) {
        \Log::error('AI Feedback Error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to record feedback'], 500);
    }
});

// Training Data Export Route (for admins)
Route::get('/ai/export-training/{module?}', function ($module = null) {
    try {
        $data = \App\Models\AITrainingData::exportForTraining($module);
        
        $filename = $module ? "training-data-{$module}.jsonl" : "training-data-all.jsonl";
        
        $jsonl = $data->map(function ($item) {
            return json_encode($item);
        })->implode("\n");
        
        return response($jsonl)
            ->header('Content-Type', 'application/jsonl')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    } catch (\Exception $e) {
        \Log::error('Training Data Export Error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to export training data'], 500);
    }
});

// AI Training Data API Routes
Route::get('/ai/training-data', function (Request $request) {
    try {
        $query = \App\Models\AITrainingData::query();
        
        // Apply filters
        if ($request->has('module') && $request->module) {
            $query->where('module', $request->module);
        }
        if ($request->has('model') && $request->model) {
            $query->where('model_used', $request->model);
        }
        if ($request->has('helpful') && $request->helpful !== '') {
            if ($request->helpful === 'null') {
                $query->whereNull('was_helpful');
            } else {
                $query->where('was_helpful', (bool)$request->helpful);
            }
        }
        if ($request->has('approved') && $request->approved !== '') {
            $query->where('training_approved', (bool)$request->approved);
        }
        
        // Pagination
        $perPage = $request->get('per_page', 20);
        $page = $request->get('page', 1);
        $total = $query->count();
        $totalPages = ceil($total / $perPage);
        
        $data = $query->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();
        
        // Calculate statistics
        $stats = [
            'total' => \App\Models\AITrainingData::count(),
            'helpful_percentage' => round(\App\Models\AITrainingData::where('was_helpful', true)->count() / max(\App\Models\AITrainingData::whereNotNull('was_helpful')->count(), 1) * 100),
            'sonar_usage' => round(\App\Models\AITrainingData::where('model_used', 'sonar-semantic')->count() / max(\App\Models\AITrainingData::count(), 1) * 100),
            'avg_confidence' => round(\App\Models\AITrainingData::whereNotNull('confidence_score')->avg('confidence_score') ?? 0)
        ];
        
        return response()->json([
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_items' => $total
            ],
            'stats' => $stats
        ]);
    } catch (\Exception $e) {
        \Log::error('Training Data API Error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch training data'], 500);
    }
});

Route::post('/ai/approve-training', function (Request $request) {
    try {
        $ids = $request->input('ids', []);
        
        \App\Models\AITrainingData::whereIn('id', $ids)
            ->update(['training_approved' => true]);
        
        return response()->json(['message' => 'Training data approved successfully']);
    } catch (\Exception $e) {
        \Log::error('Training Approval Error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to approve training data'], 500);
    }
});

// AI Dashboard Metrics API
Route::get('/ai/dashboard-metrics', function (Request $request) {
    try {
        $now = now();
        $last24h = $now->copy()->subHours(24);
        $lastHour = $now->copy()->subHour();
        
        // System Status
        $status = [
            'ollama' => \App\Helpers\AIDashboardHelper::checkOllamaStatus(),
            'api' => true, // If we can reach this endpoint, API is working
            'database' => true, // If we can query, DB is working
            'model_performance' => 'Optimized'
        ];
        
        // Basic Metrics
        $totalRequests = \App\Models\AITrainingData::where('created_at', '>=', $last24h)->count();
        $successfulRequests = \App\Models\AISuccessLog::where('created_at', '>=', $last24h)->count();
        $errorRequests = \App\Models\AIErrorLog::where('created_at', '>=', $last24h)->count();
        $timeoutErrors = \App\Models\AIErrorLog::where('created_at', '>=', $last24h)
            ->where('is_timeout', true)->count();
        
        $successRate = $totalRequests > 0 ? round(($successfulRequests / $totalRequests) * 100, 1) : 0;
        $timeoutRate = $totalRequests > 0 ? round(($timeoutErrors / $totalRequests) * 100, 1) : 0;
        
        // Response Time Metrics
        $avgResponseTime = \App\Models\AISuccessLog::where('created_at', '>=', $last24h)
            ->avg('response_time_ms') ?? 0;
        
        // Model Usage
        $codellamaUsage = \App\Models\AISuccessLog::where('created_at', '>=', $last24h)
            ->where('model', 'codellama:7b')->count();
        $llamaUsage = \App\Models\AISuccessLog::where('created_at', '>=', $last24h)
            ->where('model', 'llama3.2:3b')->count();
        $totalModelUsage = $codellamaUsage + $llamaUsage;
        
        $codellamaUsagePercent = $totalModelUsage > 0 ? round(($codellamaUsage / $totalModelUsage) * 100, 1) : 0;
        $llamaUsagePercent = $totalModelUsage > 0 ? round(($llamaUsage / $totalModelUsage) * 100, 1) : 0;
        
        // Charts Data
        $responseTimeData = \App\Helpers\AIDashboardHelper::getResponseTimeChartData($last24h);
        $errorTypesData = \App\Helpers\AIDashboardHelper::getErrorTypesChartData($last24h);
        
        // Error Details
        $errorDetails = \App\Helpers\AIDashboardHelper::getErrorDetails($last24h);
        
        return response()->json([
            'status' => $status,
            'metrics' => [
                'total_requests' => $totalRequests,
                'success_rate' => $successRate,
                'avg_response_time' => round($avgResponseTime),
                'timeout_rate' => $timeoutRate,
                'codellama_usage' => $codellamaUsagePercent,
                'llama_usage' => $llamaUsagePercent,
                'requests_change' => 0, // TODO: Calculate change from previous period
                'success_change' => 0,
                'response_change' => 0,
                'timeout_change' => 0
            ],
            'charts' => [
                'response_time' => $responseTimeData,
                'error_types' => $errorTypesData
            ],
            'errors' => $errorDetails
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Dashboard Metrics Error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch dashboard metrics'], 500);
    }
});


// Original general AI chat route
Route::post('/ai/chat', function (Request $request) {
    try {
        // Get the user's message
        $message = $request->input('message');
        
        // Search for relevant posts
        $searchTerms = explode(' ', $message);
        $query = Post::query();
        
        foreach ($searchTerms as $term) {
            if (strlen($term) > 2) {
                $query->orWhere(function($q) use ($term) {
                    $q->where('title', 'like', '%' . $term . '%')
                      ->orWhere('body', 'like', '%' . $term . '%');
                });
            }
        }
        
        // Get relevant posts with their relationships
        $relevantPosts = $query->with(['user'])
            ->select(['id', 'title', 'body', 'created_at', 'user_id'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Format posts for context
        $context = '';
        if ($relevantPosts->isNotEmpty()) {
            $context = "Here are some relevant posts from our knowledge base:\n\n";
            foreach ($relevantPosts as $post) {
                $context .= "Title: {$post->title}\n";
                $context .= "Content: {$post->body}\n";
                if ($post->user) {
                    $context .= "Author: {$post->user->name}\n";
                }
                $context .= "\n";
            }
        }

        $httpClient = new GuzzleClient();
        $baseUri = BaseUri::from('https://api.openai.com/v1');
        $apiKey = ApiKey::from(config('services.openai.api_key'));
        $headers = Headers::withAuthorization($apiKey);
        $queryParams = QueryParams::create();
        $streamHandler = function ($response) {
            return $response;
        };
        $transporter = new HttpTransporter($httpClient, $baseUri, $headers, $queryParams, $streamHandler);
        $client = new Client($transporter);
        
        // Enhanced system prompt with post context
        $systemPrompt = "You are a helpful AI assistant for a business software platform. You can help with business process automation, data analysis, software recommendations, and technical support. Be concise but informative in your responses.\n\n";
        $systemPrompt .= "Here are some relevant posts from our knowledge base that might help you provide better answers. When referencing these posts, mention their titles and categories:\n\n{$context}";

        // Prepare the conversation history
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $message]
        ];

        // Generate response
        $response = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 500,
        ]);

        return response()->json([
            'message' => $response->choices[0]->message->content,
            'relevant_posts' => $relevantPosts->map(function($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->body,
                    'author' => $post->user ? [
                        'id' => $post->user->id,
                        'name' => $post->user->name
                    ] : null,
                    'created_at' => $post->created_at
                ];
            })
        ]);
    } catch (\Exception $e) {
        \Log::error('AI Chat Error: ' . $e->getMessage());
        return response()->json([
            'error' => 'Failed to generate AI response',
            'message' => $e->getMessage()
        ], 500);
    }
});

// ============================================================================
// AUTHENTICATION & SESSION ROUTES
// ============================================================================
Route::get('/auth/verify-session', function () {
    if (!auth()->check()) {
        return response()->json(['valid' => false], 401);
    }
    
    return response()->json([
        'valid' => true,
        'user' => auth()->user()->name,
        'session_id' => session()->getId()
    ]);
})->middleware('auth:sanctum');

// ============================================================================
// USER PERMISSIONS ROUTES
// ============================================================================
Route::middleware(['auth:sanctum', 'web'])->group(function () {
    // User permissions management routes
    Route::get('/user-permissions/users', [UserPermissionController::class, 'index']);
    Route::get('/user-permissions/users/search', [UserPermissionController::class, 'search']);
    Route::get('/user-permissions/roles', [UserPermissionController::class, 'roles']);
    Route::put('/user-permissions/users/{user}/roles', [UserPermissionController::class, 'updateRoles']);
    
    // Role permissions management routes
    Route::get('/permissions', [UserPermissionController::class, 'permissions']);
    Route::get('/user-permissions/roles/search', [UserPermissionController::class, 'searchRoles']);
    Route::put('/user-permissions/roles/{role}/permissions', [UserPermissionController::class, 'updateRolePermissions']);
});

// ============================================================================
// MODEL CONFIGURATION ROUTES
// ============================================================================
Route::get('/model-config/{module}/{table}', [ModelConfigController::class, 'getModelConfig'])
    ->middleware(['web', 'auth:sanctum']);

// ============================================================================
// ENTITY SCHEMA ROUTES
// ============================================================================
Route::get('/entities/{entity}/schema', function ($entity) {
    try {
        // Map entity names to model classes
        $modelMap = [
            'finance_vendors' => \App\Models\FinanceVendor::class,
            'vendors' => \App\Models\Vendor::class,
            'users' => \App\Models\User::class,
            'posts' => \App\Models\Post::class,
            'projects' => \App\Models\Project::class,
            // Add more entities as needed
        ];

        if (!isset($modelMap[$entity])) {
            return response()->json([
                'error' => 'Entity not found',
                'entity' => $entity,
                'available_entities' => array_keys($modelMap)
            ], 404);
        }

        $modelClass = $modelMap[$entity];
        $model = new $modelClass;

        // Get table information
        $tableName = $model->getTable();
        $fillable = $model->getFillable();
        $casts = $model->getCasts();
        $hidden = $model->getHidden();

        // Get validation rules if available
        $validationRules = [];
        if (method_exists($model, 'validationRules')) {
            $validationRules = $model::validationRules();
        }

        // Build field definitions
        $fields = [];
        
        // Add primary key
        $fields['id'] = [
            'type' => 'integer',
            'primary' => true,
            'auto_increment' => true,
            'fillable' => false
        ];

        // Add fillable fields
        foreach ($fillable as $field) {
            $fields[$field] = [
                'type' => getFieldType($field, $casts[$field] ?? null),
                'fillable' => true,
                'required' => in_array($field, array_keys($validationRules)) && 
                             strpos($validationRules[$field], 'required') !== false,
                'hidden' => in_array($field, $hidden),
                'cast' => $casts[$field] ?? null,
                'validation' => $validationRules[$field] ?? null
            ];
        }

        // Add timestamps
        $fields['created_at'] = [
            'type' => 'timestamp',
            'fillable' => false,
            'required' => false,
            'hidden' => false
        ];
        $fields['updated_at'] = [
            'type' => 'timestamp',
            'fillable' => false,
            'required' => false,
            'hidden' => false
        ];

        return response()->json([
            'table' => $tableName,
            'model' => $modelClass,
            'fields' => $fields,
            'validation_rules' => $validationRules,
            'fillable' => $fillable,
            'hidden' => $hidden,
            'casts' => $casts
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to generate schema',
            'entity' => $entity,
            'message' => $e->getMessage()
        ], 500);
    }
})->name('api.entities.schema');

// Helper function to determine field type
if (!function_exists('getFieldType')) {
    function getFieldType($fieldName, $cast = null) {
        if ($cast) {
            switch ($cast) {
                case 'integer':
                case 'int':
                    return 'integer';
                case 'decimal:2':
                case 'float':
                case 'double':
                    return 'decimal';
                case 'boolean':
                case 'bool':
                    return 'boolean';
                case 'array':
                case 'json':
                    return 'array';
                case 'date':
                    return 'date';
                case 'datetime':
                case 'timestamp':
                    return 'timestamp';
                default:
                    return 'string';
            }
        }
        
        // Fallback based on field name
        if (strpos($fieldName, '_id') !== false) {
            return 'integer';
        }
        if (in_array($fieldName, ['created_at', 'updated_at', 'deleted_at'])) {
            return 'timestamp';
        }
        if (in_array($fieldName, ['is_active', 'is_approved', 'is_locked', 'is_1099_vendor'])) {
            return 'boolean';
        }
        
        return 'string';
    }
}

// ============================================================================
// RESOURCE ROUTES
// ============================================================================
Route::get('/resource/{table}', [ResourceController::class, 'index']);

// ============================================================================
// EXAMPLE & TEST ROUTES
// ============================================================================
Route::post('/example', [SomethingController::class, 'justAnExample'])
    ->middleware(VerifyFastApiKey::class);

Route::get('test_user', function (){
    return Post::take(5)->get();
});

Route::get('whatever', function (){
    return Post::take(51)->get();
});

Route::get('u', function (Request $request){
    $response = $request;
    return $response;
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| DYNAMIC API ROUTES
|--------------------------------------------------------------------------
| These routes handle ALL entity operations dynamically and should be placed LAST
| to avoid conflicts with specific routes above
|
| ALL entity updates now use: PUT /api/{table}/{id}
| ALL entity field updates use: PATCH /api/{table}/{id}/field
| ALL entity deletions use: DELETE /api/{table}/{id}
| ALL entity fetching use: GET /api/{table}
|
| Examples:
| - PUT /api/posts/123 -> Updates post with ID 123
| - PUT /api/projects/456 -> Updates project with ID 456
| - PUT /api/users/789 -> Updates user with ID 789
| - PUT /api/bots/101 -> Updates bot with ID 101
| - PUT /api/providers/202 -> Updates provider with ID 202
| - etc. for ALL entities
*/

Route::middleware(['auth:sanctum', 'web'])->group(function () {
    // Generic dynamic routes for ALL entities
    Route::get('{table}', [DynamicController::class, 'api'])
        ->name('api.table');
        
    // CREATE - Create new record
    Route::post('{table}', [DynamicController::class, 'store'])
        ->name('api.table.store');
        
    // READ - Get single record
    Route::get('{table}/{id}', [DynamicController::class, 'show'])
        ->name('api.table.show');
        
    // UPDATE - Update entire record
    Route::put('{table}/{id}', [DynamicController::class, 'update'])
        ->name('api.table.update');
        
    // UPDATE - Update single field
    Route::patch('{table}/{id}/field', [DynamicController::class, 'updateField'])
        ->name('api.table.field.update');
        
    // DELETE - Delete record
    Route::delete('{table}/{id}', [DynamicController::class, 'destroy'])
        ->name('api.table.destroy');
});


/*
Complete CRUD Operations Summary
After adding the missing operations, you'll have complete CRUD functionality for all entities:
✅ CREATE (Create new record)
Route: POST /api/{table}
Example: POST /api/posts → Creates new post
Method: DynamicController@store
✅ READ (List all records)
Route: GET /api/{table}
Example: GET /api/posts → Lists all posts
Method: DynamicController@api
✅ READ (Get single record)
Route: GET /api/{table}/{id}
Example: GET /api/posts/123 → Gets post with ID 123
Method: DynamicController@show
✅ UPDATE (Update entire record)
Route: PUT /api/{table}/{id}
Example: PUT /api/posts/123 → Updates post with ID 123
Method: DynamicController@update
✅ UPDATE (Update single field)
Route: PATCH /api/{table}/{id}/field
Example: PATCH /api/posts/123/field → Updates single field
Method: DynamicController@updateField
✅ DELETE (Delete record)
Route: DELETE /api/{table}/{id}
Example: DELETE /api/posts/123 → Deletes post with ID 123
Method: DynamicController@destroy
Entities That Will Work
This will work for ALL entities in your system:
✅ posts, projects, futures, users, bots, providers
✅ i1_orders, i1_forwarders, i1_business_customers
✅ business_services, categories, comments, tags
✅ tasks, teams, roles, permissions
✅ articles, article_groups, features
✅ And any other model in your app/Models/ directory
Would you like me to implement the missing store method and add the missing routes to complete the CRUD functionality?
*/


/*
|--------------------------------------------------------------------------
| COMMENTED OUT ROUTES (For Reference)
|--------------------------------------------------------------------------
| These routes are commented out but kept for reference

Route::get('/examplez', [SomethingController::class, 'justAnExample']);

Route::group([
    'prefix' => 'v1',
    'middleware' => 'with_fast_api_key'
], function () {
    Route::post('/examplez', [SomethingController::class, 'justAnExample']);
    // ...
});

Route::get('futures', function (){
    return Future::take(50)->get();
});

// Specific dynamic routes (replaced by generic dynamic routes above)
Route::prefix('futures')->group(function () {
    Route::get('/', [DynamicController::class, 'api'])
        ->name('futures.index');
    Route::put('/{id}', [DynamicController::class, 'update'])
        ->name('futures.update');
});

Route::prefix('projects')->group(function () {
    Route::get('/', [DynamicController::class, 'api'])
        ->name('projects.index');
    Route::put('/{id}', [DynamicController::class, 'update'])
        ->name('projects.update');
});

Route::prefix('business_services')->group(function () {
    Route::get('/', [DynamicController::class, 'api'])
        ->name('business_services.index');
    Route::put('/{id}', [DynamicController::class, 'update'])
        ->name('business_services.update');
});

Route::prefix('posts')->group(function () {
    Route::get('/', [DynamicController::class, 'api'])
        ->name('posts.index');
    Route::put('/{id}', [DynamicController::class, 'update'])
        ->name('posts.update');
});

// Specific update routes (replaced by generic dynamic routes above)
Route::put('/api/futures/{id}', function(Request $request, $id) {
    return app(DynamicController::class)->update($request, $id);
})->name('futures.update')->middleware('auth:sanctum');

Route::put('/api/projects/{id}', function(Request $request, $id) {
    return app(DynamicController::class)->update($request, $id);
})->name('projects.update')->middleware('auth:sanctum');

Route::put('/api/business_services/{id}', function(Request $request, $id) {
    return app(DynamicController::class)->update($request, $id);
})->name('business_services.update')->middleware('auth:sanctum');

Route::put('/api/posts/{id}', function(Request $request, $id) {
    return app(DynamicController::class)->update($request, $id);
})->name('posts.update')->middleware('auth:sanctum');

Route::put('/api/i1_orders/{id}', function(Request $request, $id) {
    return app(DynamicController::class)->update($request, $id);
})->name('i1_orders.update')->middleware('auth:sanctum');

// Legacy data routes (migrated to dynamic routes above)
Route::get('projects', function (){
    return Project::take(25)->get();
});

Route::get('business_services', function (){
    return BusinessService::take(50)->get();
});

Route::get('posts', function (){
    return Post::take(50)->get();
});

Route::get('users', function (Request $request) {
    if (!$request->user()) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    return UserResource::collection(User::take(25)->get());
})->middleware('auth:sanctum');

Route::get('i1_orders', function (){
    return I1Order::take(50)->get();
});
*/



// Test route to check authentication
Route::get('/test-auth', function () {
    if (auth()->check()) {
        return response()->json([
            'authenticated' => true,
            'user' => auth()->user()->email,
            'message' => 'Authentication working'
        ]);
    }
    return response()->json([
        'authenticated' => false,
        'message' => 'Not authenticated'
    ], 401);
})->middleware('auth:sanctum');

Route::prefix('finance')->middleware('auth:sanctum')->group(function () {
    // Fixed Assets
    Route::get('/assets', [FixedAssetController::class, 'index']);
    Route::post('/assets', [FixedAssetController::class, 'store']);
    Route::post('/assets/depreciate', [FixedAssetController::class, 'depreciate']);

    // Budgets
    Route::get('/budgets/{period}', [BudgetController::class, 'show']);
    Route::post('/budgets', [BudgetController::class, 'upsert']);

    // Audit
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
    Route::post('/audit-logs', [AuditLogController::class, 'store']);
});

Route::post('/ai/call', [\App\Http\Controllers\AIServiceController::class, 'callAI'])->name('ai.call');

// ============================================================================
// INVENTORY API ROUTES
// ============================================================================
Route::prefix('inventory')->group(function () {
    Route::get('/purchase-orders', function () {
        return DB::table('inventory_purchase_orders')->get();
    });
    Route::get('/suppliers', function () {
        return DB::table('inventory_suppliers')->get();
    });
    Route::get('/items', function () {
        return DB::table('inventory_items')->get();
    });
    Route::get('/warehouses', function () {
        return DB::table('inventory_warehouses')->get();
    });
    Route::get('/stock-movements', function () {
        return DB::table('inventory_stock_movements')->get();
    });
});


