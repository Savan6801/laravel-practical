<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ImportController;
use Illuminate\Support\Facades\DB;
// Customer Routes
Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [App\Http\Controllers\Customer\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Customer\AuthController::class, 'login']);
    Route::get('/register', [App\Http\Controllers\Customer\AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Customer\AuthController::class, 'register']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('customer.dashboard');
    })->name('dashboard');

    Route::post('/logout', [App\Http\Controllers\Customer\AuthController::class, 'logout'])->name('logout');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware(['guest:admin'])->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login'])->name('login.submit');
    });

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

        // Product Routes
        Route::resource('products', ProductController::class);

        // // Import
        Route::post('products/import', [ImportController::class, 'store'])->name('products.import');
        Route::get('/products', function () {
            return view('products.index', [
                'products' => DB::table('products')->latest()->get()
            ]);
        });
    });
});
