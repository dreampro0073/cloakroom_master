<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CloakRoomController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SittingController;


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


Route::get('/', [UserController::class,'login'])->name("login");
Route::post('/login', [UserController::class,'postLogin']);


Route::get('/logout',function(){
	Auth::logout();
	return Redirect::to('/');
});

Route::group(['middleware'=>'auth'],function(){
	Route::group(['prefix'=>"admin"], function(){
		Route::get('/dashboard',[AdminController::class,'dashboard']);
		Route::get('/reset-password',[UserController::class,'resetPassword']);
		Route::post('/reset-password',[UserController::class,'updatePassword']);

		Route::group(['prefix'=>"sitting"], function(){
			Route::get('/',[SittingController::class,'sitting']);
			Route::get('/print/{id?}', [SittingController::class,'printPost']);
			Route::get('/print-report', [SittingController::class,'printReports']);

		});
		

		Route::group(['prefix'=>"shift"], function(){
			Route::get('/current',[ShiftController::class,'index']);
			Route::get('/print/{type}',[ShiftController::class,'print']);
		});
		Route::group(['prefix'=>"cloak-rooms"], function(){
			Route::get('/',[CloakRoomController::class,'index']);
			Route::get('/all',[CloakRoomController::class,'allRooms']);
			Route::get('/print/{id?}', [CloakRoomController::class,'printPost']);
			Route::get('/print-luggage/{id?}', [CloakRoomController::class,'printLuggage']);
			
		});	
	
		Route::group(['prefix'=>"users"], function(){
			Route::get('/',[UserController::class,'users']);
		});
	});
});

Route::group(['prefix'=>"api"], function(){
	
	Route::group(['prefix'=>"shift"], function(){
		Route::post('/init',[ShiftController::class,'init']);
		Route::post('/prev-init',[ShiftController::class,'prevInit']);

	});

	Route::group(['prefix'=>"sitting"], function(){
		Route::post('/init',[SittingController::class,'initEntries']);
		Route::post('/edit-init',[SittingController::class,'editEntry']);
		Route::post('/store',[SittingController::class,'store']);
		Route::post('/cal-check',[SittingController::class,'calCheck']);
		Route::get('/delete/{id}',[SittingController::class,'delete']);
		
	});


	Route::group(['prefix'=>"cloak-rooms"], function(){
		Route::post('/init/{type}',[CloakRoomController::class,'initRoom']);
		Route::post('/edit-init',[CloakRoomController::class,'editRoom']);
		Route::post('/store',[CloakRoomController::class,'store']);
		Route::post('/cal-check',[CloakRoomController::class,'calCheck']);
		Route::post('/checkout-init',[CloakRoomController::class,'checkoutInit']);
		Route::post('/checkout-store',[CloakRoomController::class,'checkoutStore']);
		Route::get('/delete/{id}',[CloakRoomController::class,'delete']);

	});
	Route::group(['prefix'=>"users"], function(){
		Route::post('/init',[UserController::class,'initUsers']);
		Route::post('/edit-init',[UserController::class,'editUser']);
		Route::post('/store',[UserController::class,'storeUser']);
	});
});
