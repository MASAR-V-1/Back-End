<?php

use Illuminate\Support\Facades\Route;
use Modules\ITAdmin\app\Http\Controllers\ITAdminController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('itadmins', ITAdminController::class)->names('itadmin');
});
