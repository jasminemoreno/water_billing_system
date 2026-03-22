<?php

use App\Http\Controllers\CustomerHistoryController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\CustomerBillController;
use App\Http\Controllers\CustomerHomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerForgotPasswordController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\CustomerSettingController;
use App\Http\Controllers\ForgotPasswordController;

// ---------------------------
// Public routes
// ---------------------------
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/send', [ForgotPasswordController::class, 'sendOtp']);

// Verify OTP
Route::post('/verify', [ForgotPasswordController::class, 'verifyOtp']);

// Reset password
Route::post('/reset', [ForgotPasswordController::class, 'resetPassword']);

// ---------------------------
// Protected routes via Sanctum
// ---------------------------
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/admin/user', [AuthController::class, 'user']); // authenticated admin
    Route::put('/admin/user', [AuthController::class, 'updateEmail']);
    Route::put('/admin/user/password', [AuthController::class, 'updatePassword']);

    // Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'index']);

    // Customers
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers/{id}', [CustomerController::class, 'update']);
    Route::get('/customers/search', [CustomerController::class, 'search']); // optional live search

    // Bills
    Route::get('/bills', [BillController::class, 'index']);
    Route::get('/bills/customers/search', [BillController::class, 'searchCustomers']);
    Route::post('/bills', [BillController::class, 'store']);
    Route::delete('/bills/{bill}', [BillController::class, 'destroy']);
    Route::put('/bills/{bill}', [BillController::class, 'update']);

    // Payments
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/payments/search-customer', [PaymentController::class, 'searchCustomer']);
    Route::get('/payments/{id}', [PaymentController::class, 'show']);
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::put('/payments/{id}', [PaymentController::class, 'update']);
    Route::post('/payments/{id}/verify', [PaymentController::class, 'verify']);
    Route::post('/payments/{id}/reject', [PaymentController::class, 'reject']);
    Route::delete('/payments/{id}', [PaymentController::class, 'destroy']);
    Route::patch('/payments/{id}/status', [PaymentController::class, 'updateStatus']);
    Route::post('/payments/customer', [PaymentController::class, 'storeCustomerPayment']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('reports/payment-history/{year}/{month}', [ReportController::class, 'paymentHistoryByMonth']);
    Route::get('reports/bill-history/{year}/{month}', [ReportController::class, 'billHistoryByMonth']);
    

    // User Management
    Route::get('/admin/user-management', [UserManagementController::class, 'index']);
    Route::delete('/admin/user-management/{id}', [UserManagementController::class, 'destroy']);

    // Admin Profile
    Route::get('/admin/profile', [AdminProfileController::class, 'show']);
    Route::post('/admin/profile/update', [AdminProfileController::class, 'updateProfile']);
    Route::put('/admin/profile/phone', [AdminProfileController::class, 'updatePhone']); 
});

    Route::post('customer/login', [CustomerAuthController::class, 'login'])->name('customer.login');
    Route::post('customer/forgot/send', [CustomerForgotPasswordController::class, 'sendOtp']);
    Route::post('customer/forgot/verify', [CustomerForgotPasswordController::class, 'verifyOtp']);
    Route::post('customer/forgot/reset', [CustomerForgotPasswordController::class, 'resetPassword']);

    Route::middleware('auth:customer-api')->prefix('customer')->group(function () {
    Route::get('/dashboard', [CustomerHomeController::class, 'dashboard']);
    Route::get('/dashboardData', [CustomerHomeController::class, 'dashboardData']);
    Route::get('/mybills', [CustomerBillController::class, 'mybills']);

    Route::post('/logout', [CustomerAuthController::class, 'logout']);

    Route::get('/paybills', [CustomerPaymentController::class, 'index']);
    Route::post('/paybills/{id}',[CustomerPaymentController::class, 'store']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    // Mark as read
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // Delete notification
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
   

    Route::get('/history', [CustomerHistoryController::class, 'history']);

    Route::get('/profile', [CustomerProfileController::class, 'show']);
    Route::post('/profile/update', [CustomerProfileController::class, 'update']);

    Route::get('/settings', [CustomerSettingController::class, 'index']);
    Route::post('/settings/update-account', [CustomerSettingController::class, 'updateAccount']);
    Route::post('/settings/change-password',[CustomerSettingController::class, 'changePassword']);


});