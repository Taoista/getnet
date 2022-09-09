<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GetnetController;
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


Route::get("/",[GetnetController::class, "index"]);
Route::get("/generate_token",[GetnetController::class, "generate_token"]);


// Route::get("/return_page/{login}/{tranKey}/{nonce}/{seed}",[GetnetController::class, "return_page"]);
Route::get("/return_page",[GetnetController::class, "return_page"]);
