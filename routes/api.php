<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ClientController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('all-roles', [UserController::class, 'allUserRoles']);

Route::controller(AuthController::class)->prefix('user')->group(function () {
    Route::post('register', 'registerUser')->name('register');
    Route::post('login', 'loginUser')->name('login');
    Route::get('logout', 'logoutUser')->middleware('auth:sanctum');
    Route::post('forgot-password', 'forgotPass');
    Route::post('reset-password', 'resetPass');
});

Route::middleware(['auth:sanctum', 'user.status'])->group(function (){
    // View User Profile and Edit Profile
    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::get('me', 'viewMe');
        Route::patch('edit', 'updateMe');
    });

    // View projects with searchable fields
    Route::controller(ProjectController::class)->prefix('project')->group(function () {
        Route::get('/', 'viewAllProjects');
    });

    // User log/activity 
    Route::controller(TimeLogController::class)->prefix('log')->group(function () {
        Route::post('/', 'addActivity');
        Route::get('{id}', 'viewLogs');
        Route::patch('{id}', 'editActivity');
        Route::delete('{id}', 'removeActivity');
    });

    // Is the user admin?
    Route::middleware(['isAdmin'])->group(function (){
        Route::controller(AdminController::class)->prefix('admin')->group(function () {
            Route::get('users', 'viewAllUsers');
            Route::post('change-roles', 'assignRoles');
            Route::get('user-roles/{id}', 'viewUserRole');
            Route::delete('user/{id}', 'deleteUser');
            Route::post('invite', 'inviteOthers');
            Route::patch('user-status/{id}', 'updateUserStatus');
        });
        Route::controller(InviteController::class)->prefix('invite')->group(function () {
            Route::get('invited-users', 'listInvitedUsers');
            Route::post('resend', 'reInvite');
            Route::delete('revoke/{id}', 'revoke');
        });
        Route::controller(ProjectController::class)->prefix('project')->group(function () {
            Route::post('/', 'addActivity');
            Route::patch('{id}', 'updateActivity');
            Route::patch('project-status/{id}', 'updateProjectStatus');
            Route::delete('{id}', 'deleteProject');
            
        });
        Route::controller(ClientController::class)->prefix('client')->group(function () {
            Route::get('/', 'viewAllClients');
            Route::post('/', 'addClient');
            Route::patch('{id}', 'updateClient');
            Route::delete('{id}', 'deleteClient');
            Route::patch('client-status/{id}', 'updateClientStatus');
        });
        Route::middleware(['project.status'])->group(function () {
                Route::controller(ProjectController::class)->prefix('project')->group(function (){
                    Route::patch('billable-status/{id}', 'updateBillableStatus');
            });
        });
    });
});
