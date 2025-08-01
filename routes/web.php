<?php

use Adldap\Laravel\Facades\Adldap;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\A3CenterSaraController;
use App\Http\Controllers\ChartController;
use App\Service;
use Illuminate\Support\Arr;
use App\Http\Controllers\IncentivesController;

Route::get('/', function () {
    return redirect('login');
});

Auth::routes();
Route::middleware(['check-permission'])->group(function () {
    //! Employees
    Route::get('/admin/employees', 'EmployeeController@index')->name('employees.index');
    Route::get('/admin/employees/history/{id}', 'EmployeeController@history')->name('employees.history.index');
    Route::get('/admin/employees/edit/{id}', 'EmployeeController@edit')->name('employees.edit');
    Route::put('/admin/employees/update/{id}', 'EmployeeController@update')->name('employees.update');
    Route::post('/admin/employees/confirmUsername', 'EmployeeController@confirmUsername')->name('employees.confirmUsername');
    Route::post('/admin/employees/changeUsername', 'EmployeeController@changeUsername')->name('employees.changeUsername');
    Route::post('/admin/employees/denyAccessApp', 'EmployeeController@denyAccessApp')->name('employees.denyAccessApp');
    Route::post('/admin/employees/resetAccessApp', 'EmployeeController@resetAccessApp')->name('employees.resetAccessApp');
    Route::post('/admin/employees/syncA3', 'EmployeeController@syncA3')->name('employees.syncA3');
    Route::post('/admin/employees/resetPassword', 'EmployeeController@resetPassword')->name('employees.resetPassword');
    Route::put('/admin/employees/{id}', 'EmployeeController@cancel')->name('employees.cancel');

    //!Centress
    Route::get('/admin/centres', 'CentreController@index')->name('centres.index');
    Route::get('/admin/centres/create', 'CentreController@create')->name('centres.create');
    Route::get('/admin/centres/edit/{id}', 'CentreController@edit')->name('centres.edit');
    Route::post('/admin/centres/store', 'CentreController@store')->name('centres.store');
    Route::put('/admin/centres/update/{id}', 'CentreController@update')->name('centres.update');
    Route::get('/admin/centres/destroy/{id}', 'CentreController@destroy')->name('centres.destroy');
    //! Services
    Route::get('/admin/services', 'ServiceController@index')->name('services.index');
    Route::get('/admin/services/create', 'ServiceController@create')->name('services.create');
    Route::post('/admin/services', 'ServiceController@store')->name('services.store');
    Route::get('/admin/services/edit/{id}', 'ServiceController@edit')->name('services.edit');
    Route::put('/admin/services/update/{id}', 'ServiceController@update')->name('services.update');
    Route::get('/admin/services/destroy/{id}', 'ServiceController@destroy')->name('services.destroy');
    Route::get('/admin/services/exportServices', 'ServiceController@exportServicesIncentivesActives')->name('services.exportServicesIncentivesActives');
    Route::get('/calculateServices', 'ServiceController@calculateServices')->name('services.calculateServices');
    Route::post('/getSalesServices', 'ServiceController@getSalesServices')->name('services.getSalesServices');
    Route::get('/calculateServices', 'ServiceController@showAllServicesAndByCentre')->name('calculateServices');
    //?Exportar dinámica de servicios
    Route::get('/export/services', 'ServiceController@exportDinamicServices')->name('export.all-services');

    //!Incentives
    Route::any('/incentives', [IncentivesController::class, 'index'])->name('incentives.index');
    Route::post('/incentives/edit', 'IncentivesController@updateIncentive')->name('incentives.edit');
    Route::post('/incentives/create', 'IncentivesController@createIncentive')->name('incentives.create');
    Route::post('/incentives/destroyIncentive', 'IncentivesController@destroyIncentive')->name('incentives.destroy');
    Route::post('/incentives/import', 'IncentivesController@import')->name('incentives.import');
    Route::get('/service/centres', [IncentivesController::class, 'showCenters'])->name('service.centres');


    //! Tracking 
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
    Route::post('/tracking/unvalidateTrackings', 'TrackingController@unvalidateTrackings')->name('tracking.unvalidateTrackings');
    Route::post('/tracking/validateTrackings', 'TrackingController@validateTrackings')->name('tracking.validateTrackings');
    Route::post('/tracking/unvalidateTrackings', 'TrackingController@unvalidateTrackings')->name('tracking.unvalidateTrackings');
    Route::get('/tracking/requestChange', 'TrackingController@requestChange')->name('tracking.requestChange');
    Route::post('/tracking/saveRequest', 'TrackingController@saveRequest')->name('tracking.saveRequest');
    Route::post('/tracking/getRequestChanges', 'TrackingController@getRequestChanges')->name('tracking.getRequestChanges');
    Route::post('/tracking/confirmRequest', 'TrackingController@confirmRequest')->name('tracking.confirmRequest');
    // Route::post('/tracking/discountStatistics', 'TrackingController@discountStats')->name('tracking.discountStats');
    //!Target
    Route::get('/calculateIncentive', 'TargetController@index')->name('calculateIncentive');
    Route::post('/target/import', 'TargetController@import')->name('target.import');
    Route::post('/target/importSales', 'TargetController@importSales')->name('target.importSales');
    Route::post('/target/importIncentive', 'TargetController@importIncentive')->name('target.importIncentive');
    Route::post('/target/calculateIncentives', 'TargetController@calculateIncentives')->name('target.calculateIncentives');
    Route::post('/target/incentivesReportDownload', 'TargetController@incentivesReportDownload')->name('target.incentivesReportDownload');
    Route::post('/target/tracingTargets', 'TargetController@tracingTargets')->name('target.tracingTargets');
    Route::post('/target/targetReportDownload', 'TargetController@targetsReportDownload')->name('target.targetsReportDownload');
    Route::post('/target/targetReportView', 'TargetController@targetsReportView')->name('target.targetsReportView');
    Route::post('/target/incentivesReportView', 'TargetController@incentivesReportView')->name('target.incentivesReportView');
    Route::post('/target/incentivesSummaryView', 'TargetController@incentivesSummaryView')->name('target.incentivesSummaryView');
    Route::post('/target/incentivesSummaryView', 'TargetController@incentivesSummaryView')->name('target.incentivesSummaryView');
    Route::post('/target/importPrivateSales', 'TargetController@importPrivateSales')->name('target.importPrivateSales');
    //!Ranking
    Route::get('/calculateRanking', 'RankingController@index')->name('calculateRanking');
    Route::post('/ranking/calculateRankings', 'RankingController@calculateRankings')->name('ranking.calculateRankings');
    //!League
    Route::get('/league/export', 'LeagueController@exportLeague')->name('league.exportLeague');
    Route::get('/centerLeague', 'LeagueController@index')->name('centerLeague');
    Route::post('/league/generateLeague', 'LeagueController@generateLeague')->name('league.generateLeague');
    Route::post('/league/details', 'LeagueController@detailsCentreLeague')->name('league.detailsCentreLeague');
    //!Notifications
    Route::any('/notifications/index', 'NotificationController@index')->name('notifications.index');
});

//!AMDMIN Roles
Route::middleware(['check-admin-permission'])->group(function () {
    Route::get('/admin/roles', 'RoleController@index')->name('roles.index');
    Route::get('/admin/roles/create', 'RoleController@create')->name('roles.create');
    Route::get('/admin/roles/edit/{id}', 'RoleController@edit')->name('roles.edit');
    Route::put('/admin/roles/update/{id}', 'RoleController@update')->name('roles.update');
    Route::get('/admin/roles/destroy/{id}', 'RoleController@destroy')->name('roles.destroy');
    Route::post('/admin/roles', 'RoleController@store')->name('roles.store');
    Route::get('/admin/employees/validation', 'EmployeeController@indexPending')->name('employees.indexPending');
});

//! User
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/profile', 'HomeController@profile')->name('profile');
Route::get('/admin/profile', 'HomeController@viewProfile')->name('admin.profile');
Route::put('/editProfile/{id}', 'HomeController@editProfile')->name('editProfile');
Route::get('/getSales', 'HomeController@getSales')->name('home.getSales');
Route::post('/getTargets', 'HomeController@getTargets')->name('home.getTargets');
Route::get('/generateVersion', 'VersionAppController@generateVersion');

//!A3API
Route::prefix('a3api')->group(function () {
    Route::get('/a3', 'A3Controller@index')->name('a3');
    Route::get('/centres/{companyCode}', 'A3Controller@getCentres')->name('centres');
    Route::get('/employees/{companyCode}/{workplaceCode}/{pagenumber}', 'A3Controller@getEmployees')->name('employees');
    Route::get('/pages/{companyCode}/{workplaceCode}', 'A3Controller@getPages')->name('pages');
    Route::get('/allemployees', 'A3Controller@getAllEmployees')->name('allemployees');
    Route::get('/jobTitle/{companyCode}/{employeeCode}', 'A3Controller@getJobTitle')->name('jobTitle');
    Route::get('/contactData/{companyCode}/{employeeCode}', 'A3Controller@getContactData')->name('contactData');
    Route::get('/hiringData/{companyCode}/{employeeCode}', 'A3Controller@getHiringData')->name('hiringData');
    Route::get('/workplace/{companyCode}/{workplaceCode}', 'A3Controller@getCentreName')->name('workplace');
    Route::get('/refreshtoken', 'A3Controller@refreshToken')->name('refreshtoken');
    Route::get('/token', 'A3Controller@getAuthCode')->name('code');
});


// Route::fallback(function () {
//     return response()->json(['error' => 'No encontrado'], 404);
//   });
