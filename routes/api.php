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

    
    Route::group(['middleware' => 'auth:api'], function(){
        Route::post('register', 'Api\AuthController@register');
        Route::post('login', 'Api\AuthController@login');
        
            Route::get('showvehiclenumberinsales', 'Api\PurchaseOrderController@showVehicleNumberinSales');
        
            Route::get('salesorder/{id}', 'Api\SalesOrderController@show');
            Route::post('salesorder', 'Api\SalesOrderController@store');
            Route::put('salesorder/{id}', 'Api\SalesOrderController@update');
            Route::delete('salesorder/{id}', 'Api\SalesOrderController@destroy');
            Route::get('salesorder', 'Api\SalesOrderController@index');
            Route::get('showagreementnumber', 'Api\SalesOrderController@showAgreementNumber');
            Route::get('showactivesales', 'Api\SalesOrderController@showActiveSales');
            Route::get('test', 'Api\SalesOrderController@test');
        
            Route::get('showagreementnumberinrehiring', 'Api\SalesOrderController@showAgreementNumberInRehiring');
            Route::get('showagreementnumberinvehiclesold', 'Api\SalesOrderController@showAgreementNumberInVehicleSold');
        
            Route::get('purchaseorder/{id}', 'Api\PurchaseOrderController@show');
            Route::post('purchaseorder', 'Api\PurchaseOrderController@store');
            Route::put('purchaseorder/{id}', 'Api\PurchaseOrderController@update');
            Route::delete('purchaseorder/{id}', 'Api\PurchaseOrderController@destroy');
            Route::get('purchaseorder', 'Api\PurchaseOrderController@index');
            Route::get('purchaseorderall', 'Api\PurchaseOrderController@indexAll');
            Route::get('showvehiclenumberexceptsold', 'Api\PurchaseOrderController@showVehicleNumberExceptSold');
            Route::get('showsalesnumberinvehiclesold', 'Api\PurchaseOrderController@showSalesNumberInVehicleSold');
            Route::get('availablestock', 'Api\PurchaseOrderController@availableStock');
            
        
            Route::get('showcontractbyid/{id}', 'Api\PurchaseOrderController@showContractById');
            Route::get('showvehiclebyid/{id}', 'Api\PurchaseOrderController@showVehicleById');
            Route::get('showcostandfunding/{id}', 'Api\PurchaseOrderController@showCostandFunding');
        
            Route::get('showdashboard/{date1},{date2}', 'Api\PurchaseOrderController@showDashboard');
        
            Route::get('countvehiclehired/{date1},{date2}', 'Api\PurchaseOrderController@countVehicleHired');
            Route::get('countvehiclesold/{date1},{date2}', 'Api\PurchaseOrderController@countVehicleSold');
        
            Route::get('laporan/{date1},{date2}', 'Api\PurchaseOrderController@laporan');
        
            //dashboard
            Route::get('sumtotalincome', 'Api\PurchaseOrderController@sumTotalIncome');
            Route::get('sumtotalcost', 'Api\PurchaseOrderController@sumTotalCost');
            Route::get('sumrentalincome', 'Api\PurchaseOrderController@sumRentalIncome');
            Route::get('sumotherincome', 'Api\PurchaseOrderController@sumOtherIncome');
            Route::get('sumothercost', 'Api\PurchaseOrderController@sumOtherCost');
            Route::get('sumsoldprice', 'Api\PurchaseOrderController@sumSoldPrice');
            Route::get('sumresidualvalue', 'Api\PurchaseOrderController@sumResidualValue');
            
            
            Route::get('listvehicleinvehiclecard/{id}', 'Api\PurchaseOrderController@listVehicleInVehicleCard');
            Route::get('listtotalincard/{id}', 'Api\PurchaseOrderController@listTotalInCard');
            Route::get('listcostincard/{id}', 'Api\PurchaseOrderController@listCostInCard');
            
            Route::get('listtotalincome/{id}', 'Api\PurchaseOrderController@listTotalIncome');
            Route::get('listtotalcost/{id}', 'Api\PurchaseOrderController@listTotalCost');
            Route::get('listrentalincome/{id}', 'Api\PurchaseOrderController@listRentalIncome');
            Route::get('listotherincome/{id}', 'Api\PurchaseOrderController@listOtherIncome');
            Route::get('listothercost/{id}', 'Api\PurchaseOrderController@listOtherCost');
            Route::get('listsoldprice/{id}', 'Api\PurchaseOrderController@listSoldPrice');
            Route::get('listresidualvalue/{id}', 'Api\PurchaseOrderController@listResidualValue');
            
            Route::get('compilationdb', 'Api\PurchaseOrderController@compilationDB');
           
            Route::get('showvehicle', 'Api\PurchaseOrderController@showVehicle');
            Route::get('listvehiclebyid/{id}', 'Api\PurchaseOrderController@listVehicleById');
            
            Route::get('showvehiclenumber', 'Api\PurchaseOrderController@showVehicleNumber');
            Route::get('showvehiclenumberinothercost', 'Api\PurchaseOrderController@showVehicleNumberInOtherCost');
            Route::get('showvehiclenumberinotherincome', 'Api\PurchaseOrderController@showVehicleNumberInOtherIncome');
            
            Route::get('rehiringorder/{id}', 'Api\RehiringController@show');
            Route::post('rehiringorder', 'Api\RehiringController@store');
            Route::put('rehiringorder/{id}', 'Api\RehiringController@update');
            Route::delete('rehiringorder/{id}', 'Api\RehiringController@destroy');
            Route::get('rehiringorder', 'Api\RehiringController@index');
            Route::get('showvehiclesold', 'Api\RehiringController@showVehicleSold');
            Route::put('updatevehiclesold/{id}', 'Api\RehiringController@updateVehicleSold');
        
            Route::get('vehiclesold/{id}', 'Api\VehicleSoldController@show');
            Route::post('vehiclesold', 'Api\VehicleSoldController@store');
            Route::put('vehiclesold/{id}', 'Api\VehicleSoldController@update');
            Route::delete('vehiclesold/{id}', 'Api\VehicleSoldController@destroy');
            Route::get('vehiclesold', 'Api\VehicleSoldController@index');
        
            Route::get('showvehiclerehiringorder', 'Api\RehiringController@showVehicleRehiringOrder');
            
            Route::get('othercost/{id}', 'Api\OtherCostController@show');
            Route::post('othercost', 'Api\OtherCostController@store');
            Route::put('othercost/{id}', 'Api\OtherCostController@update');
            Route::delete('othercost/{id}', 'Api\OtherCostController@destroy');
            Route::get('othercost', 'Api\OtherCostController@index');
        
            Route::get('otherincome/{id}', 'Api\OtherIncomeController@show');
            Route::post('otherincome', 'Api\OtherIncomeController@store');
            Route::put('otherincome/{id}', 'Api\OtherIncomeController@update');
            Route::delete('otherincome/{id}', 'Api\OtherIncomeController@destroy');
            Route::get('otherincome', 'Api\OtherIncomeController@index');
        
            Route::get('mileage/{id}', 'Api\MileageController@show');
            Route::post('mileage', 'Api\MileageController@store');
            Route::put('mileage/{id}', 'Api\MileageController@update');
            Route::delete('mileage/{id}', 'Api\MileageController@destroy');
            Route::get('mileage', 'Api\MileageController@index');
            Route::get('listmileagebyid/{id}', 'Api\MileageController@listMileageById');
        
            Route::get('baseinterest/{id}', 'Api\BaseInterestController@show');
            Route::post('baseinterest', 'Api\BaseInterestController@store');
            Route::put('baseinterest/{id}', 'Api\BaseInterestController@update');
            Route::delete('baseinterest/{id}', 'Api\BaseInterestController@destroy');
            Route::get('baseinterest', 'Api\BaseInterestController@index');
            Route::put('updatebaseinterest/{id}', 'Api\BaseInterestController@updateStatus');
            Route::get('findbaseinterest', 'Api\BaseInterestController@findBaseInterest');
            Route::get('showbaseinterest/{date1},{date2}', 'Api\BaseInterestController@showBaseInterest');
        
            Route::get('sumtotalbaseinterest/{id}', 'Api\BaseInterestDetailController@sumTotalBaseInterest');
            Route::post('baseinterestdetail', 'Api\BaseInterestDetailController@store');
    Route::post('logout', 'Api\AuthController@logout');

});

