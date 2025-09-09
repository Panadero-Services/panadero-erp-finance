<?php

namespace App\Services;

class AIModelRouter
{
    /**
     * Mathematical keywords that indicate a math-focused query
     */
    private const MATH_KEYWORDS = [
        'calculate', 'computation', 'formula', 'equation', 'percentage', 'ratio', 'rate',
        'depreciation', 'roi', 'return on investment', 'profit margin', 'interest',
        'tax calculation', 'payroll calculation', 'overtime', 'salary', 'wage',
        'inventory turnover', 'reorder point', 'safety stock', 'working capital',
        'cash flow', 'debt ratio', 'equity ratio', 'financial ratio', 'variance',
        'budget analysis', 'cost analysis', 'utilization rate', 'efficiency ratio',
        'revenue per', 'cost per', 'price calculation', 'discount calculation',
        'total cost', 'unit cost', 'break-even', 'net present value', 'irr',
        'amortization', 'compound interest', 'simple interest', 'annual percentage'
    ];

    /**
     * Semantic keywords that indicate a process/workflow query
     */
    private const SEMANTIC_KEYWORDS = [
        'how to', 'process', 'workflow', 'procedure', 'steps', 'guide',
        'onboarding', 'recruitment', 'hiring', 'approval', 'policy',
        'compliance', 'audit', 'risk assessment', 'training', 'development',
        'employee relations', 'performance review', 'goal setting',
        'vendor management', 'supplier', 'procurement', 'purchase order',
        'goods receipt', 'quality control', 'warehouse management',
        'order fulfillment', 'inventory management', 'stock control',
        'journal entry creation', 'invoice processing', 'payment processing',
        'financial reporting', 'budget planning', 'period close',
        'what is', 'explain', 'describe', 'overview', 'features',
        'modules', 'integration', 'implementation', 'best practices'
    ];

    /**
     * Determine which AI model to use based on the query content
     */
    public function determineModel(string $query, string $module = null): array
    {
        $queryLower = strtolower($query);
        $mathScore = $this->calculateMathScore($queryLower);
        $semanticScore = $this->calculateSemanticScore($queryLower);
        
        // Check for explicit mathematical operations
        $hasNumbers = preg_match('/\d+/', $query);
        $hasCurrency = preg_match('/\$|€|£|¥/', $query);
        $hasMathSymbols = preg_match('/[+\-*\/=<>%]/', $query);
        
        $mathIndicators = ($hasNumbers ? 1 : 0) + ($hasCurrency ? 1 : 0) + ($hasMathSymbols ? 1 : 0);
        
        // Decision logic - Use CodeLlama for both for now since it's working reliably
        if ($mathScore > $semanticScore && ($mathScore > 2 || $mathIndicators >= 2)) {
            return [
                'model' => 'self-math',
                'ollama_model' => 'codellama:7b', // Better for mathematical reasoning
                'confidence' => min(($mathScore / 5) * 100, 100),
                'reason' => 'Mathematical calculation or financial computation detected'
            ];
        } else {
            return [
                'model' => 'sonar-semantic',
                'ollama_model' => 'codellama:7b', // Using CodeLlama for semantic too until timeout issue is resolved
                'confidence' => min(($semanticScore / 5) * 100, 100),
                'reason' => 'Workflow, process, or semantic understanding required'
            ];
        }
    }

    /**
     * Calculate math-related score for the query
     */
    private function calculateMathScore(string $query): int
    {
        $score = 0;
        foreach (self::MATH_KEYWORDS as $keyword) {
            if (strpos($query, $keyword) !== false) {
                $score++;
            }
        }
        return $score;
    }

    /**
     * Calculate semantic-related score for the query
     */
    private function calculateSemanticScore(string $query): int
    {
        $score = 0;
        foreach (self::SEMANTIC_KEYWORDS as $keyword) {
            if (strpos($query, $keyword) !== false) {
                $score++;
            }
        }
        return $score;
    }

    /**
     * Get enhanced system prompt based on model type and module
     */
    public function getSystemPrompt(string $modelType, string $module): string
    {
        $prompts = [
            'sonar-semantic' => [
                'finance' => "You are an ERP Finance expert. Explain financial workflows like journal entries, invoice processing, and budget procedures step-by-step.",
                'hr' => "You are an ERP HR expert. Explain HR processes like recruitment, onboarding, and performance management step-by-step.",
                'inventory' => "You are an ERP Inventory expert. Explain inventory workflows like procurement, purchase orders, and warehouse management step-by-step.",
                'compliance' => "You are an ERP Compliance expert. Explain compliance processes like risk assessment, audits, and policy management step-by-step."
            ],
            'self-math' => [
                'finance' => "You are an ERP Finance calculator. Solve financial calculations like depreciation, ROI, ratios, and budget analysis. Show formulas and step-by-step math.",
                'hr' => "You are an ERP HR calculator. Solve HR calculations like payroll, overtime, utilization rates, and workforce metrics. Show formulas and step-by-step math.",
                'inventory' => "You are an ERP Inventory calculator. Solve inventory calculations like reorder points, safety stock, and turnover ratios. Show formulas and step-by-step math.",
                'compliance' => "You are an ERP Compliance calculator. Solve compliance calculations like risk scoring and audit sampling. Show formulas and step-by-step math."
            ]
        ];

        return $prompts[$modelType][$module] ?? $prompts['sonar-semantic']['finance'];
    }
}
