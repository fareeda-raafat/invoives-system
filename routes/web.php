<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomersReportController;
use App\Http\Controllers\InvoiceAttachmentsController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\InvoicesDetailsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SectionsController;
use App\Models\invoice_attachments;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoicsReportController;
use App\Exports\InvoicesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;


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
    return view('welcome');
});

Route::resource('index', HomeController::class);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('invoices', InvoicesController::class);
Route::resource('sections', SectionsController::class);
Route::resource('products', ProductsController::class);
Route::get('/section/{id}', [InvoicesController::class, 'getproducts']);
Route::get('/invoices_details/{id}', [InvoicesDetailsController::class, 'get_details']);
Route::get('/View_file/{invoice_nubmber}/{file_name}', [InvoicesDetailsController::class, 'view_file']);
Route::get('/download/{invoice_number}/{file_name}', [InvoicesDetailsController::class, 'download_file']);
Route::post('/delete_file', [InvoicesDetailsController::class, 'destroy'])->name('delete_file');
Route::get('invoices_report', [InvoicsReportController::class, 'index']);
Route::post('Search_invoices', [InvoicsReportController::class, 'Search_invoices']);

Route::get('customer_reports', [CustomersReportController::class], 'index');
Route::post('Search_customers', [CustomersReportController::class], 'Search_customers');

Route::get('export_invoices', [InvoicesController::class, 'export']);

Route::group(['middleware' => ['auth']], function () {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
});

require __DIR__ . '/auth.php';
Route::get('/{page}',  [AdminController::class, 'index']);
