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
    Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    
    // Tasks
    Route::resource('tasks', TaskController::class);
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
    
    // Clients
    Route::resource('clients', ClientController::class);
    Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
    Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
    
    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::put('invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');

    // Time Entries
    Route::resource('time-entries', TimeEntryController::class);
    Route::delete('time-entries/{timeEntry}', [TimeEntryController::class, 'destroy'])->name('time-entries.destroy');
    Route::post('time-entries', [TimeEntryController::class, 'store'])->name('time-entries.store');
    Route::put('time-entries/{timeEntry}', [TimeEntryController::class, 'update'])->name('time-entries.update');
    Route::get('time-entries', [TimeEntryController::class, 'index'])->name('time-entries.index');

    // Dashboard
    Route::get('/', function () {
        return view('prasso-pm::dashboard');
    })->name('dashboard');
});
