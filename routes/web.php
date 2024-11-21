<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TopBarController;  
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\RfidController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\Sys\Modu\SysModuMainsController;
use App\Http\Controllers\Sys\Modu\SysModuSubmsController;
use App\Http\Controllers\Sys\Modu\SysModuTabmsController;
use App\Http\Controllers\Sys\Orga\SysOrgaCtrlsController;
use App\Http\Controllers\Sys\Usrm\SysUsrmGrpcsController;
use App\Http\Controllers\Qms\Ipqa\QmsIpqaPsmtsController;
use App\Http\Controllers\Qms\Ipqa\QmsIpqaTempsController;
use App\Http\Controllers\Qms\Role\QmsRoleAsgnsController;
use App\Http\Controllers\Sys\Usrm\SysUsrmUsersController;
use App\Http\Controllers\Wms\Item\WmsItemDatasController;
use App\Http\Controllers\Wms\Item\WmsItemGrpcsController;
use App\Http\Controllers\Wms\Whmg\WmsWhmgWhmgsController;
use App\Http\Controllers\Qms\Role\QmsRoleRollsController;
use App\Http\Controllers\Env\Sinv\EnvSinvSinvsController;
use App\Http\Controllers\Env\Sinv\EnvSinvCndnsController;
use App\Http\Controllers\Env\Tmsv\EnvTmsvSinvsController;
use App\Http\Controllers\Env\Tmsv\EnvTmsvUpinsController;
use App\Http\Controllers\Mrb\Lvlc\MrbLvlcGallsController;
use App\Http\Controllers\Mrb\Lvlc\MrbLvlcRollsController;
use App\Http\Controllers\Mrb\NewFormController;
use App\Http\Controllers\Permission;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Role;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Sys\Role\SysRoleAssgsController;
use App\Http\Controllers\Sys\Role\SysRoleAuthsController;
use App\Http\Controllers\Sys\Usrm\SysUsrmRolesController;
use App\Http\Controllers\Wms\Invt\WmsInvtCtrlsController;
use App\Http\Controllers\Wms\Rcpt\WmsRcptItemsController;
use App\Http\Controllers\Wms\Rfid\WmsRfidMgmtsController;
use App\Http\Controllers\Wms\Role\WmsRoleAsgnsController;
use App\Http\Controllers\Wms\Role\WmsRolePermsController;
use App\Http\Controllers\Wms\Ship\WmsShipCtrlsController;
use App\Http\Controllers\Wms\Wgrn\WmsWgrnCtrlsController;
use App\Http\Controllers\Wms\Whmg\WmsWhmgLoctsController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

Route::get('/database-query', [EnvSinvSinvsController::class, 'index']);
Route::get('/phpinfo', function () {
    phpinfo();
});
Route::get('/test-sftp', function () {
    try {
        $disk = Storage::disk('sftp');
        
        // Attempt to list files in the root directory to test connection
        $files = $disk->files('/');
        
        return response()->json(['message' => 'SFTP connection successful!', 'files' => $files]);
    } catch (Exception $e) {
        Log::error('SFTP connection test failed: ' . $e->getMessage());
        return response()->json(['message' => 'SFTP connection failed: ' . $e->getMessage()], 500);
    }
});
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
    Route::prefix('wms/{organisation}')->middleware('organisation')->group(function () {

        Route::get('/home', [HomeController::class, 'index'])->name('home');
        Route::get('/select/module/{subModule}', [HomeController::class, 'selectModule'])->name('select-module')->middleware('mainModulePermission');
        Route::prefix('/qmsRoleManagement')->group(function(){
            Route::prefix('/userRole')->group(function (){
                Route::get('/', [QmsRoleRollsController::class, 'index'])->name('qms.role.rolls.index');
                Route::post('/show', [QmsRoleRollsController::class, 'show'])->name('qms.role.rolls.show');

            });

            Route::prefix('/roleAssign')->group(function (){
                Route::get('/', [QmsRoleAsgnsController::class, 'index'])->name('qms.role.asgns.index');
                Route::post('/show', [QmsRoleAsgnsController::class, 'show'])->name('qms.role.asgns.show');
                Route::delete('/destroy', [QmsRoleAsgnsController::class, 'destroy'])->name('qms.role.asgns.destroy');
                Route::post('/create', [QmsRoleAsgnsController::class, 'create'])->name('qms.role.asgns.create');                
                Route::post('/getUser', [QmsRoleAsgnsController::class, 'getUsersWithoutRole'])->name('qms.role.asgns.getUsersWithoutRole');
                Route::post('/store', [QmsRoleAsgnsController::class, 'store'])->name('qms.role.asgns.store');
                Route::post('/edit', [QmsRoleAsgnsController::class, 'edit'])->name('qms.role.asgns.edit');
                Route::post('/update', [QmsRoleAsgnsController::class, 'update'])->name('qms.role.asgns.update');
            });
        });

        // Route::get('/database-query', [EnvSinvSinvsController::class, 'index']);
        Route::prefix('/env')->group(function () {
            Route::prefix('/sinv')->group(function () {
                Route::get('/', [EnvSinvSinvsController::class, 'index'])->name('env.sinv.sinvs.index');
                Route::post('/show', [EnvSinvSinvsController::class, 'show'])->name('env.sinv.sinvs.show');
                Route::post('/create', [EnvSinvSinvsController::class, 'create'])->name('env.sinv.sinvs.create'); 
                Route::post('/save-csv', [EnvSinvSinvsController::class, 'saveCSV'])->name('env.sinv.sinvs.save-csv');
                Route::post('/save-ftp', [EnvSinvSinvsController::class, 'saveFTP'])->name('env.sinv.sinvs.save-ftp');

              
    
                }); 
            Route::prefix('/cndn')->group(function () {
                Route::get('/', [EnvSinvCndnsController::class, 'index'])->name('env.sinv.cndns.index');
                Route::post('/show', [EnvSinvCndnsController::class, 'show'])->name('env.sinv.cndns.show');
                Route::post('/create', [EnvSinvCndnsController::class, 'create'])->name('env.sinv.cndns.create'); 
                Route::post('/save-csv', [EnvSinvCndnsController::class, 'saveCSV'])->name('env.sinv.cndns.save-csv');
                Route::post('/save-ftp', [EnvSinvCndnsController::class, 'saveFTP'])->name('env.sinv.cndns.save-ftp');
                        }); 
            Route::prefix('/tmsmtimport')->group(function () {
                Route::get('/', [EnvTmsvSinvsController::class, 'index'])->name('env.tmsv.sinvs.index');
                Route::post('/show', [EnvTmsvSinvsController::class, 'show'])->name('env.tmsv.sinvs.show');
                Route::post('/create', [EnvTmsvSinvsController::class, 'create'])->name('env.tmsv.sinvs.create');
                Route::get('/import', [EnvTmsvSinvsController::class, 'showImportForm'])->name('env.tmsv.sinvs.import.form');
                Route::post('/import', [EnvTmsvSinvsController::class, 'import'])->name('env.tmsv.sinvs.import');
                Route::post('/detail', [EnvTmsvSinvsController::class, 'showImportForm'])->name('env.tmsv.sinvs.import.form');
            }); 
            Route::prefix('/tmsmtupload')->group(function () {
                Route::get('/', [EnvTmsvUpinsController::class, 'index'])->name('env.tmsv.upins.index');
                Route::post('/show', [EnvTmsvUpinsController::class, 'show'])->name('env.tmsv.upins.show');

            });
                });
        
                /*  THIS IS ROUTE TEMPLATE FOR REFERENCE

                    Route::prefix('/E-Invoice')->group(function(){
                        Route::get('/database-query', [EnvSinvSinvsController::class, 'index'])->name('temp.level2.level3.index'); //Follow the model name
                        Route::get('/create', [TemplateController::class, 'create'])->name('temp.level2.level3.create');
                        Route::post('/store', [TemplateController::class, 'store'])->name('temp.level2.level3.store');
                        Route::post('/show', [TemplateController::class, 'show'])->name('temp.level2.level3.show');
                        Route::post('/edit', [TemplateController::class, 'edit'])->name('temp.level2.level3.edit');
                        Route::post('/update', [TemplateController::class, 'update'])->name('temp.level2.level3.update');
                        Route::delete('/{id}', [TemplateController::class, 'destroy'])->name('temp.level2.level3.destroy');
                    });

                */

    Route::prefix('/emrb')->group(function(){
        Route::get('/newEMRB/{newEMRBId}', [NewFormController::class, 'index'])->name('mrb.emrb.custom');
        Route::post('/loadWODetails', [NewFormController::class, 'loadWODetails'])->name('mrb.emrb.loadWoDetails');
        Route::post('/showAddContent/{customer}/{mrbFormId}', [NewFormController::class, 'create'])->name('mrb.emrb.create');
        Route::post('/getDataFromInforln', [NewFormController::class, 'qtyCur'])->name('mrb.emrb.qtyCur');
        Route::post('/submitEmrbHeader', [NewFormController::class, 'store'])->name('mrb.emrb.store');
        Route::post('/submitEmrbContent', [NewFormController::class, 'storeEmrbContent'])->name('mrb.emrb.storeEmrbContent');
        Route::post('/showEmrbContent', [NewFormController::class, 'showEmrbContent'])->name('mrb.emrb.showEmrbContent');
        Route::delete('/destroyEmrbContent', [NewFormController::class, 'destroyEmrbContent'])->name('mrb.emrb.destroyContent');
        Route::post('/editEmrbContent/{customer}/{mrbFormId}', [NewFormController::class, 'edit'])->name('mrb.emrb.edit');
        Route::post('/updateEmrbContent', [NewFormController::class, 'updateEmrbContent'])->name('mrb.emrb.updateEmrbContent');
        Route::post('/submitMrbForm', [NewFormController::class, 'submitMrbForm'])->name('mrb.emrb.submitMrbForm');
        Route::post('/approveMrb', [NewFormController::class, 'approveMrb'])->name('mrb.emrb.approveMrb');
        Route::post('/rejectMrb', [NewFormController::class, 'rejectMrb'])->name('mrb.emrb.rejectMrb');

        Route::prefix('/role')->group(function (){

            Route::get('/', [MrbLvlcRollsController::class, 'index'])->name('mrb.lvlc.rolls.index');
            Route::post('/show', [MrbLvlcRollsController::class, 'show'])->name('mrb.lvlc.rolls.show');
            // Route::post('/getWODetails', [QmsIpqaPsmtsController::class, 'getWorkOrderDetails'])->name('qms.ipqa.psmts.getWorkOrderDetails');
            // Route::post('/getCustomerWODetails', [QmsIpqaPsmtsController::class, 'getCustomerWorkOrderDetails'])->name('qms.ipqa.psmts.getCustomerWorkOrderDetails');    
            // Route::post('/getTemplate', [QmsIpqaPsmtsController::class, 'getTemplate'])->name('qms.ipqa.psmts.getTemplate');
            // Route::post('/store', [QmsIpqaPsmtsController::class, 'store'])->name('qms.ipqa.psmts.store');
            // Route::post('/getWOWithCustomerWODetails', [QmsIpqaPsmtsController::class, 'getWOWithCustomerWODetails'])->name('qms.ipqa.psmts.getWOWithCustomerWODetails');
            // Route::post('/downloadTemplate', [QmsIpqaPsmtsController::class, 'stepOne'])->name('qms.ipqa.psmts.stepOne');
            // Route::post('/getStep2Modal', [QmsIpqaPsmtsController::class, 'getStep2Modal'])->name('qms.ipqa.psmts.getStep2Modal');
            // Route::post('/uploadStep2', [QmsIpqaPsmtsController::class, 'uploadStep2'])->name('qms.ipqa.psmts.uploadStep2');
            // Route::post('/downloadVerifyIpqa', [QmsIpqaPsmtsController::class, 'stepThree'])->name('qms.ipqa.psmts.stepThree');
            // Route::post('/completeVerify', [QmsIpqaPsmtsController::class, 'stepFour'])->name('qms.ipqa.psmts.stepFour');
            // Route::post('/viewCompleteIpqa', [QmsIpqaPsmtsController::class, 'stepFive'])->name('qms.ipqa.psmts.stepFive');
            
            //Route::post('/edit', [SysUsrmUsersController::class, 'edit'])->name('sys.usrm.users.edit');
            //Route::post('/update', [SysUsrmUsersController::class, 'update'])->name('sys.usrm.users.update');
            //Route::delete('/destroy', [SysUsrmUsersController::class, 'destroy'])->name('sys.usrm.users.destroy');
            //Route::post('/activate', [SysUsrmUsersController::class,'activate'])->name('sys.usrm.users.activate');
            //Route::post('/deactivate', [SysUsrmUsersController::class,'deactivate'])->name('sys.usrm.users.deactivate');
            //Route::post('reset-password/default', [ResetPasswordController::class, 'resetPasswordDefault'])->name('sys.usrm.users.password-reset-default');

        });

        Route::prefix('/groupAssign')->group(function (){
            
            Route::get('/', [MrbLvlcGallsController::class, 'index'])->name('mrb.lvlc.galls.index');
            Route::post('/create', [MrbLvlcGallsController::class, 'create'])->name('mrb.lvlc.galls.create');
            Route::post('/store', [MrbLvlcGallsController::class, 'store'])->name('mrb.lvlc.galls.store');
            // Route::get('/noperm', [QmsIpqaTempsController::class, 'noperm'])->name('qms.ipqa.temps.noperm');

            // Route::post('/show', [QmsIpqaTempsController::class, 'show'])->name('qms.ipqa.temps.show');
            Route::post('/create', [MrbLvlcGallsController::class, 'create'])->name('mrb.lvlc.galls.create');
            Route::post('/show', [MrbLvlcGallsController::class, 'show'])->name('mrb.lvlc.galls.show');

            // // Route::post('/show/child-data', [SysUsrmGrpcsController::class, 'showChildData'])->name('sys.usrm.grpcs.show-child-data');
            // // Route::post('/create', [SysUsrmGrpcsController::class, 'create'])->name('sys.usrm.grpcs.create');
            Route::post('/edit', [MrbLvlcGallsController::class, 'edit'])->name('mrb.lvlc.galls.edit');
            Route::post('/update', [MrbLvlcGallsController::class, 'update'])->name('mrb.lvlc.galls.update');
            // Route::delete('/destroy', [QmsIpqaTempsController::class, 'destroy'])->name('qms.ipqa.temps.destroy');
            // Route::post('/activate', [QmsIpqaTempsController::class,'activate'])->name('qms.ipqa.temps.activate');
            // Route::post('/deactivate', [QmsIpqaTempsController::class,'deactivate'])->name('qms.ipqa.temps.deactivate');
            // // Route::post('/add/user', [SysUsrmGrpcsController::class, 'addUser'])->name('sys.usrm.grpcs.add-user');
            // // Route::post('/store/user', [SysUsrmGrpcsController::class, 'storeUser'])->name('sys.usrm.grpcs.store-user');
            // // Route::post('/destroy/user', [SysUsrmGrpcsController::class, 'destroyUser'])->name('sys.usrm.grpcs.destroy-user');

        });
    });
        Route::prefix('/qmsManagement')->group(function(){

            Route::prefix('/smt')->group(function (){

                Route::get('/', [QmsIpqaPsmtsController::class, 'index'])->name('qms.ipqa.psmts.index');
                Route::post('/show', [QmsIpqaPsmtsController::class, 'show'])->name('qms.ipqa.psmts.show');
                Route::post('/create', [QmsIpqaPsmtsController::class, 'create'])->name('qms.ipqa.psmts.create');
                Route::post('/getWODetails', [QmsIpqaPsmtsController::class, 'getWorkOrderDetails'])->name('qms.ipqa.psmts.getWorkOrderDetails');
                Route::post('/getCustomerWODetails', [QmsIpqaPsmtsController::class, 'getCustomerWorkOrderDetails'])->name('qms.ipqa.psmts.getCustomerWorkOrderDetails');    
                Route::post('/getTemplate', [QmsIpqaPsmtsController::class, 'getTemplate'])->name('qms.ipqa.psmts.getTemplate');
                Route::post('/store', [QmsIpqaPsmtsController::class, 'store'])->name('qms.ipqa.psmts.store');
                Route::post('/getWOWithCustomerWODetails', [QmsIpqaPsmtsController::class, 'getWOWithCustomerWODetails'])->name('qms.ipqa.psmts.getWOWithCustomerWODetails');
                Route::post('/downloadTemplate', [QmsIpqaPsmtsController::class, 'stepOne'])->name('qms.ipqa.psmts.stepOne');
                Route::post('/getStep2Modal', [QmsIpqaPsmtsController::class, 'getStep2Modal'])->name('qms.ipqa.psmts.getStep2Modal');
                Route::post('/uploadStep2', [QmsIpqaPsmtsController::class, 'uploadStep2'])->name('qms.ipqa.psmts.uploadStep2');
                Route::post('/downloadVerifyIpqa', [QmsIpqaPsmtsController::class, 'stepThree'])->name('qms.ipqa.psmts.stepThree');
                Route::post('/completeVerify', [QmsIpqaPsmtsController::class, 'stepFour'])->name('qms.ipqa.psmts.stepFour');
                Route::post('/viewCompleteIpqa', [QmsIpqaPsmtsController::class, 'stepFive'])->name('qms.ipqa.psmts.stepFive');
                
                //Route::post('/edit', [SysUsrmUsersController::class, 'edit'])->name('sys.usrm.users.edit');
                //Route::post('/update', [SysUsrmUsersController::class, 'update'])->name('sys.usrm.users.update');
                //Route::delete('/destroy', [SysUsrmUsersController::class, 'destroy'])->name('sys.usrm.users.destroy');
                //Route::post('/activate', [SysUsrmUsersController::class,'activate'])->name('sys.usrm.users.activate');
                //Route::post('/deactivate', [SysUsrmUsersController::class,'deactivate'])->name('sys.usrm.users.deactivate');
                //Route::post('reset-password/default', [ResetPasswordController::class, 'resetPasswordDefault'])->name('sys.usrm.users.password-reset-default');

            });

            Route::prefix('/template')->group(function (){
                
                Route::get('/', [QmsIpqaTempsController::class, 'index'])->name('qms.ipqa.temps.index');
                Route::get('/noperm', [QmsIpqaTempsController::class, 'noperm'])->name('qms.ipqa.temps.noperm');

                Route::post('/show', [QmsIpqaTempsController::class, 'show'])->name('qms.ipqa.temps.show');
                Route::post('/create', [QmsIpqaTempsController::class, 'create'])->name('qms.ipqa.temps.create');

                // Route::post('/show/child-data', [SysUsrmGrpcsController::class, 'showChildData'])->name('sys.usrm.grpcs.show-child-data');
                // Route::post('/create', [SysUsrmGrpcsController::class, 'create'])->name('sys.usrm.grpcs.create');
                Route::post('/store', [QmsIpqaTempsController::class, 'store'])->name('qms.ipqa.temps.store');
                // Route::post('/edit', [SysUsrmGrpcsController::class, 'edit'])->name('sys.usrm.grpcs.edit');
                // Route::post('/update', [SysUsrmGrpcsController::class, 'update'])->name('sys.usrm.grpcs.update');
                Route::delete('/destroy', [QmsIpqaTempsController::class, 'destroy'])->name('qms.ipqa.temps.destroy');
                Route::post('/activate', [QmsIpqaTempsController::class,'activate'])->name('qms.ipqa.temps.activate');
                Route::post('/deactivate', [QmsIpqaTempsController::class,'deactivate'])->name('qms.ipqa.temps.deactivate');
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

            Route::prefix('/roleControl')->group(function (){

                Route::get('/', [SysUsrmRolesController::class, 'index'])->name('sys.usrm.roles.index');
                Route::post('/show', [SysUsrmRolesController::class, 'show'])->name('sys.usrm.roles.show');
                Route::post('/create', [SysUsrmRolesController::class, 'create'])->name('sys.usrm.roles.create');
                Route::post('/store', [SysUsrmRolesController::class, 'store'])->name('sys.usrm.roles.store');
                Route::post('/edit', [SysUsrmRolesController::class, 'edit'])->name('sys.usrm.roles.edit');
                Route::post('/update', [SysUsrmRolesController::class, 'update'])->name('sys.usrm.roles.update');
                Route::delete('/destroy', [SysUsrmRolesController::class, 'destroy'])->name('sys.usrm.roles.destroy');
            });
        });

        Route::prefix('/AuthorizationManagement')->group(function () {

            Route::prefix('/permissions')->group(function () {

                Route::get('/', [PermissionController::class, 'index'])->name('rbp.permission.index');
                Route::post('/show', [PermissionController::class, 'show'])->name('rbp.permission.show');
                Route::post('/create/{mainModule}', [PermissionController::class, 'create'])->name('rbp.permission.create');
                Route::post('/store', [PermissionController::class, 'store'])->name('rbp.permission.store');
                Route::post('/edit', [PermissionController::class, 'edit'])->name('rbp.permission.edit');
                Route::post('/update', [PermissionController::class, 'update'])->name('rbp.permission.update');
            });

            Route::prefix('/role')->group(function () {

                Route::get('/', [RoleController::class, 'index'])->name('rbp.role.index');
                Route::post('/show', [RoleController::class, 'show'])->name('rbp.role.show');
                Route::post('/create', [RoleController::class, 'create'])->name('rbp.role.create');
                Route::post('/store', [RoleController::class, 'store'])->name('rbp.role.store');
                Route::post('/edit', [RoleController::class, 'edit'])->name('rbp.role.edit');
                Route::post('/update', [RoleController::class, 'update'])->name('rbp.role.update');
                Route::post('/destroy', [RoleController::class, 'destroy'])->name('rbp.role.destroy');

            });
        });

        Route::prefix('/WMSAuthorizationManagement')->group(function () {

            Route::prefix('/permissions')->group(function () {

                Route::get('/', [WmsRolePermsController::class, 'index'])->name('wms.role.perms.index');
                Route::post('/show', [WmsRolePermsController::class, 'show'])->name('wms.role.perms.show');
                Route::post('/create', [WmsRolePermsController::class, 'create'])->name('wms.role.perms.create');
                Route::post('/store', [WmsRolePermsController::class, 'store'])->name('wms.role.perms.store');
                Route::post('/edit', [WmsRolePermsController::class, 'edit'])->name('wms.role.perms.edit');
                Route::post('/update', [WmsRolePermsController::class, 'update'])->name('swms.role.perms.update');
            });

            Route::prefix('/assignPermissions')->group(function () {

                Route::get('/', [WmsRoleAsgnsController::class, 'index'])->name('wms.role.asgns.index');
                Route::post('/show', [WmsRoleAsgnsController::class, 'show'])->name('wms.role.asgns.show');
                Route::post('/create', [WmsRoleAsgnsController::class, 'create'])->name('wms.role.asgns.create');
                Route::post('/store', [WmsRoleAsgnsController::class, 'store'])->name('wms.role.asgns.store');
                Route::post('/edit', [WmsRoleAsgnsController::class, 'edit'])->name('wms.role.asgns.edit');
                Route::post('/update', [WmsRoleAsgnsController::class, 'update'])->name('wms.role.asgns.update');
                Route::post('/destroy', [WmsRoleAsgnsController::class, 'destroy'])->name('wms.role.asgns.destroy');

            });
        });

        Route::prefix('/moduleManagement')->group(function (){

            Route::prefix('/main')->group(function (){

                Route::get('/', [SysModuMainsController::class, 'index'])->name('sys.modu.mains.index');
                Route::post('/show', [SysModuMainsController::class, 'show'])->name('sys.modu.mains.show');
                Route::post('/create', [SysModuMainsController::class, 'create'])->name('sys.modu.mains.create');
                Route::post('/store', [SysModuMainsController::class, 'store'])->name('sys.modu.mains.store');
                Route::post('/edit', [SysModuMainsController::class, 'edit'])->name('sys.modu.mains.edit');
                Route::post('/update', [SysModuMainsController::class, 'update'])->name('sys.modu.mains.update');
                Route::delete('/destroy', [SysModuMainsController::class, 'destroy'])->name('sys.modu.mains.destroy');
                Route::post('/activate', [SysModuMainsController::class,'activate'])->name('sys.modu.mains.activate');
                Route::post('/deactivate', [SysModuMainsController::class,'deactivate'])->name('sys.modu.mains.deactivate');

            });

            Route::prefix('/sub')->group(function (){

                Route::get('/', [SysModuSubmsController::class, 'index'])->name('sys.modu.subms.index');
                Route::post('/show', [SysModuSubmsController::class, 'show'])->name('sys.modu.subms.show');
                Route::post('/create', [SysModuSubmsController::class, 'create'])->name('sys.modu.subms.create');
                Route::post('/store', [SysModuSubmsController::class, 'store'])->name('sys.modu.subms.store');
                Route::post('/edit', [SysModuSubmsController::class, 'edit'])->name('sys.modu.subms.edit');
                Route::post('/update', [SysModuSubmsController::class, 'update'])->name('sys.modu.subms.update');
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
                Route::post('/edit', [SysModuTabmsController::class, 'edit'])->name('sys.modu.tabms.edit');
                Route::post('/update', [SysModuTabmsController::class, 'update'])->name('sys.modu.tabms.update');
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

        Route::prefix('/item')->group(function(){

            Route::prefix('/data')->group(function (){
                Route::get('/', [WmsItemDatasController::class, 'index'])->name('wms.item.datas.index');
                Route::post('/show', [WmsItemDatasController::class, 'show'])->name('wms.item.datas.show');
                // Route::post('/show/child-data', [WmsItemDatasController::class, 'showChildData'])->name('wms.item.datas.show-child-data');
                Route::post('/create', [WmsItemDatasController::class, 'create'])->name('wms.item.datas.create');
                Route::post('/store', [WmsItemDatasController::class, 'store'])->name('wms.item.datas.store');
                Route::post('/edit', [WmsItemDatasController::class, 'edit'])->name('wms.item.datas.edit');
                Route::post('/update', [WmsItemDatasController::class, 'update'])->name('wms.item.datas.update');
                Route::delete('/destroy', [WmsItemDatasController::class, 'destroy'])->name('wms.item.datas.destroy');
            });

            Route::prefix('/group')->group(function (){
                Route::get('/', [WmsItemGrpcsController::class, 'index'])->name('wms.item.grpcs.index');
                Route::post('/show', [WmsItemGrpcsController::class, 'show'])->name('wms.item.grpcs.show');
                Route::post('/create', [WmsItemGrpcsController::class, 'create'])->name('wms.item.grpcs.create');
                Route::post('/store', [WmsItemGrpcsController::class, 'store'])->name('wms.item.grpcs.store');
                Route::post('/edit', [WmsItemGrpcsController::class, 'edit'])->name('wms.item.grpcs.edit');
                Route::post('/update', [WmsItemGrpcsController::class, 'update'])->name('wms.item.grpcs.update');
                Route::delete('/destroy', [WmsItemGrpcsController::class, 'destroy'])->name('wms.item.grpcs.destroy');
                Route::post('/show/child-data', [WmsItemGrpcsController::class, 'showChildData'])->name('wms.item.grpcs.show-child-data');
                Route::post('/create/child', [WmsItemGrpcsController::class, 'createChild'])->name('wms.item.grpcs.create-child');
                Route::post('/store/child', [WmsItemGrpcsController::class, 'storeChild'])->name('wms.item.grpcs.store-child');
                Route::post('/edit/child', [WmsItemGrpcsController::class, 'editChild'])->name('wms.item.grpcs.edit-child');
                Route::post('/update/child', [WmsItemGrpcsController::class, 'updateChild'])->name('wms.item.grpcs.update-child');
                Route::post('/destroy/child', [WmsItemGrpcsController::class, 'destroyChild'])->name('wms.item.grpcs.destroy-child');
            });
        });

        Route::prefix('/rfid')->group(function(){

            Route::prefix('/management')->group(function (){
                Route::get('/', [WmsRfidMgmtsController::class, 'index'])->name('wms.rfid.mgmts.index');
                Route::post('/show', [WmsRfidMgmtsController::class, 'show'])->name('wms.rfid.mgmts.show');
                // Route::post('/show/child-data', [WmsRfidMgmtsController::class, 'showChildData'])->name('wms.item.datas.show-child-data');
                Route::post('/create', [WmsRfidMgmtsController::class, 'create'])->name('wms.rfid.mgmts.create');
                Route::post('/store', [WmsRfidMgmtsController::class, 'store'])->name('wms.rfid.mgmts.store');
                Route::post('/edit', [WmsRfidMgmtsController::class, 'edit'])->name('wms.rfid.mgmts.edit');
                Route::post('/update', [WmsRfidMgmtsController::class, 'update'])->name('wms.rfid.mgmts.update');
                Route::delete('/destroy', [WmsRfidMgmtsController::class, 'destroy'])->name('wms.rfid.mgmts.destroy');
            });

        });

        Route::prefix('/grn')->group(function (){
            
            Route::get('/', [WmsWgrnCtrlsController::class, 'index'])->name('wms.wgrn.ctrls.index');
            Route::post('/show', [WmsWgrnCtrlsController::class, 'show'])->name('wms.wgrn.ctrls.show');
            Route::get('/create', [WmsWgrnCtrlsController::class, 'create'])->name('wms.wgrn.ctrls.create');
            Route::post('/store', [WmsWgrnCtrlsController::class, 'store'])->name('wms.wgrn.ctrls.store');
            Route::post('/edit', [WmsWgrnCtrlsController::class, 'edit'])->name('wms.wgrn.ctrls.edit');
            Route::post('/update', [WmsWgrnCtrlsController::class, 'update'])->name('wms.wgrn.ctrls.update');
            Route::delete('/destroy', [WmsWgrnCtrlsController::class, 'destroy'])->name('wms.wgrn.ctrls.destroy');
            Route::get('/view/{header}', [WmsWgrnCtrlsController::class, 'view'])->name('wms.wgrn.ctrls.view');

            Route::prefix('contents')->group(function (){
                Route::get('/show', [WmsWgrnCtrlsController::class, 'showContent'])->name('wms.wgrn.ctrls.contents.show');
                Route::post('/{header}/create', [WmsWgrnCtrlsController::class, 'createContent'])->name('wms.wgrn.ctrls.contents.create');
                Route::post('/{header}/store', [WmsWgrnCtrlsController::class, 'storeContent'])->name('wms.wgrn.ctrls.contents.store');
                Route::post('/destroy', [WmsWgrnCtrlsController::class, 'destroyContent'])->name('wms.wgrn.ctrls.contents.destroy');
            });
        });

        Route::prefix('/shipment')->group(function (){
            
            Route::get('/', [WmsShipCtrlsController::class, 'index'])->name('wms.ship.ctrls.index');
            Route::post('/show', [WmsShipCtrlsController::class, 'show'])->name('wms.ship.ctrls.show');
            Route::get('/create', [WmsShipCtrlsController::class, 'create'])->name('wms.ship.ctrls.create');
            Route::post('/store', [WmsShipCtrlsController::class, 'store'])->name('wms.ship.ctrls.store');
            Route::post('/edit', [WmsShipCtrlsController::class, 'edit'])->name('wms.ship.ctrls.edit');
            Route::post('/update', [WmsShipCtrlsController::class, 'update'])->name('wms.ship.ctrls.update');
            Route::delete('/destroy', [WmsShipCtrlsController::class, 'destroy'])->name('wms.ship.ctrls.destroy');
            Route::post('/update', [WmsShipCtrlsController::class, 'approve'])->name('wms.ship.ctrls.approve');
            Route::get('/view/{shipment}', [WmsShipCtrlsController::class, 'view'])->name('wms.ship.ctrls.view');

            Route::prefix('contents')->group(function (){
                Route::get('/show', [WmsShipCtrlsController::class, 'showContent'])->name('wms.ship.ctrls.contents.show');
                Route::post('/{header}/create', [WmsShipCtrlsController::class, 'createContent'])->name('wms.ship.ctrls.contents.create');
                Route::post('/{header}/store', [WmsShipCtrlsController::class, 'storeContent'])->name('wms.ship.ctrls.contents.store');
                Route::post('/destroy', [WmsShipCtrlsController::class, 'destroyContent'])->name('wms.ship.ctrls.contents.destroy');
            });
        });

        Route::prefix('/whmg')->group(function(){

            Route::prefix('/data')->group(function (){
                Route::get('/', [WmsWhmgWhmgsController::class, 'index'])->name('wms.whmg.whmgs.index');
                Route::post('/show', [WmsWhmgWhmgsController::class, 'show'])->name('wms.whmg.whmgs.show');
                // Route::post('/show/child-data', [WmsItemDatasController::class, 'showChildData'])->name('wms.item.datas.show-child-data');
                Route::post('/create', [WmsWhmgWhmgsController::class, 'create'])->name('wms.whmg.whmgs.create');
                Route::post('/store', [WmsWhmgWhmgsController::class, 'store'])->name('wms.whmg.whmgs.store');
                Route::post('/edit', [WmsWhmgWhmgsController::class, 'edit'])->name('wms.whmg.whmgs.edit');
                // Route::post('/update', [WmsWhmgWhmgsController::class, 'update'])->name('wms.whmg.whmgs.update');
                Route::delete('/destroy', [WmsWhmgWhmgsController::class, 'destroy'])->name('wms.whmg.whmgs.destroy');
            });

            Route::prefix('/location')->group(function (){
                Route::get('/', [WmsWhmgLoctsController::class, 'index'])->name('wms.whmg.locts.index');
                Route::post('/show', [WmsWhmgLoctsController::class, 'show'])->name('wms.whmg.locts.show');
                Route::post('/create', [WmsWhmgLoctsController::class, 'create'])->name('wms.whmg.locts.create');
                Route::post('/store', [WmsWhmgLoctsController::class, 'store'])->name('wms.whmg.locts.store');
                Route::post('/edit', [WmsWhmgLoctsController::class, 'edit'])->name('wms.whmg.locts.edit');
                Route::post('/update', [WmsWhmgLoctsController::class, 'update'])->name('wms.whmg.locts.update');
                Route::delete('/destroy', [WmsWhmgLoctsController::class, 'destroy'])->name('wms.whmg.locts.destroy');
            });

        });

        Route::prefix('/inventory')->group(function(){
            Route::prefix('/control')->group(function (){
                Route::get('/', [WmsInvtCtrlsController::class, 'index'])->name('wms.invt.ctrls.index');
                Route::post('/show', [WmsInvtCtrlsController::class, 'show'])->name('wms.invt.ctrls.show');
                Route::post('/store', [WmsInvtCtrlsController::class, 'store'])->name('wms.invt.ctrls.store');
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