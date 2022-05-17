<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageUploadController;
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

Route::get('send-mail', function () {
    $to_name = "RECEIVER_NAME";
    $to_email = "nikultaka@gmail.com";
    $data = array("name"=>"Cloudways (sender_name)", "body" => "A test mail");

    Mail::send([], $data, function($message) use ($to_name, $to_email) {
    $message->to($to_email, $to_name)
    ->subject("Laravel Test Mail");
    $message->from("no-reply@easytap.co","Test Mail");
    });
});

Route::get('/', function () {
    return view('welcome');
});


Route::get('qrcode-with-image', function () {
         $image = \QrCode::format('png')
                         ->merge('https://w3adda.com/wp-content/uploads/2019/07/laravel.png', 0.5, true)
                         ->size(500)->errorCorrection('H')
                         ->generate('A simple example of QR code!');
      return response($image)->header('Content-type','image/png');
 });

Auth::routes();

Route::get('/firebase', [App\Http\Controllers\HomeController::class, 'firebase'])->name('firebase');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/logout-admin', [App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('logout-admin');


Route::group(['prefix' => ADMIN], function () {
    Route::get('/', [App\Http\Controllers\Admin\Auth\LoginController::class, 'index']);
    Route::get('/login', [App\Http\Controllers\Admin\Auth\LoginController::class, 'index'])->name('admin-login');
    Route::post('/login/proccess', [App\Http\Controllers\Admin\Auth\LoginController::class, 'loginProccess'])->name('admin-login-proccess');


    Route::group(['middleware' => ['auth:admin']], function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin-dashboard');

        // manage user
        Route::get('/manage/users', [App\Http\Controllers\Admin\ManageUsersController::class, 'index'])->name('admin-manage-users');
        Route::post('/manage/users/save', [App\Http\Controllers\Admin\ManageUsersController::class, 'add'])->name('admin-manage-users-save');
        Route::post('/manage/users/dataTable', [App\Http\Controllers\Admin\ManageUsersController::class, 'datatable'])->name('admin-manage-users-datatable');
        Route::post('/manage/users/edit', [App\Http\Controllers\Admin\ManageUsersController::class, 'edit'])->name('admin-manage-users-edit');
        Route::post('/manage/users/delete', [App\Http\Controllers\Admin\ManageUsersController::class, 'delete'])->name('admin-manage-users-delete');
        Route::get('/email/exist/or/not', [App\Http\Controllers\Admin\ManageUsersController::class, 'emailExistOrNot'])->name('admin-email-exist-or-not');
        Route::post('/manage/users/blockuser', [App\Http\Controllers\Admin\ManageUsersController::class, 'blockuser'])->name('admin-manage-users-blockuser');
        Route::post('/manage/users/resetPassword', [App\Http\Controllers\Admin\ManageUsersController::class, 'resetPassword'])->name('admin-manage-users-resetPassword');

        // role
        Route::get('/role', [App\Http\Controllers\Admin\RoleController::class, 'index'])->name('admin-role');
        Route::post('/role/add', [App\Http\Controllers\Admin\RoleController::class, 'add'])->name('admin-role-add');
        Route::post('/role/datatable', [App\Http\Controllers\Admin\RoleController::class, 'datatable'])->name('admin-role-datatable');
        Route::post('/role/delete', [App\Http\Controllers\Admin\RoleController::class, 'delete'])->name('admin-role-delete');
        Route::post('/role/edit', [App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('admin-role-edit');

    });
});

Route::get('/{username}', [App\Http\Controllers\Admin\ManageUsersController::class, 'username'])->name('username'); 