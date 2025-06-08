<?php

use App\Controllers\WelcomeController;
use Core\Routing\Route;
use App\Controllers\api\AuthController;

Route::post('/admin/auth', [AuthController::class, 'login']);

/**
 * Make something great with this app
 * keep simple yeah.
 */

Route::get('/', WelcomeController::class);
