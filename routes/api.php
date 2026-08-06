<?php

use App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Api\App;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoanProductController;
use App\Http\Controllers\Api\Officer;
use App\Http\Controllers\Api\SettingController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/settings', [SettingController::class, 'show']);
Route::get('/loan-products', [LoanProductController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', fn (Request $request) => new UserResource($request->user()));

    Route::middleware('role:borrower')->prefix('app')->group(function () {
        Route::get('/loans', [App\LoanController::class, 'index']);
        Route::post('/loans', [App\LoanApplicationController::class, 'store']);
        Route::get('/loans/{loan}', [App\LoanApplicationController::class, 'show']);
        Route::post('/loans/{loan}/repay', [App\RepaymentController::class, 'initiate']);
        Route::post('/kyc', [App\KycController::class, 'store']);
    });

    Route::middleware('role:officer,owner')->prefix('officer')->group(function () {
        Route::apiResource('borrowers', Officer\BorrowerController::class)->except('destroy');
        Route::get('/loans', [Officer\LoanController::class, 'index']);
        Route::post('/loans/{loan}/approve', [Officer\LoanController::class, 'approve']);
        Route::post('/loans/{loan}/disburse', [Officer\LoanController::class, 'disburse']);
        Route::post('/loans/{loan}/collections', [Officer\CollectionController::class, 'store']);
    });

    Route::middleware('role:owner')->prefix('admin')->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index']);
        Route::apiResource('officers', Admin\OfficerController::class);
        Route::post('/officers/{officer}/deactivate', [Admin\OfficerController::class, 'deactivate']);
        Route::post('/officers/{officer}/reassign', [Admin\OfficerController::class, 'reassignBook']);
        Route::get('/borrowers', [Admin\BorrowerController::class, 'index']);
        Route::get('/loans', [Admin\LoanController::class, 'index']);
        Route::get('/collections', [Admin\CollectionController::class, 'index']);
        Route::get('/reports/officer-performance', [Admin\ReportController::class, 'officerPerformance']);
        Route::put('/settings', [Admin\SettingController::class, 'update']);
    });
});
