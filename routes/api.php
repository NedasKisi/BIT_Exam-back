<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenusController;
use App\Http\Controllers\DishesController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\RestaurantsController;
use App\Http\Controllers\PassportAuthController;

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

Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);
Route::post('logout', [PassportAuthController::class, 'logout'])->middleware('auth:api');
Route::get('authcheck', [PassportAuthController::class, 'index'])->middleware('auth:api');

Route::get('restaurants', [RestaurantsController::class, 'index']);
Route::get('restaurants/{id}', [RestaurantsController::class, 'show']);
Route::post('restaurants', [RestaurantsController::class, 'store'])->middleware('auth:api');
Route::put('restaurants/{id}', [RestaurantsController::class, 'update'])->middleware('auth:api');
Route::delete('restaurants/{id}', [RestaurantsController::class, 'destroy'])->middleware('auth:api');


Route::get('menus', [MenusController::class, 'index']);
Route::get('menus/{id}', [MenusController::class, 'show']);
Route::get('menus/restaurant/{id}', [MenusController::class, 'byRestaurant']);
Route::get('menus/sort/name', [MenusController::class, 'sortByName']);
Route::get('menus/search/{keyword}', [MenusController::class, 'search']);
Route::post('menus', [MenusController::class, 'store'])->middleware('auth:api');
Route::put('menus/{id}', [MenusController::class, 'store'])->middleware('auth:api');
Route::post('menus/{id}', [MenusController::class, 'update'])->middleware('auth:api');
Route::delete('menus/{id}', [MenusController::class, 'destroy'])->middleware('auth:api');

Route::get('dishes', [DishesController::class, 'index']);
Route::get('dishes/{id}', [DishesController::class, 'show']);
Route::get('dishes/menu/{id}', [DishesController::class, 'byMenu']);
Route::get('dishes/sort/name', [DishesController::class, 'sortByName']);
Route::get('dishes/search/{keyword}', [DishesController::class, 'search']);
Route::post('dishes', [DishesController::class, 'store'])->middleware('auth:api');
Route::post('dishes/{id}', [DishesController::class, 'update'])->middleware('auth:api');
Route::delete('dishes/{id}', [DishesController::class, 'destroy'])->middleware('auth:api');

Route::get('orders', [OrdersController::class, 'index'])->middleware('auth:api');
Route::get('orders/all', [OrdersController::class, 'all'])->middleware('auth:api');
Route::get('orders/{id}', [OrdersController::class, 'status'])->middleware('auth:api');
Route::post('orders', [OrdersController::class, 'store'])->middleware('auth:api');
Route::delete('orders/{id}', [OrdersController::class, 'destroy'])->middleware('auth:api');