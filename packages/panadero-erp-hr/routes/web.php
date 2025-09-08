<?php

use Illuminate\Support\Facades\Route;
use Panadero\Erp\Hr\Http\Controllers\HrController;

/*
|--------------------------------------------------------------------------
| HR Module Routes
|--------------------------------------------------------------------------
|
| Here are the routes for the HR module. These routes are loaded by the
| RouteServiceProvider within a group which contains the "web" middleware
| group. Now create something great!
|
*/

Route::middleware(['web', 'auth'])->prefix('erp/hr')->name('erp.hr.')->group(function () {
    // Main HR dashboard
    Route::get('/', [HrController::class, 'index'])->name('index');
    
    // Employee management
    Route::get('/employees', [HrController::class, 'employees'])->name('employees');
    Route::post('/employees', [HrController::class, 'storeEmployee'])->name('employees.store');
    Route::put('/employees/{employee}', [HrController::class, 'updateEmployee'])->name('employees.update');
    Route::delete('/employees/{employee}', [HrController::class, 'deleteEmployee'])->name('employees.delete');
    
    // Vacancy management
    Route::get('/vacancies', [HrController::class, 'vacancies'])->name('vacancies');
    Route::post('/vacancies', [HrController::class, 'storeVacancy'])->name('vacancies.store');
    Route::put('/vacancies/{vacancy}', [HrController::class, 'updateVacancy'])->name('vacancies.update');
    Route::delete('/vacancies/{vacancy}', [HrController::class, 'deleteVacancy'])->name('vacancies.delete');
    Route::post('/vacancies/{vacancy}/publish', [HrController::class, 'publishVacancy'])->name('vacancies.publish');
    Route::post('/vacancies/{vacancy}/close', [HrController::class, 'closeVacancy'])->name('vacancies.close');
    
    // Recruitment
    Route::get('/recruitment', [HrController::class, 'recruitment'])->name('recruitment');
    Route::get('/applications', [HrController::class, 'applications'])->name('applications');
    Route::post('/applications', [HrController::class, 'storeApplication'])->name('applications.store');
    Route::put('/applications/{application}', [HrController::class, 'updateApplication'])->name('applications.update');
    Route::post('/applications/{application}/update-status', [HrController::class, 'updateApplicationStatus'])->name('applications.update-status');
    
    // Department management
    Route::get('/departments', [HrController::class, 'departments'])->name('departments');
    Route::post('/departments', [HrController::class, 'storeDepartment'])->name('departments.store');
    Route::put('/departments/{department}', [HrController::class, 'updateDepartment'])->name('departments.update');
    Route::delete('/departments/{department}', [HrController::class, 'deleteDepartment'])->name('departments.delete');
    
    // Reports
    Route::get('/reports', [HrController::class, 'reports'])->name('reports');
    Route::post('/reports/generate', [HrController::class, 'generateReport'])->name('reports.generate');
    Route::get('/reports/{report}/export', [HrController::class, 'exportReport'])->name('reports.export');
    
    // Agent Portal (AI)
    Route::get('/agent-portal', [HrController::class, 'agentPortal'])->name('agent-portal');
    Route::post('/agent-portal/execute', [HrController::class, 'executeAIPrompt'])->name('agent-portal.execute');
    Route::post('/agent-portal/analyze-resume', [HrController::class, 'analyzeResume'])->name('agent-portal.analyze-resume');
    Route::post('/agent-portal/match-candidates', [HrController::class, 'matchCandidates'])->name('agent-portal.match-candidates');
});

// Public routes (for job applications, etc.)
Route::middleware(['web'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/careers', [HrController::class, 'careers'])->name('careers');
    Route::get('/careers/{vacancy}', [HrController::class, 'showVacancy'])->name('careers.show');
    Route::post('/careers/{vacancy}/apply', [HrController::class, 'applyForJob'])->name('careers.apply');
});
