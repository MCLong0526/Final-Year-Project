<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\OrderServiceController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/', 'index');
        Route::get('/get-following', 'getFollowing');
        Route::get('/get-user-details/{user_id}', 'getUserDetails');
        Route::post('/check-password', 'checkPassword');
        Route::post('/store', 'store');
        Route::post('/follow-user/{user_id}', 'followUser');
        Route::post('/unfollow-user/{user_id}', 'unfollowUser');
        Route::delete('/delete/{user_id}', 'destroy');
        Route::put('/update/{user_id}', 'update');
        Route::put('/update-profile/{user_id}', 'editProfile');
        Route::put('/update-password', 'editPassword');
        Route::put('/update-avatar/{user_id}', 'editAvatar');

    });

    Route::controller(RoleController::class)->prefix('roles')->group(function () {
        Route::get('/', 'index');

    });

    Route::controller(AuthController::class)->prefix('auth')->group(function () {
        Route::get('/get-user', 'getCurrentLoggedUser');
        Route::get('/get-user-by-token', 'getUserByToken');
        Route::post('/logout', 'logout');
    });

    Route::controller(ItemController::class)->prefix('items')->group(function () {
        Route::get('/get-all-items', 'index');
        Route::get('/get-auth-items', 'getAuthItems');
        Route::put('/update/{item_id}', 'update');
        Route::put('/update-status/{item_id}', 'updateStatus');
        Route::put('/update-statusinfo/{item_id}', 'updateStatusInfo');
        Route::post('/store', 'store');
        Route::post('/save-item-pictures/{item_id}', 'saveItemPictures');
        Route::delete('/delete/{item_id}', 'destroy');
    });
    Route::controller(OrderItemController::class)->prefix('order-items')->group(function () {
        Route::post('/store', 'store');
        Route::post('/rate-seller', 'rateSeller');
        Route::get('/get-pending-sell-orders', 'getPendingSellOrders');
        Route::get('/get-confirmed-sell-orders', 'getConfirmedSellOrders');
        Route::get('/get-buy-orders', 'getBuyOrders');
        Route::get('/count-pending-orders', 'countPendingOrders');
        Route::get('/get-purchases-orders', 'getPurchasesOrders');
        Route::put('/confirmed-order/{item_user_id}', 'confirmedOrder');
        Route::put('/cancel-pending-order/{item_user_id}', 'cancelPendingOrder');
        Route::get('/count-auth-purchases', 'countAuthPurchases');
        Route::get('/get-order-persons/{item_id}', 'getOrderPersons');
    });
    Route::controller(OrderServiceController::class)->prefix('order-services')->group(function () {

        Route::get('/get-pending-sell-orders', 'getPendingSellOrders');
        Route::get('/get-confirmed-sell-orders', 'getConfirmedSellOrders');
        Route::get('/get-purchases-orders', 'getPurchasesOrders');
        Route::get('/count-auth-purchases', 'countAuthPurchases');
        Route::get('/count-pending-orders', 'countPendingOrders');
        Route::put('/cancel-pending-order/{item_user_id}', 'cancelPendingOrder');
        Route::put('/confirmed-order/{service_user_id}', 'confirmedOrder');
        Route::post('/store', 'store');
        Route::post('/rate-provider', 'rateProvider');

    });
    Route::controller(NotificationController::class)->prefix('notifications')->group(function () {
        Route::get('/get-auth-notifications', 'getAuthNotifications');
        Route::put('/mark-as-read/{notification_id}', 'markAsRead');
        Route::put('/mark-all-read/{user_id}', 'markAllRead');
    });
    Route::controller(PostController::class)->prefix('posts')->group(function () {
        Route::post('/store', 'store');
        Route::get('/', 'index');
        Route::get('/get-auth-posts', 'getAuthPosts');
        Route::get('/get-post-by-id/{post_id}', 'getPostById');
        Route::put('/update/{post_id}', 'update');
        Route::post('/like/{post_id}', 'likePost');
        Route::post('/unlike/{post_id}', 'unlikePost');
        Route::delete('/delete/{post_id}', 'destroy');

    });
    Route::controller(CommentController::class)->prefix('comments')->group(function () {
        Route::post('/store', 'store');
        Route::get('/get-comments-by-post-id/{post_id}', 'getCommentsByPostId');

    });
    Route::controller(AdminDashboardController::class)->prefix('admin-dashboard')->group(function () {
        Route::get('/get-number-of-users', 'getNumberOfUsers');
        Route::get('/get-number-of-items', 'getNumberOfItems');
        Route::get('/get-number-of-posts', 'getNumberOfPosts');
        Route::get('/get-number-of-services', 'getNumberOfServices');
        Route::get('/get-status-users', 'getStatusUsers');
        Route::get('/get-all-percentage-type-items', 'getAllPercentageTypeItems');
        Route::get('/get-all-percentage-type-services', 'getAllPercentageTypeServices');

    });
    Route::controller(DashboardController::class)->prefix('dashboard')->group(function () {
        Route::get('/get-schedules', 'getSchedules');
        Route::get('/get-auth-earned', 'getAuthEarned');
        Route::get('/get-number-of-items', 'getNumberOfItems');
        Route::get('/get-number-of-posts', 'getNumberOfPosts');
        Route::get('/get-number-of-services', 'getNumberOfServices');
        Route::get('/get-number-of-likes', 'getNumberOfLikes');
        Route::get('/get-weekly-earned', 'getWeeklyEarned');
        Route::get('/get-monthly-earned', 'getMonthlyEarned');
    });
    Route::controller(ServiceController::class)->prefix('services')->group(function () {
        Route::get('/get-all-services', 'index');
        Route::get('/get-auth-services', 'getAuthServices');
        Route::post('/store', 'store');
        Route::delete('/delete/{service_id}', 'destroy');
        Route::put('/update/{service_id}', 'update');
        Route::put('/update-status/{service_id}', 'updateStatus');

    });

    Route::controller(ChatsController::class)->prefix('chat')->group(function () {
        Route::get('/messages', 'fetchMessages');

        Route::post('/messages', 'sendMessage');
    });

    Route::controller(CalendarController::class)->prefix('calendar')->group(function () {
        Route::get('/upcoming-meetup-seller', 'upComingMeetupSeller');
        Route::get('/upcoming-meetup-buyer', 'upComingMeetupBuyer');
    });

});

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/authenticate', 'authenticate');
    Route::post('/forgot-password', 'forgotPassword');
    Route::post('/verify-code', 'verifyCode');
    Route::post('/check-verification-code', 'checkVerificationCode');
    Route::post('/register', 'register');
    Route::get('/check-email-exists', 'checkEmailExists');
    Route::post('/register-seller', 'registerSeller');

});
