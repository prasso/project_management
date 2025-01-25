<?php

use Illuminate\Support\Facades\Route;
use Prasso\ProjectManagement\Http\Controllers\ProjectController;
use Prasso\ProjectManagement\Http\Controllers\TaskController;
use Prasso\ProjectManagement\Http\Controllers\ClientController;
use Prasso\ProjectManagement\Http\Controllers\InvoiceController;
use Prasso\ProjectManagement\Http\Controllers\TimeEntryController;

Route::middleware(['web', 'auth'])->prefix('prasso-pm')->name('prasso-pm.')->group(function () {
    // Projects
    Route::resource('projects', ProjectController::class);
    
    // Tasks
    Route::resource('tasks', TaskController::class);
    
    // Clients
    Route::resource('clients', ClientController::class);
    
    // Invoices
    Route::resource('invoices', InvoiceController::class);

    // Time Entries
    Route::resource('time-entries', TimeEntryController::class);

    // Dashboard
    Route::get('/', function () {
        return view('prasso-pm::dashboard');
    })->name('dashboard');
});
