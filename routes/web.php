<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\AdminController;

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

Route::middleware(['auth:sanctum','verified'])->get('/dashboard', function () {
        if(Auth::check()){
            if(Auth::user()->role =='admin'){
                return redirect()->route('admin#profile');
            }else if(Auth::user()->role =='user'){
                return redirect()->route('user#index');
            }
        }
    })->name('dashboard');


Route::group(['prefix'=>'admin','namespace'=>'Admin'],function(){
     Route::get('profile','AdminController@profile')->name('admin#profile');
     Route::post('update/{id}','AdminController@update')->name('admin#update');
     Route::get('changePassword','AdminController@changePasswordPage')->name('admin#changePasswordPage');
     Route::post('changePassword/{id}','AdminController@changePassword')->name('admin#changePassword');

     Route::get('category','CategoryController@category')->name('admin#category');//list
     Route::get('addCategory','CategoryController@addCategory')->name('admin#addCategory');
     Route::post('createCategory','CategoryController@createCategory')->name('admin#createCategory');
     Route::get('deleteCategory/{id}','CategoryController@deleteCategory')->name('admin#deleteCategory');
     Route::get('editCategory/{id}','CategoryController@editCategory')->name('admin#editCategory');
     Route::post('updateCategory','CategoryController@updateCategory')->name('admin#updateCategory');
     Route::get('category/search','CategoryController@searchCategory')->name('admin#searchCategory');

     Route::get('pizza','PizzaController@pizza')->name('admin#pizza');//listpage
     Route::get('createPizza','PizzaController@createPizza')->name('admin#createPizza');
     Route::post('insertPizza','PizzaController@insertPizza')->name('admin#insertPizza');
     Route::get('deletePizza/{id}','PizzaController@deletePizza')->name('admin#deletePizza');
     Route::get('infoPizza/{id}','PizzaController@infoPizza')->name('admin#infoPizza');
     Route::get('editPizza/{id}','PizzaController@editPizza')->name('admin#editPizza');
     Route::post('updatePizza/{id}','PizzaController@updatePizza')->name('admin#updatePizza');
     Route::post('searchPizaa','PizzaController@searchPizza')->name('admin#searchPizza');
     Route::get('categoryItem/{id}','PizzaController@categoryItem')->name('admin#categoryItem');

     Route::get('userList','UserController@userList')->name('admin#userList');
     Route::get('adminList','UserController@adminList')->name('admin#adminList');
     Route::get('searchUser','UserController@searchUser')->name('admin#searchUser');
     Route::get('searchAdmin','UserController@searchAdmin')->name('admin#searchAdmin');
     Route::get('userDelete/{id}','UserController@userDelete')->name('admin#userDelete');
     Route::get('adminDelete/{id}','UserController@adminDelete')->name('admin#adminDelete');



});

Route::group(['prefix'=>'user'],function(){
     Route::get('/','UserController@index')->name('user#index');
});
