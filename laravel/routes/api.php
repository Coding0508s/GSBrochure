<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BrochureController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\StockHistoryController;
use App\Http\Controllers\Api\AdminController;

Route::get('/health', [BrochureController::class, 'health']);

Route::get('brochures', [BrochureController::class, 'index']);
Route::post('brochures', [BrochureController::class, 'store']);
Route::put('brochures/{id}', [BrochureController::class, 'update']);
Route::delete('brochures/{id}', [BrochureController::class, 'destroy']);
Route::put('brochures/{id}/stock', [BrochureController::class, 'updateStock']);

Route::get('contacts', [ContactController::class, 'index']);
Route::post('contacts', [ContactController::class, 'store']);
Route::put('contacts/{id}', [ContactController::class, 'update']);
Route::delete('contacts/{id}', [ContactController::class, 'destroy']);

Route::get('requests', [RequestController::class, 'index']);
Route::post('requests', [RequestController::class, 'store']);
Route::put('requests/{id}', [RequestController::class, 'update']);
Route::delete('requests/{id}', [RequestController::class, 'destroy']);
Route::post('requests/{id}/invoices', [RequestController::class, 'addInvoices']);
Route::delete('requests/{id}/invoices', [RequestController::class, 'deleteInvoices']);

Route::get('stock-history', [StockHistoryController::class, 'index']);
Route::post('stock-history', [StockHistoryController::class, 'store']);

Route::post('admin/login', [AdminController::class, 'login']);
Route::get('admin/users', [AdminController::class, 'users']);
Route::post('admin/users', [AdminController::class, 'createUser']);
Route::put('admin/users/{id}/password', [AdminController::class, 'changePassword']);
Route::delete('admin/users/{id}', [AdminController::class, 'deleteUser']);
