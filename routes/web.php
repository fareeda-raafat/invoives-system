<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InvoiceAttachmentsController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\InvoicesDetailsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SectionsController;
use App\Models\invoice_attachments;

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
Route::post('/delete_file',[InvoicesDetailsController::class , 'destroy'])->name('delete_file');

require __DIR__ . '/auth.php';
Route::get('/{page}',  [AdminController::class, 'index']);
