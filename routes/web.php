<?php

use Illuminate\Support\Facades\Route;

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

// With no admin prefix (the default from Exment 7.2.1), "/" is the home of Exment.
// The welcome page is only for a site that keeps a prefix such as "admin".
if (trim((string) config('admin.route.prefix'), '/') !== '') {
    Route::get('/', function () {
        return view('welcome');
    });
}
