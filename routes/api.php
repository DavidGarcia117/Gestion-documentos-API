<?php

use App\Http\Controllers\Api\DocumentController;
use Illuminate\Support\Facades\Route;

Route::post('/documents/filing', [DocumentController::class, 'filing']);