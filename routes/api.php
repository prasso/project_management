<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Prasso\ProjectManagement\Http\Controllers\TimeEntryController;

Route::middleware('auth:sanctum')
    ->prefix('prasso-pm/api')
    ->group(function () {
  //  Route::get('/uninvoiced-time-entries/{clientId}', [TimeEntryController::class, 'getUninvoicedTimeEntries']);
});
