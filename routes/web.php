<?php

use App\Http\Controllers\Api\VideoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get("/", function () {
    // Redirect to admin
    // return "Hello World";

    return redirect('/admin');
});

// Route::get("/admin/wish-texts", function () {
//     // Redirect to edit with Id 1

//     return redirect('/admin/wish-texts/1/edit');
// });


Route::post('/generate-video', [VideoController::class, 'generateVideo'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
