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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', function() {
    return view('home');
})->name('home')->middleware('auth');

//Route::get('/configurations/roles', function(){
//    return 'Ol�, user!';
//});

//$this->resource('/configurations/roles', 'RolesController');

//Route::resource('/configurations/roles', 'Configs\RolesController', 
//['except' => [
//  'create', 'edit'
//]]); 
//Route::resource('/configurations/users', 'Configs\UsersController', 
//['except' => [
//  'create', 'edit'
//]]); 

Route::group(['prefix' => 'configs', 'namespace' => 'Configs', 'middleware' => 'auth'], function() {

  Route::resource('users', 'UsersController');
  Route::resource('roles','RolesController');

});

