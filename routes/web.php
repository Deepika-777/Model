<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\detailController;
use App\Http\Controllers\DeviceStatus;
use App\Http\Controllers\DeviceInfo;


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
//use App\Http\Controllers\DatabaseController;

//Route::get('/', [DatabaseController::class,'index']);

Route::get('/', function () {
    return view('login');
});

Route::get('/Admin', function () {
    return view('Admin.dashboard');
})->middleware(['auth']);

Route::get('/vfm-dashboard', [detailController::class,'getDataVFM']
   // return view('Admin.icons');
)->middleware(['auth']);

Route::get('/project-status', [DeviceStatus::class,'getData'] 
   // return view('Admin.status');
)->middleware(['auth']);

Route::get('/deviceinfo/{project_id}', [DeviceInfo::class,'getData']
// return view('Admin.status');
)->middleware(['auth']);

Route::get('/deviceinfo_vfm/{project_id}/{sub_project_id}', [DeviceInfo::class,'getDataVFM']
// return view('Admin.status');
)->middleware(['auth']);

Route::get('/vfm-project-status', [DeviceStatus::class,'getDataVFM'] 
   // return view('Admin.tables');
)->middleware(['auth']);

 Route::get('/dashboard', [detailController::class,'getData']
   // return view('Admin.dashboard');
)->middleware(['auth']);

Route::get('details',);

require __DIR__.'/auth.php';






