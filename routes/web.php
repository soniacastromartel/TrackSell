<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
    return redirect('login');
});

Auth::routes();

Route::middleware(['check-permission'])->group(function () {

    Route::get('/admin/employees', 'EmployeeController@index')->name('employees.index');
    Route::get('/admin/employees/validation', 'EmployeeController@indexPending')->name('employees.indexPending');
    Route::get('/admin/employees/history/{id}', 'EmployeeController@history')->name('employees.history.index');
    Route::get('/admin/employees/edit/{id}', 'EmployeeController@edit')->name('employees.edit');
    Route::put('/admin/employees/update/{id}', 'EmployeeController@update')->name('employees.update');
    Route::post('/admin/employees/confirmUsername', 'EmployeeController@confirmUsername')->name('employees.confirmUsername');
    Route::post('/admin/employees/changeUsername', 'EmployeeController@changeUsername')->name('employees.changeUsername');
    Route::post('/admin/employees/denyAccessApp', 'EmployeeController@denyAccessApp')->name('employees.denyAccessApp');
    Route::post('/admin/employees/resetAccessApp', 'EmployeeController@resetAccessApp')->name('employees.resetAccessApp');
    Route::post('/admin/employees/syncA3', 'EmployeeController@syncA3')->name('employees.syncA3');

    Route::get('/admin/centres', 'CentreController@index')->name('centres.index');
    Route::get('/admin/centres/create', 'CentreController@create')->name('centres.create');
    Route::get('/admin/centres/edit/{id}', 'CentreController@edit')->name('centres.edit');
    Route::post('/admin/centres/store', 'CentreController@store')->name('centres.store');
    Route::put('/admin/centres/update/{id}', 'CentreController@update')->name('centres.update');
    Route::get('/admin/centres/destroy/{id}', 'CentreController@destroy')->name('centres.destroy');

    Route::get('/admin/services', 'ServiceController@index')->name('services.index');
    Route::any('/admin/incentives', 'ServiceController@incentives')->name('services.incentives');
    Route::get('/admin/services/create', 'ServiceController@create')->name('services.create');
    Route::post('/admin/services', 'ServiceController@store')->name('services.store');
    Route::get('/admin/services/edit/{id}', 'ServiceController@edit')->name('services.edit');
    Route::put('/admin/services/update/{id}', 'ServiceController@update')->name('services.update');
    Route::get('/admin/services/destroy/{id}', 'ServiceController@destroy')->name('services.destroy');
    Route::post('/admin/services/destroyIncentive', 'ServiceController@destroyIncentive')->name('services.destroyIncentive');
    Route::get('/admin/services/exportServices', 'ServiceController@exportServicesIncentivesActives')->name('services.exportServicesIncentivesActives');

    Route::any('/tracking/index', 'TrackingController@index')->name('tracking.index');
    Route::get('/tracking/create', 'TrackingController@create')->name('tracking.create');
    Route::post('/tracking/store', 'TrackingController@store')->name('tracking.store');
    Route::get('/tracking/edit/{state}/{id}', 'TrackingController@edit')->name('tracking.edit');
    Route::put('/tracking/update/{state}/{id}', 'TrackingController@update')->name('tracking.update');
    Route::get('/tracking/updateState/{state}/{id}/{date}/{back?}','TrackingController@updateState')->name('tracking.updateState');
    Route::post('/tracking/updatePaidState','TrackingController@updatePaidState')->name('tracking.updatePaidState');
    Route::get('/tracking/refreshServices/{centre_id}','TrackingController@refreshServices')->name('tracking.refreshServices');
    Route::get('/tracking/refreshDiscount/{service_id}/{centre_id}','TrackingController@refreshDiscount')->name('tracking.refreshDiscount');
    Route::get('/tracking/exportForm', 'TrackingController@exportForm')->name('tracking.exportForm');
    Route::post('/tracking/export', 'TrackingController@export')->name('tracking.export');
    Route::get('/tracking/deleteForm', 'TrackingController@deleteForm')->name('tracking.deleteForm');
    Route::post('/tracking/searchDelete', 'TrackingController@searchDelete')->name('tracking.searchDelete');
    Route::get('/tracking/destroy/{id}', 'TrackingController@destroy')->name('tracking.destroy');
    Route::any('/tracking/indexvalidation', 'TrackingController@indexFinalValidation')->name('tracking.index_validation_final');
    Route::get('/tracking/checkDate/{date}/{status}', 'TrackingController@checkDate')->name('tracking.checkDate');
    Route::post('/tracking/exportFinalValidation', 'TrackingController@exportFinalValidation')->name('tracking.exportFinalValidation');
    Route::post('/tracking/calculateValidationRRHH', 'TrackingController@calculateValidationRRHH')->name('tracking.calculateValidationRRHH');
    Route::post('/tracking/validateTrackings', 'TrackingController@validateTrackings')->name('tracking.validateTrackings');
    Route::get('/tracking/requestChange', 'TrackingController@requestChange')->name('tracking.requestChange');
    Route::post('/tracking/saveRequest', 'TrackingController@saveRequest')->name('tracking.saveRequest');
    Route::post('/tracking/getRequestChanges', 'TrackingController@getRequestChanges')->name('tracking.getRequestChanges');
    Route::post('/tracking/confirmRequest', 'TrackingController@confirmRequest')->name('tracking.confirmRequest');

    Route::get('/calculateIncentive', 'TargetController@index')->name('calculateIncentive');
    Route::post('/target/import', 'TargetController@import')->name('target.import');
    Route::post('/target/importSales', 'TargetController@importSales')->name('target.importSales');
    Route::post('/target/importIncentive', 'TargetController@importIncentive')->name('target.importIncentive');
    Route::post('/target/calculateTargets', 'TargetController@calculateTargets')->name('target.calculateTargets');
    Route::post('/target/tracingTargets', 'TargetController@tracingTargets')->name('target.tracingTargets');

    Route::get('/calculateRanking', 'RankingController@index')->name('calculateRanking');
    Route::post('/ranking/calculateRankings', 'RankingController@calculateRankings')->name('ranking.calculateRankings');

    Route::get('/centerLeague', 'LeagueController@index')->name('centerLeague');
    Route::post('/league/generateLeague', 'LeagueController@generateLeague')->name('league.generateLeague');
    Route::post('/league/details', 'LeagueController@detailsCentreLeague')->name('league.detailsCentreLeague');

    Route::any('/notifications/index', 'NotificationController@index')->name('notifications.index');

});

Route::middleware(['check-admin-permission'])->group(function () {
    Route::get('/admin/roles', 'RoleController@index')->name('roles.index');
    Route::get('/admin/roles/create', 'RoleController@create')->name('roles.create');
    Route::get('/admin/roles/edit/{id}', 'RoleController@edit')->name('roles.edit');
    Route::put('/admin/roles/update/{id}', 'RoleController@update')->name('roles.update');
    Route::post('/admin/roles', 'RoleController@store')->name('roles.store');
});

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/profile', 'HomeController@profile')->name('profile');
Route::get('/admin/profile', 'HomeController@viewProfile')->name('admin.profile');
Route::put('/editProfile/{id}', 'HomeController@editProfile')->name('editProfile');
Route::get('/getSales', 'HomeController@getSales')->name('home.getSales');
Route::post('/getTargets', 'HomeController@getTargets')->name('home.getTargets');

Route::get('/generateVersion', 'VersionAppController@generateVersion');