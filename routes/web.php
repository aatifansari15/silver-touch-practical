<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/get-categories-dropdown/{id?}', 'HomeController@getCategoriesDropDown')->name('get-categories-dropdown');

Route::resource('/categories', 'CategoryController')->except(['show']);
Route::post('/categories/destroy-all', 'CategoryController@destroyAll')->name('categories.destroy-all');

Route::resource('/products', 'ProductController')->except(['show']);
Route::post('/products/destroy-all', 'ProductController@destroyAll')->name('products.destroy-all');