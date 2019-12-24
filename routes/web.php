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



Auth::routes(['register' => false]);


Route::get('/', function() {
    return view('home');
})->name('home')->middleware('auth');

Route::get('/home', function() {
    return view('home');
})->name('home')->middleware('auth');


Route::group(['prefix' => 'configs', 'namespace' => 'Configs', 'middleware' => 'auth'], function() {

  Route::resource('users', 'UsersController');
  Route::resource('roles','RolesController');
  Route::resource('pbx','PbxController');
  Route::resource('prefixes','PrefixesController');
  Route::resource('routes','RoutesController');
  Route::resource('rates','RatesController');
  Route::resource('trunks','TrunksController');

  Route::resource('calls','CallsController');

});

Route::group(['prefix' => 'dashboards', 'namespace' => 'Dashboards', 'middleware' => 'auth'], function() {

  Route::resource('resumes', 'ResumesController');
  
});

Route::group(['prefix' => 'maintenance', 'namespace' => 'Maintenance', 'middleware' => 'auth'], function() {

  Route::resource('status', 'StatusController');
  
});

Route::group(['prefix' => 'services', 'namespace' => 'Services'], function() {

  Route::get('collector','ServicesController@collector');
  Route::get('import','ServicesController@import');
  Route::get('billing','ServicesController@billing');

});

