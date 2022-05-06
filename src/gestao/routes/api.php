<?php

use App\Http\Controllers\MedicosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('/medico')->controller(MedicosController::class)->group(function () {
    Route::post('/new', 'create');
    Route::get('/show/{type}/{search}', 'show');
    Route::put('/update/{type}/{id}', 'update');
    Route::delete('/delete/{id}', 'destroy');
    
});

