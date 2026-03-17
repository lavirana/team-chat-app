<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DirectMessageController;
use App\Http\Controllers\NotificationController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function(){

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/users/search', [UserController::class, 'search']);
    Route::patch('/users/status', [UserController::class, 'updateStatus']);
    Route::post('/users/avatar', [UserController::class, 'uploadAvatar']);

    //workspace
    Route::apiResource('workspaces', WorkspaceController::class);
    Route::post('/workspaces/{workspace}/invite', [WorkspaceController::class, 'invite']);

    //channels (nested under workspace)
    Route::prefix('workspaces/{workspace}')->group(function() {
        Route::apiResource('channels', ChannelController::class);
        Route::post('/channels/{channel}/join',  [ChannelController::class, 'join']);
        Route::post('/channels/{channel}/leave', [ChannelController::class, 'leave']);
    });

     //direct messages
     Route::get('/dm/{user}',  [DirectMessageController::class, 'index']);
     Route::post('/dm/{user}', [DirectMessageController::class, 'store']);

     // Messages
    Route::get('/channels/{channel}/messages',       [MessageController::class, 'index']);
    Route::post('/channels/{channel}/messages',      [MessageController::class, 'store']);
    Route::patch('/messages/{message}',              [MessageController::class, 'update']);
    Route::delete('/messages/{message}',             [MessageController::class, 'destroy']);
    Route::post('/messages/{message}/react',         [MessageController::class, 'react']);

    // Direct Messages
    Route::get('/dm/{user}',    [DirectMessageController::class, 'index']);
    Route::post('/dm/{user}',   [DirectMessageController::class, 'store']);

    // Notifications
    Route::get('/notifications',           [NotificationController::class, 'index']);
    Route::patch('/notifications/read-all',[NotificationController::class, 'markAllRead']);
    Route::patch('/notifications/{id}',    [NotificationController::class, 'markRead']);

    });




/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/
