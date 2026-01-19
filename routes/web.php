<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\AppointmentmentStatusController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardStatController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ChequeIssuancesController;
use App\Http\Controllers\Admin\QuotasController;
use App\Http\Controllers\Admin\SongController;
use App\Http\Controllers\Admin\TeachingController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\ChildDedicationController;
use App\Http\Controllers\MemberController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('login');
});

// SECRET ADMIN REGISTRATION ROUTE - Not visible on login page
Route::get('/admin/secret-register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.secret.register.form');
Route::post('/admin/secret-register', [AdminAuthController::class, 'register'])->name('admin.secret.register');

// Route::get('/admin/dashboard', function () {

//     return view('dashboard');

// });

// Route::get('csrf', function () {
//     return csrf_token();
// });

Route::middleware('auth')->group(function (){

    // ==================== POS System Routes ====================
    
    // POS Management (Admin Only)
    Route::prefix('admin/pos')->middleware('admin')->group(function () {
        // Categories
        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
            Route::get('/list/all', [CategoryController::class, 'listAll'])->name('categories.list-all');
            Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
            Route::get('/{category}', [CategoryController::class, 'show'])->name('categories.show');
            Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
            Route::post('/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
            Route::delete('/{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('categories.force-delete');
        });
        
        // Products
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('products.index');
            Route::post('/', [ProductController::class, 'store'])->name('products.store');
            Route::get('/search', [ProductController::class, 'search'])->name('products.search');
            Route::get('/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');
            Route::get('/{product}', [ProductController::class, 'show'])->name('products.show');
            Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
            Route::post('/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
            Route::delete('/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('products.force-delete');
        });
        
        // Customers
        Route::prefix('customers')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('customers.index');
            Route::post('/', [CustomerController::class, 'store'])->name('customers.store');
            Route::get('/{customer}', [CustomerController::class, 'show'])->name('customers.show');
            Route::put('/{customer}', [CustomerController::class, 'update'])->name('customers.update');
            Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
            Route::post('/{id}/restore', [CustomerController::class, 'restore'])->name('customers.restore');
            Route::get('/{customer}/orders', [CustomerController::class, 'orders'])->name('customers.orders');
        });
        
        // Orders & Reports
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('orders.index');
            Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
            Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
            Route::post('/{id}/restore', [OrderController::class, 'restoreOrder'])->name('orders.restore');
            Route::get('/reports/daily-sales', [OrderController::class, 'dailySales'])->name('orders.daily-sales');
            Route::get('/reports/by-payment-method', [OrderController::class, 'salesByPaymentMethod'])->name('orders.by-payment-method');
        });
    });
    
    // POS Orders (for cashiers)
    Route::post('/api/orders', [OrderController::class, 'store'])->name('orders.create');
    
    // ==================== Original Routes ====================

    Route::get('/api/stats/apointments', [DashboardStatController::class, 'appointments']);
    Route::get('/api/stats/users', [DashboardStatController::class, 'users']);

    Route::get('/api/users', [UserController::class, 'index']);
    Route::post('/api/users', [UserController::class, 'store']);
    // Route::get('/api/users/search', [UserController::class, 'search']);
    Route::patch('/api/users/{user}/change-role', [UserController::class, 'changeRole']);
    Route::put('/api/users/{user}', [UserController::class, 'update']);
    Route::delete('/api/users/{user}', [UserController::class, 'destroy']);
    Route::delete('/api/users', [UserController::class, 'bulkDelete']);

    Route::get('/api/clients', [ClientController::class, 'index']);

    // start of song
    Route::post('/songs/create', [SongController::class, 'store']);
    Route::get('/songs', [SongController::class, 'index']);
    Route::get('/songs/{song}/edit', [SongController::class, 'edit']);
    Route::put('/songs/{song}/edit', [SongController::class, 'update']);
    Route::delete('/songs/{song}', [SongController::class, 'destroy']);
    // end of song

    // start of teachings
    Route::post('/teachings/create', [TeachingController::class, 'store']);
    Route::get('/teachings', [TeachingController::class, 'index']);
    Route::get('/teachings/{teaching}/edit', [TeachingController::class, 'edit']);
    Route::put('/teachings/{teaching}/edit', [TeachingController::class, 'update']);
    Route::delete('/teachings/{teaching}', [TeachingController::class, 'destroy']);
    // end of teachings


    // Events routes
    Route::post('/events/create', [EventController::class, 'store']);
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{id}/edit', [EventController::class, 'edit']);
    Route::put('/events/{id}/edit', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
    // start of child dedication
    Route::post('/child-dedications/create', [ChildDedicationController::class, 'store']);
    Route::get('/child-dedications', [ChildDedicationController::class, 'index']);
    Route::get('/child-dedication/{dedication}/edit', [ChildDedicationController::class, 'edit']);
    Route::put('/child-dedications/{dedication}/edit', [ChildDedicationController::class, 'update']);
    Route::delete('/child-dedications/{dedication}', [ChildDedicationController::class, 'destroy']);
    // end of child dedication

    // start of memebers module
    Route::post('/members/create', [MemberController::class, 'store']);
    Route::get('/members', [MemberController::class, 'index']);
    Route::get('/members/{member}/edit', [MemberController::class, 'edit']);
    Route::put('/members/{member}/edit', [MemberController::class, 'update']);
    Route::delete('/members/{member}', [MemberController::class, 'destroy']);
    // end of memebers module

    Route::get('/api/appointment-status', [AppointmentmentStatusController::class, 'getStatusWithCount']);
    Route::get('/api/appointments', [AppointmentController::class, 'index']);
    Route::post('/api/appointments/create', [AppointmentController::class, 'store']);
    Route::get('/api/appointments/{appointment}/edit', [AppointmentController::class, 'edit']);
    Route::put('/api/appointments/{appointment}/edit', [AppointmentController::class, 'update']);
    Route::delete('/api/appointments/{appointment}', [AppointmentController::class, 'destroy']);

    Route::get('/api/settings', [SettingController::class, 'index']);
    Route::post('/api/settings', [SettingController::class, 'update']);

});

Route::get('{view}', ApplicationController::class)->where('view', '(.*)')->middleware('auth');
