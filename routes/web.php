<?php

use App\Http\Controllers\DBBackupController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;



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

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::redirect('/', '/dashboard');

Route::group(['middleware' => ['auth', 'role']], function () {

	Route::resource('/user', UserController::class);
	Route::resource('/sla', SlaController::class);
	Route::resource('/pop', PopController::class);
	Route::resource('/port', PortController::class);
	Route::resource('/snport', SNPortController::class);
	Route::resource('/subcom', SubcomController::class);
	Route::get('/generateSN', 'SNPortController@generateSN');
	Route::delete('/snport/group/{id}', 'SNPortController@deleteGroup');
	Route::delete('/port/group/{id}', 'PortController@deleteGroup');
	Route::resource('/township', TownshipController::class);
	Route::resource('/city', CityController::class);
	Route::resource('/equiptment', EquiptmentController::class);
	Route::resource('/menu', MenuController::class);
	Route::resource('/package', PackageController::class);
	Route::resource('/project', ProjectController::class);
	Route::resource('/status', StatusController::class);
	Route::resource('/role', RoleController::class);
	Route::resource('/voip', VoipController::class);
	Route::resource('/activity-log', ActivityLogController::class);
});
Route::group(['middleware' => 'auth'], function () {
	Route::get('/getpackage/{id}', 'PackageController@getBundle');
	Route::get('/incidentOverdue', 'IncidentAlertController@getOverdue');
	Route::get('/incidentRemain', 'IncidentAlertController@getRemain');
	Route::get('/getTask/{id}', 'IncidentController@getTask');
	Route::get('/getLog/{id}', 'IncidentController@getLog');
	Route::get('/getHistory/{id}', 'IncidentController@getHistory');
	Route::get('/getFile/{id}', 'IncidentController@getFile');
	Route::get('/getCustomerHistory/{id}', 'CustomerController@getHistory');
	Route::get('/getCustomerFile/{id}', 'IncidentController@getCustomerFile');
	Route::get('/getCustomerIp/{id}', 'PublicIpController@getCustomerIp');
	Route::delete('/deleteFile/{id}', 'IncidentController@deleteFile');
	Route::post('/addTask', 'IncidentController@addTask');
	Route::put('/editTask/{id}', 'IncidentController@editTask');
	Route::post('/uploadData', 'FileController@upload')->name('upload');
	Route::post('/getMenu', 'MenuController@getMenu');
	Route::get('/getDnId/{id}', 'PortController@getSNByDN');
	Route::get('/getDNInfo/{id}', 'PortController@getDNInfo');
	Route::get('/getOLTByPOP/{id}', 'PortController@getOLTByPOP');
	Route::get('/getDNByOLT/{id}', 'PortController@getDNByOLT');
	Route::get('/dashboard', 'DashboardController@show')->name('dashboard');
	Route::resource('/customer', CustomerController::class);
	Route::post('/customer/search/', 'CustomerController@show');
	Route::resource('/incident', IncidentController::class);
	Route::get('/incidentlist', 'IncidentController@getIncident');
	Route::get('/getCustomer/{id}', 'IncidentController@getCustomer');
	Route::get('importExportView', 'ExcelController@importExportView')->name('importExportView');
	// Route for export/download tabledata to .csv, .xls or .xlsx
	Route::post('/exportExcel', 'ExcelController@exportExcel')->name('exportExcel');
	// Route for import excel data to database.
	Route::post('importExcel', 'ExcelController@importExcel')->name('importExcel');
	Route::post('updateExcel', 'ExcelController@updateExcel')->name('updateExcel');


	Route::resource('/port', PortController::class);
	Route::post('/port', 'PortController@index')->name('port.search');
	Route::post('/port-store', 'PortController@store')->name('port.store');
	Route::resource('/snport', SNPortController::class);
	Route::get('/generateSN', 'SNPortController@generateSN');
	Route::delete('/snport/group/{id}', 'SNPortController@deleteGroup');
	Route::delete('/port/group/{id}', 'PortController@deleteGroup');
	Route::get('updateDNView', 'ExcelController@updateDNView')->name('updateDNView');
	Route::get('updateSNView', 'ExcelController@updateSNView')->name('updateSNView');
	Route::post('importDN', 'ExcelController@importDN')->name('importDN');
	Route::post('importSN', 'ExcelController@importSN')->name('importSN');

	//Billing 
	Route::get('tempImportView', 'ExcelController@tempImportView')->name('tempImportView');

	Route::get('updateContractView', 'ExcelController@updateContractView')->name('updateContractView');
	Route::get('updateTownshipView', 'ExcelController@updateTownshipView')->name('updateTownshipView');
	Route::post('updateContract', 'ExcelController@updateContract')->name('updateContract');
	Route::post('updateTownship', 'ExcelController@updateTownship')->name('updateTownship');

	Route::post('updateTempExcel', 'ExcelController@updateTemp')->name('updateTempExcel');
	Route::post('importPayment', 'ExcelController@importPayment')->name('importPayment');
	Route::get('updateCustomerView', 'ExcelController@updateCustomerView')->name('updateCustomerView');
	Route::post('updateCustomer', 'ExcelController@updateCustomer')->name('updateCustomer');

	Route::post('/exportBillingExcel', 'ExcelController@exportBillingExcel')->name('exportBillingExcel');
	Route::post('/exportTempBillingExcel', 'ExcelController@exportTempBillingExcel')->name('exportTempBillingExcel');
	Route::post('/exportRevenue', 'ExcelController@exportRevenue')->name('exportRevenue');

	Route::get('/billGenerator', 'BillingController@BillGenerator')->name('billGenerator');
	Route::post('/updateTemp', 'BillingController@updateTemp')->name('updateTemp');
	Route::post('/updateInvoice', 'BillingController@updateInvoice')->name('updateInvoice');
	Route::post('/createInvoice', 'BillingController@createInvoice')->name('createInvoice');
	Route::delete('/deleteInvoice/{id}', 'BillingController@destroyInvoice')->name('deleteInvoice');
	Route::post('/doGenerate', 'BillingController@doGenerate');
	Route::post('/saveFinal', 'BillingController@saveFinal');
	//Route::post('/showbill', 'BillingController@showBill')->name('showbill');
	Route::get('/showbill', 'BillingController@showBill')->name('showbill');

	Route::get('/tempBilling', 'BillingController@goTemp')->name('tempBilling');
	Route::post('/tempBilling/search/', 'BillingController@goTemp');
	Route::post('/truncateBilling', 'BillingController@destroyall');
	Route::resource('/billing', BillingController::class);

	//Billing PDF
	Route::get('/pdfpreview1/{id}', 'BillingController@preview_1');
	Route::get('/pdfpreview2/{id}', 'BillingController@preview_2');
	Route::get('/showInvoice/{id}', 'BillingController@showInvoice');
	Route::get('/ReceiptTemplate/{id}', 'ReceiptController@template');

	Route::post('/getSinglePDF/{id}', 'BillingController@makeSinglePDF');
	Route::post('/getReceiptPDF/{id}', 'ReceiptController@makeReceiptPDF');
	Route::post('/sendSingleEmail/{id}', 'BillingController@sendSingleEmail');
	Route::post('/getAllPDF', 'BillingController@makeAllPDF');
	Route::post('/sendSingleSMS/{id}', 'BillingController@sendSingleSMS');
	Route::post('/sendAllSMS', 'BillingController@sendAllSMS');
	Route::post('/sendBillReminder', 'BillingController@sendBillReminder');

	//Billing Receipt
	Route::post('/saveReceipt', 'ReceiptController@store');
	Route::post('/receipt/search', 'ReceiptController@show');
	Route::resource('/receipt', ReceiptController::class);
	Route::get('/saveSingle', 'BillingController@saveSingle');
	Route::get('/runSummery', 'ReceiptController@runReceiptSummery');
	Route::get('/gettclmax', 'CustomerController@gettclmaxid');
	Route::get('/getmkmax', 'CustomerController@getmkmaxid');


	//Email Template
	Route::resource('/template', EmailTemplateController::class);

	//SMS Gatweay
	Route::resource('/smsgateway', SmsGatewayController::class);

	//Radius Gateway
	Route::resource('/radiusconfig', RadiusController::class);
	Route::get('/fillAllRadius', 'RadiusController@autofillRadius');

	//Radius
	Route::get('/getRadiusInfo/{id}', 'RadiusController@getRadiusInfo');
	Route::get('/getRadiusServices', 'RadiusController@getRadiusServices');
	Route::post('/enableRadius/{id}', 'RadiusController@enableRadiusUser');
	Route::post('/saveRadius/{id}', 'RadiusController@saveRadius');
	Route::post('/createRadius/{id}', 'RadiusController@createRadius');
	Route::post('/disableRadius/{id}', 'RadiusController@disableRadiusUser');
	Route::get('/showRadius', 'RadiusController@display')->name('showRadius');
	Route::post('/showRadius', 'RadiusController@display');
	Route::post('/RadiusExport', 'ExcelController@exportRadiusReportExcel')->name('RadiusExport');
	Route::post('/tempDeactivate/{id}', 'RadiusController@tempDeactivate');
	Route::post('/tempActivate/{id}', 'RadiusController@tempActivate');

	//Announcement
	Route::get('/announcement/listall', 'AnnouncementController@listAll')->name('announcement.list');
	Route::get('/announcement/show', 'AnnouncementController@showAll');
	Route::resource('/announcement', AnnouncementController::class);
	Route::post('/announcement/show', 'AnnouncementController@showAll');
	Route::get('/announcement/detail/{id}', 'AnnouncementController@detail')->name('announcement.detail');
	Route::post('/announcement/detail/{id}', 'AnnouncementController@detail');
	Route::get('/announcement/log/{id}', 'AnnouncementController@log')->name('announcement.log');
	Route::post('/announcement/detail/{id}/update', 'AnnouncementController@update');
	Route::post('/announcement/detail/{id}/send', 'AnnouncementController@send');
	Route::post('/exportAnnouncementLogExcel', 'ExcelController@exportAnnouncementLog')->name('exportAnnouncementLog');

	//test
	Route::get('/testCustomer', 'CustomerController@preg_test');
	//Service Request
	Route::resource('/servicerequest', ServiceRequestController::class);

	//Reports 

	Route::resource('/dailyreceipt', DailyReceiptController::class);
	Route::post('/dailyreceipt/show', 'DailyReceiptController@index');
	Route::get('/dailyreceipt/show', 'DailyReceiptController@index')->name('dailyreceipt');
	Route::post('/exportReceipt', 'ExcelController@exportReceipt')->name('exportReceipt');

	Route::get('/incidentReport', 'ReportController@incidentReport')->name('incidentReport');
	Route::post('/incidentReport', 'ReportController@incidentReport');
	Route::get('/getIncidentDetail/{id}/{date}', 'ReportController@getIncidentDetail');
	Route::post('/exportIncidentReportExcel', 'ExcelController@exportIncidentReportExcel')->name('exportIncidentReportExcel');

	//Bill Configuration
	Route::resource('/billconfig', BillingConfiguration::class);

	//Utils 
	Route::get('/sanitiseAllPhone', 'BillingController@sanitiseAllPhone');

	//POPs
	Route::get('/getPackages/{id}', 'PackageController@getPackage');

	Route::resource('/publicIP', PublicIpController::class);

	Route::get('/publicIpReport', 'ReportController@PublicIpReport')->name('publicIpReport');
	Route::post('/publicIpReport', 'ReportController@PublicIpReport');
	Route::post('/exportPublicIpReportExcel', 'ExcelController@exportPublicIpReportExcel')->name('exportPublicIpReportExcel');
	Route::resource('/test', TestController::class);
	Route::post('/doTestPDF', 'TestController@makeSinglePDF');
	Route::post('/delTestPDF', 'TestController@destroyPDF');

	//DBBackup
	Route::get('/dbbackup', [DBBackupController::class, 'index'])->name('dbbackup.index');
	Route::post('/dbbackup', [DBBackupController::class, 'update'])->name('dbbackup.update');
	Route::post('/dbbackup-store', [DBBackupController::class, 'backup'])->name('dbbackup.store');
});

Route::get('/s/{shortURLKey}', '\AshAllenDesign\ShortURL\Controllers\ShortURLController');