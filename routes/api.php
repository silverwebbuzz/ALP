<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\GameController;

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

// Route::group(['middleware' => ['json.response']], function () {
//     Route::group(['namespace' => 'Api'], function () {
//         Route::get('student/credit-points','StudentController@GetStudentCreditPoints')->name('student.credit-points');
//         Route::get('student/credit-points','StudentController@GetStudentCreditPoints')->name('student.credit-points');
//         Route::get('student/{student_id}','StudentController@StudentDetail')->name('student.detail')->middleware('AuthenticateUser');

//         Route::get('student/clear-data/{student_id}','StudentController@ClearStudentData')->name('student.clear')->middleware('AuthenticateUser');

//         Route::get('planet/list','GameController@PlanetList')->name('planet.list');
        
//         Route::get('game/configuration','GameController@GameConfiguration')->name('game/configuration');
//         Route::post('game/store/{id}','GameController@StoreGameDetail')->name('game/store');
//         Route::post('game/update/current_position/{student_id}','GameController@UpdateGamePosition')->name('game.update.current_position');
//     });
// });

Route::group([
    'middleware' => 'api',
    'prefix' => 'game'
], function ($router) {
    Route::group(['namespace' => 'Api'], function () {
        Route::post('/login', 'GameController@login')->name('user-login');
        // Route::post('/logout', [GameController::class, 'logout']);
        // Route::get('/user-profile', [GameController::class, 'userProfile']);  
    });  
});