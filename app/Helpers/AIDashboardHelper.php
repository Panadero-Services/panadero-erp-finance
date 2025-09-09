<?php

namespace App\Helpers;

class AIDashboardHelper
{
    public static function checkOllamaStatus(): bool
    {
        try {
            $response = \Http::timeout(5)->get('http://localhost:11434/api/tags');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getResponseTimeChartData($since)
    {
        $data = \App\Models\AISuccessLog::where('created_at', '>=', $since)
            ->selectRaw('DATE_FORMAT(created_at, "%H:00") as hour, AVG(response_time_ms) as avg_time')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        
        $labels = [];
        $times = [];
        
        for ($i = 0; $i < 24; $i++) {
            $hour = $since->copy()->addHours($i)->format('H:00');
            $labels[] = $hour;
            $times[] = $data->where('hour', $hour)->first()->avg_time ?? 0;
        }
        
        return ['labels' => $labels, 'data' => $times];
    }

    public static function getErrorTypesChartData($since)
    {
        $errors = \App\Models\AIErrorLog::where('created_at', '>=', $since)
            ->selectRaw('error_type, COUNT(*) as count')
            ->groupBy('error_type')
            ->get();
        
        return [
            'labels' => $errors->pluck('error_type')->toArray(),
            'data' => $errors->pluck('count')->toArray()
        ];
    }

    public static function getErrorDetails($since)
    {
        $errors = \App\Models\AIErrorLog::where('created_at', '>=', $since)
            ->selectRaw('error_type, COUNT(*) as count, is_timeout')
            ->groupBy('error_type', 'is_timeout')
            ->get();
        
        $errorMap = [
            'Ollama API Error' => 'API communication issues',
            'Ollama Request Failed' => 'Request processing failures',
            'Timeout Error' => 'Request timeouts'
        ];
        
        return $errors->map(function ($error) use ($errorMap) {
            $class = $error->is_timeout ? 'timeout-count' : 'api-error-count';
            return [
                'type' => $error->error_type,
                'description' => $errorMap[$error->error_type] ?? 'Unknown error type',
                'count' => $error->count,
                'class' => $class
            ];
        })->toArray();
    }
}

