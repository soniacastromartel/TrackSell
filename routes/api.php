<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/* MIS DATOS */
Route::get('/employee_info/{id}', 'API\EmployeeController@info');
/* MIS INCENTIVOS */
Route::post('/incentives', 'API\TrackingController@incentives');
/* MIS SERVICIOS */
Route::get('/services/{id}/{order}', 'API\ServiceController@servicesByCentre');
/* NUEVO SEGUIMIENTO */
Route::post('/tracking/create', 'API\TrackingController@store');
/* RANKING EMPLEADO */
Route::post('/employee_ranking', 'API\EmployeeController@getRanking');
Route::get('/promotions', 'API\PromotionsController@getPromotions');

/* LISTADOS DE CENTROS Y CATEGORÍAS */
Route::get('/employee_categories', 'API\EmployeeController@getJobCategories');
Route::get('/getCenters', 'API\CentreController@getCenters');
Route::get('/getCentersByService/{id}', 'API\CentreController@getCentersByService');

/* CONTROL/RESETEO DE ACCESOS */
Route::get('/controlUser', 'API\EmployeeController@controlUser');

Route::post('/register', 'API\AuthController@register');

Route::post('/unlockRequest', 'API\AuthController@unlockRequest');

Route::post('/login', 'API\AuthController@login');
Route::post('/initCheck', 'API\AuthController@initCheck');
Route::post('/recoveryPass', 'API\AuthController@recoveryPass');
Route::post('/changingPass', 'API\AuthController@changingPass');

Route::post('/save_errors', 'API\ErrorsController@saveErrors');
Route::get('/checkingVersion', 'API\VersionAppController@checkingVersion');
Route::get('/getLastVersion', 'API\VersionAppController@getLastVersion');
Route::post('/getClasification', 'API\LeagueCentresController@getClasification');

Route::post('/getLastChanges', 'API\VersionAppController@getLastChanges');

Route::post('/notUpdate', 'API\VersionAppController@notUpdate');
Route::post('/resetCountUpdate', 'API\VersionAppController@resetCountUpdate');

Route::get('/getDataFaq', 'API\FAQController@getDataFaq');

Route::get('/tracking/search', 'API\TrackingController@getTrackingInfo');

Route::get('/discounts/{service_id}/{centre_id}', 'API\ServiceController@getAvailablesDiscounts');

Route::post('/logs', 'API\LogsAppController@savelogs');

Route::get('/getServiceCategories', 'API\ServiceController@getServiceCategories');
Route::get('/getCategoriesWithServices', 'API\ServiceController@getServiceCategoriesWithServices');
Route::get('/getServicesList', 'API\ServiceController@getServices');
Route::get('/getCentersByService/{id}', 'API\CentreController@getCentersByService');


