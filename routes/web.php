<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\TopBarController;  
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\RfidController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\MainModuleController;
use App\Http\Controllers\Sys\Modu\SysModuMainsController;
use App\Http\Controllers\Sys\Modu\SysModuSubmsController;
use App\Http\Controllers\Sys\Modu\SysModuTabmsController;
use App\Http\Controllers\Sys\Orga\SysOrgaCtrlsController;
use App\Http\Controllers\Sys\SysModuController;
use App\Http\Controllers\Sys\SysOrgaController;
use App\Http\Controllers\Sys\SysUsrmController;
use App\Http\Controllers\Sys\Usrm\SysUsrmGrousController;
use App\Http\Controllers\Sys\Usrm\SysUsrmGrpcsController;
use App\Http\Controllers\Qms\Ipqa\QmsIpqaTempsController;
use App\Http\Controllers\Sys\Usrm\SysUsrmUsersController;
use App\Http\Controllers\Tes\Test\TesTestTeslsController;

/*  THIS IS ROUTE TEMPLATE FOR REFERENCE

    Route::prefix('/template')->group(function(){
        Route::get('/', [TemplateController::class, 'index'])->name('temp.level2.level3.index'); //Follow the model name
        Route::get('/create', [TemplateController::class, 'create'])->name('temp.level2.level3.create');
        Route::post('/store', [TemplateController::class, 'store'])->name('temp.level2.level3.store');
        Route::post('/show', [TemplateController::class, 'show'])->name('temp.level2.level3.show');
        Route::post('/edit', [TemplateController::class, 'edit'])->name('temp.level2.level3.edit');
        Route::post('/update', [TemplateController::class, 'update'])->name('temp.level2.level3.update');
        Route::delete('/{id}', [TemplateController::class, 'destroy'])->name('temp.level2.level3.destroy');
    });

*/

Route::get('/Test/Layout', [HomeController::class, 'test']);

Route::get('/',[LoginController::class, 'index'])->name('login.page'); 
Route::post('/', [LoginController::class, 'login'])->name('login');
Route::get('/reset/password', [ResetPasswordController::class, 'index'])->name('password.reset');
Route::post('/reset/passsword', [ResetPasswordController::class, 'resetPassword'])->name('password.update');
Route::post('/wms/organisation/change', [TopBarController::class,'changeOrganisation'])->name('change.organisation');
Route::post('/wms/organisation/update', [TopBarController::class,'updateOrganisationStatus'])->name('change.organisation.status');
Route::get('wms/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'auth.session'])->group(function(){
    Route::prefix('wms/{organisation}')->group(function () {

        Route::get('/home', [HomeController::class, 'index'])->name('home');
        Route::get('/select/module/{subModule}', [HomeController::class, 'selectModule'])->name('select-module');
        Route::prefix('/qmsManagement')->group(function(){

            Route::prefix('/smt')->group(function (){

                Route::get('/', [QmsIpqaPsmtsController::class, 'index'])->name('qms.ipqa.psmts.index');
                //Route::post('/show', [SysUsrmUsersController::class, 'show'])->name('sys.usrm.users.show');
                Route::post('/create', [QmsIpqaPsmtsController::class, 'create'])->name('qms.ipqa.psmts.create');
                //Route::post('/store', [SysUsrmUsersController::class, 'store'])->name('sys.usrm.users.store');
                //Route::post('/edit', [SysUsrmUsersController::class, 'edit'])->name('sys.usrm.users.edit');
                //Route::post('/update', [SysUsrmUsersController::class, 'update'])->name('sys.usrm.users.update');
                //Route::delete('/destroy', [SysUsrmUsersController::class, 'destroy'])->name('sys.usrm.users.destroy');
                //Route::post('/activate', [SysUsrmUsersController::class,'activate'])->name('sys.usrm.users.activate');
                //Route::post('/deactivate', [SysUsrmUsersController::class,'deactivate'])->name('sys.usrm.users.deactivate');
                //Route::post('reset-password/default', [ResetPasswordController::class, 'resetPasswordDefault'])->name('sys.usrm.users.password-reset-default');

            });

            Route::prefix('/template')->group(function (){
                
                Route::get('/', [QmsIpqaTempsController::class, 'index'])->name('qms.ipqa.temps.index');
                Route::post('/show', [QmsIpqaTempsController::class, 'show'])->name('qms.ipqa.temps.show');
                Route::post('/create', [QmsIpqaTempsController::class, 'create'])->name('qms.ipqa.temps.create');

                // Route::post('/show/child-data', [SysUsrmGrpcsController::class, 'showChildData'])->name('sys.usrm.grpcs.show-child-data');
                // Route::post('/create', [SysUsrmGrpcsController::class, 'create'])->name('sys.usrm.grpcs.create');
                Route::post('/store', [QmsIpqaTempsController::class, 'store'])->name('qms.ipqa.temps.store');
                // Route::post('/edit', [SysUsrmGrpcsController::class, 'edit'])->name('sys.usrm.grpcs.edit');
                // Route::post('/update', [SysUsrmGrpcsController::class, 'update'])->name('sys.usrm.grpcs.update');
                // Route::delete('/destroy', [SysUsrmGrpcsController::class, 'destroy'])->name('sys.usrm.grpcs.destroy');
                // Route::post('/add/user', [SysUsrmGrpcsController::class, 'addUser'])->name('sys.usrm.grpcs.add-user');
                // Route::post('/store/user', [SysUsrmGrpcsController::class, 'storeUser'])->name('sys.usrm.grpcs.store-user');
                // Route::post('/destroy/user', [SysUsrmGrpcsController::class, 'destroyUser'])->name('sys.usrm.grpcs.destroy-user');

            });
        });
        Route::prefix('/userManagement')->group(function(){

            Route::prefix('/userControl')->group(function (){

                Route::get('/', [SysUsrmUsersController::class, 'index'])->name('sys.usrm.users.index');
                Route::post('/show', [SysUsrmUsersController::class, 'show'])->name('sys.usrm.users.show');
                Route::post('/create', [SysUsrmUsersController::class, 'create'])->name('sys.usrm.users.create');
                Route::post('/store', [SysUsrmUsersController::class, 'store'])->name('sys.usrm.users.store');
                Route::post('/edit', [SysUsrmUsersController::class, 'edit'])->name('sys.usrm.users.edit');
                Route::post('/update', [SysUsrmUsersController::class, 'update'])->name('sys.usrm.users.update');
                Route::delete('/destroy', [SysUsrmUsersController::class, 'destroy'])->name('sys.usrm.users.destroy');
                Route::post('/activate', [SysUsrmUsersController::class,'activate'])->name('sys.usrm.users.activate');
                Route::post('/deactivate', [SysUsrmUsersController::class,'deactivate'])->name('sys.usrm.users.deactivate');
                Route::post('reset-password/default', [ResetPasswordController::class, 'resetPasswordDefault'])->name('sys.usrm.users.password-reset-default');

            });

            Route::prefix('/groupControl')->group(function (){
                
                Route::get('/', [SysUsrmGrpcsController::class, 'index'])->name('sys.usrm.grpcs.index');
                Route::post('/show', [SysUsrmGrpcsController::class, 'show'])->name('sys.usrm.grpcs.show');
                Route::post('/show/child-data', [SysUsrmGrpcsController::class, 'showChildData'])->name('sys.usrm.grpcs.show-child-data');
                Route::post('/create', [SysUsrmGrpcsController::class, 'create'])->name('sys.usrm.grpcs.create');
                Route::post('/store', [SysUsrmGrpcsController::class, 'store'])->name('sys.usrm.grpcs.store');
                Route::post('/edit', [SysUsrmGrpcsController::class, 'edit'])->name('sys.usrm.grpcs.edit');
                Route::post('/update', [SysUsrmGrpcsController::class, 'update'])->name('sys.usrm.grpcs.update');
                Route::delete('/destroy', [SysUsrmGrpcsController::class, 'destroy'])->name('sys.usrm.grpcs.destroy');
                Route::post('/add/user', [SysUsrmGrpcsController::class, 'addUser'])->name('sys.usrm.grpcs.add-user');
                Route::post('/store/user', [SysUsrmGrpcsController::class, 'storeUser'])->name('sys.usrm.grpcs.store-user');
                Route::post('/destroy/user', [SysUsrmGrpcsController::class, 'destroyUser'])->name('sys.usrm.grpcs.destroy-user');

            });
        });

        Route::prefix('/moduleManagement')->group(function (){

            Route::prefix('/main')->group(function (){

                Route::get('/', [SysModuMainsController::class, 'index'])->name('sys.modu.mains.index');
                Route::post('/show', [SysModuMainsController::class, 'show'])->name('sys.modu.mains.show');
                Route::post('/create', [SysModuMainsController::class, 'create'])->name('sys.modu.mains.create');
                Route::post('/store', [SysModuMainsController::class, 'store'])->name('sys.modu.mains.store');
                Route::delete('/destroy', [SysModuMainsController::class, 'destroy'])->name('sys.modu.mains.destroy');
                Route::post('/activate', [SysModuMainsController::class,'activate'])->name('sys.modu.mains.activate');
                Route::post('/deactivate', [SysModuMainsController::class,'deactivate'])->name('sys.modu.mains.deactivate');

            });

            Route::prefix('/sub')->group(function (){

                Route::get('/', [SysModuSubmsController::class, 'index'])->name('sys.modu.subms.index');
                Route::post('/show', [SysModuSubmsController::class, 'show'])->name('sys.modu.subms.show');
                // Route::post('/getPath', [SysModuSubmsController::class, ''])->name('getPath');
                Route::post('/create', [SysModuSubmsController::class, 'create'])->name('sys.modu.subms.create');
                Route::post('/store', [SysModuSubmsController::class, 'store'])->name('sys.modu.subms.store');
                Route::delete('/destroy', [SysModuSubmsController::class, 'destroy'])->name('sys.modu.subms.destroy');
                Route::post('/activate', [SysModuSubmsController::class,'activate'])->name('sys.modu.subms.activate');
                Route::post('/deactivate', [SysModuSubmsController::class,'deactivate'])->name('sys.modu.subms.deactivate');
                Route::post('/show/path', [SysModuSubmsController::class,'showPath'])->name('sys.modu.subms.show-path');
            });


            Route::prefix('/tab')->group(function (){
                Route::get('/', [SysModuTabmsController::class, 'index'])->name('sys.modu.tabms.index');
                Route::post('/show', [SysModuTabmsController::class, 'show'])->name('sys.modu.tabms.show');
                Route::post('/create', [SysModuTabmsController::class, 'create'])->name('sys.modu.tabms.create');
                Route::post('/store', [SysModuTabmsController::class, 'store'])->name('sys.modu.tabms.store');
                Route::delete('/destroy', [SysModuTabmsController::class, 'destroy'])->name('sys.modu.tabms.destroy');
                Route::post('/activate', [SysModuTabmsController::class,'activate'])->name('sys.modu.tabms.activate');
                Route::post('/deactivate', [SysModuTabmsController::class,'deactivate'])->name('sys.modu.tabms.deactivate');
                Route::post('/show/path', [SysModuTabmsController::class,'showPath'])->name('sys.modu.tabms.show-path');
            });

        });

        Route::prefix('/organisationManagement')->group(function(){

            Route::prefix('/control')->group(function (){
                Route::get('/', [SysOrgaCtrlsController::class, 'index'])->name('sys.orga.ctrls.index');
                Route::post('/show', [SysOrgaCtrlsController::class, 'show'])->name('sys.orga.ctrls.show');
                Route::post('/show/child-data', [SysOrgaCtrlsController::class, 'showChildData'])->name('sys.orga.ctrls.show-child-data');
                Route::post('/create', [SysOrgaCtrlsController::class, 'create'])->name('sys.orga.ctrls.create');
                Route::post('/store', [SysOrgaCtrlsController::class, 'store'])->name('sys.orga.ctrls.store');
                Route::post('/edit', [SysOrgaCtrlsController::class, 'edit'])->name('sys.orga.ctrls.edit');
                Route::post('/update', [SysOrgaCtrlsController::class, 'update'])->name('sys.orga.ctrls.update');
                Route::delete('/destroy', [SysOrgaCtrlsController::class, 'destroy'])->name('sys.orga.ctrls.destroy');
                Route::post('/destroy/user', [SysOrgaCtrlsController::class, 'destroyUser'])->name('sys.orga.ctrls.destroy-user');
            });

        });



        Route::get('/excelControl', [ExcelController::class, 'index'])->name('excelControl');
        Route::post('/excelContol/getExcelData', [ExcelController::class, 'getExcelData'])->name('getExcelData');
        Route::post('/excelControl/addExcel', [ExcelController::class, 'addExcel'])->name('addExcel');
        Route::post('/excelControl/editExcel', [ExcelController::class, 'editExcel'])->name('editExcel');
        Route::post('/excelControl/addExcelSubmit', [ExcelController::class, 'addExcelSubmit'])->name('addExcelSubmit');
        Route::post('/excelControl/editExcelSubmit', [ExcelController::class, 'editExcelSubmit'])->name('editExcelSubmit');
    }); 
});

Route::get('/rfidControl', [RfidController::class, 'index'])->name('rfidControl');
Route::post('/rfidControl/getRfidData', [RfidController::class,'getRfidData'])->name('getRfidData');
Route::post('/rfidControl/getRfidChildData', [RfidController::class,'getRfidChildData'])->name('getRfidChildData');
Route::post('/rfidControl/printRfid', [RfidController::class,'printRfid'])->name('printRfid');
Route::post('/rfidControl/printRfidSubmit', [RfidController::class,'printRfidSubmit'])->name('printRfidSubmit');

Route::get('/wms/warehouseControl', [WarehouseController::class, 'index'])->name('warehouseControl');
Route::post('/wms/warehouseControl/getWarehouseData', [WarehouseController::class,'getWarehouseData'])->name('getWarehouseData');
Route::post('/wms/warehouse/addView', [WarehouseController::class,'addWarehouse'])->name('addWarehouse');
Route::post('/wms/warehouse/getNotWarehouseUser', [WarehouseController::class,'getNotWarehouseUser'])->name('getNotWarehouseUser');
Route::post('/wms/warehouse/editView', [WarehouseController::class,'editWarehouse'])->name('editWarehouse');
Route::post('/wms/warehouse/addUserToWarehouseView', [WarehouseController::class,'addUserToWarehouse'])->name('addUserToWarehouse');
Route::post('/wms/warehouse/addUserToWarehouseSubmit', [WarehouseController::class,'addUserToWarehouseSubmit'])->name('addUserToWarehouseSubmit');
Route::post('/wms/warehouse/addSubmit', [WarehouseController::class,'addWarehouseSubmit'])->name('addWarehouseSubmit');
Route::post('/wms/warehouse/editSubmit', [WarehouseController::class,'editWarehouseSubmit'])->name('editWarehouseSubmit');
Route::post('/wms/warehouse/delete', [WarehouseController::class,'remove'])->name('deleteWarehouse');
Route::post('/wms/warehouse/getChildData', [WarehouseController::class,'getChildData'])->name('getChildData');
Route::post('/wms/warehouse/removeWarehouseUser', [WarehouseController::class, 'removeWarehouseUser'])->name('removeWarehouseUser');

// Template
Route::get('/wms/template', [TemplateController::class, 'method']);

Route::get('/excel', [ExcelController::class, 'index'])->name('excel');
Route::post('/upload', [ExcelController::class, 'upload'])->name('upload');
Route::get('/edit/{id}', [ExcelController::class, 'edit'])->name('edit');
Route::post('/save/{id}', [ExcelController::class, 'save'])->name('save');