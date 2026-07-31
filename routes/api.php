<?php

use App\Http\Controllers\Api\ChannelController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuditionController;
use App\Http\Controllers\Api\EpisodeController;
use App\Http\Controllers\Api\WebSeriesController;
use App\Http\Controllers\Api\ReactionController;
use App\Http\Controllers\Api\Reels\FeedController;
use App\Http\Controllers\Api\Reels\SearchController;
use App\Http\Controllers\Api\Reels\WalletController;
use App\Http\Controllers\Api\Reels\SeriesController;
use App\Http\Controllers\Api\Reels\WatchProgressController;
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

// ---------------- HomeController ----------------
Route::post('get_language', [HomeController::class, 'get_language']);
Route::post('cast_detail', [HomeController::class, 'cast_detail']);
Route::post('get_category', [HomeController::class, 'get_category']);
Route::post('get_banner', [HomeController::class, 'get_banner']);
Route::post('general_setting', [HomeController::class, 'general_setting']);
Route::post('get_type', [HomeController::class, 'get_type']);
Route::post('get_avatar', [HomeController::class, 'get_avatar']);
Route::post('section_list', [HomeController::class, 'section_list']);
Route::post('section_list_new', [HomeController::class, 'section_list_new']);
Route::post('section_detail', [HomeController::class, 'section_detail']);
Route::post('add_continue_watching', [HomeController::class, 'add_continue_watching']);
Route::post('remove_continue_watching', [HomeController::class, 'remove_continue_watching']);
Route::post('add_remove_bookmark', [HomeController::class, 'add_remove_bookmark']);
Route::post('add_remove_download', [HomeController::class, 'add_remove_download']);
Route::post('add_transaction', [HomeController::class, 'add_transaction']);
Route::post('add_rent_transaction', [HomeController::class, 'add_rent_transaction']);
Route::post('video_by_category', [HomeController::class, 'video_by_category']);
Route::post('video_by_language', [HomeController::class, 'video_by_language']);
Route::post('get_bookmark_video', [HomeController::class, 'get_bookmark_video']);
Route::post('search_video', [HomeController::class, 'search_video']);
Route::post('user_rent_video_list', [HomeController::class, 'user_rent_video_list']);
Route::post('rent_video_list', [HomeController::class, 'rent_video_list']);
Route::post('get_payment_option', [HomeController::class, 'get_payment_option']);
Route::post('get_video_by_session_id', [HomeController::class, 'get_video_by_session_id']);
Route::post('get_package', [HomeController::class, 'get_package']);
Route::post('get_payment_token', [HomeController::class, 'get_payment_token']);
Route::post('apply_coupon', [HomeController::class, 'apply_coupon']);
Route::post('subscription_list', [HomeController::class, 'subscription_list']);
Route::post('get_pages', [HomeController::class, 'get_pages']);
Route::post('video_view', [HomeController::class, 'video_view']);
Route::post('get_social_link', [HomeController::class, 'get_social_link']);
Route::get('get_popup_banner', [HomeController::class, 'popupBannerDetails']);
Route::get('whats_up_button_status', [HomeController::class, 'whatsupButtonStatus']);
Route::get('home_banner_details', [HomeController::class, 'homeBannerDetails']);
Route::post('enews_list', [HomeController::class, 'newsList']);
Route::post('enews/read', [HomeController::class, 'eNewsMarkRead']);
Route::post('enews/visit', [HomeController::class, 'markVisit']); 
Route::get('get_enews_banner', [HomeController::class, 'enewsBannerDetails']);

// ---------------- ChannelController ----------------
Route::post('get_channel', [ChannelController::class, 'get_channel']);
Route::post('channel_section_list', [ChannelController::class, 'channel_section_list']);

// ---------------- UsersController ----------------
Route::post('login', [UserController::class, 'login']);
Route::post('registration', [UserController::class, 'registration']);
Route::post('get_profile', [UserController::class, 'get_profile']);
Route::post('update_profile', [UserController::class, 'update_profile']);
Route::post('image_upload', [UserController::class, 'image_upload']);
Route::post('get_tv_login_code', [UserController::class, 'get_tv_login_code']);
Route::post('tv_login', [UserController::class, 'tv_login']);
Route::post('create_order', [UserController::class, 'create_order']);
//Route::post('create_razorpay_order', [UserController::class, 'create_razorpay_order']);
Route::post('payment_options', [UserController::class, 'payment_options']);
Route::post('get_order_details', [UserController::class, 'getOrderDetails']);
Route::post('update_order_status', [UserController::class, 'update_order_status']);
Route::get('subscription_current_status', [UserController::class, 'subscriptionCurrentStatus']);
Route::get('get_additional_banner', [UserController::class, 'getAdditionalBanner']);
Route::get('get_user_info', [UserController::class, 'get_user_info']);
//Route::get('update_razorpay_order_status', [UserController::class, 'update_razorpay_order_status']);

//Audition
Route::get('get_country_list', [AuditionController::class, 'getCountryList']);
Route::get('get_state_list/{id}', [AuditionController::class, 'getStateList']);
Route::get('get_city_list/{id}', [AuditionController::class, 'getCityList']);
Route::get('get_audition_list/{type?}', [AuditionController::class, 'getAuditionList']);
Route::post('audition_application', [AuditionController::class, 'auditionApplication']);
Route::post('update_audition_application', [AuditionController::class, 'updateAuditionApplication']);
Route::post('audition_application_voting', [AuditionController::class, 'auditionApplicationVoting']);
Route::post('get_application_votings', [AuditionController::class, 'getApplicationVotings']);
Route::get('get_audition_application_details/{user_id}/{id?}', [AuditionController::class, 'getAuditionApplicationDetail']);
Route::get('get_all_audition_applications/{season_id}/{audition_id}/{ref_id}', [AuditionController::class, 'getAllAuditionApplications']);
Route::get('check_audition_application/{user_id}/{audition_id}', [AuditionController::class, 'checkAuditionApplication']);
Route::get('tester', [UserController::class, 'tester']);
Route::get('audition_tester', [AuditionController::class, 'audition_tester']);
Route::get('get_event_info', [AuditionController::class, 'get_event_info']);
Route::post('book_ticket_grand_finale', [AuditionController::class, 'book_ticket_grand_finale']);
Route::post('update_ticket_grand_finale', [AuditionController::class, 'update_ticket_grand_finale']);
Route::post('update_ticket_grand_finale', [AuditionController::class, 'update_ticket_grand_finale']);
Route::post('send_otp', [UserController::class, 'send_otp']);
Route::post('verify_otp', [UserController::class, 'verify_otp']);

Route::post('user_coupan_code', [UserController::class, 'CoupanCode']);
Route::post('customNotify', [UserController::class, 'customNotify']);

Route::post('tv/views', [HomeController::class, 'tvViews']);

Route::post("webseries", [WebSeriesController::class, "webserieslist"]);
Route::post("webseries-detail", [WebSeriesController::class, "detail"]);
Route::post("webseries/seasons", [WebSeriesController::class, "seasons"]);

Route::post("seasons/episodes", [EpisodeController::class, "list"]);
Route::post("v1/seasons/episodes", [EpisodeController::class, "listV"]);

Route::post("episodes", [EpisodeController::class, "detail"]);

Route::post("v1/episodes", [EpisodeController::class, "detailV"]);
Route::post("seasons/trailer", [EpisodeController::class, "TrailerList"]);

Route::post("episode/react", [ReactionController::class, "episodeReact"]);
Route::post("episode/wishlist", [ReactionController::class, "episodeWishlist"]);

Route::post("webseries/react", [ReactionController::class, "webseriesReact"]);
Route::post("webseries/wishlist", [ReactionController::class, "webseriesWishlist"]);

Route::post("mylist", [UserController::class, "myList"]);

Route::get("search", [WebSeriesController::class, "search"]);
 
Route::post("episode/watch-progress", [EpisodeController::class, "watchProgress"]);


Route::get('option-data', [HomeController::class, 'optionData']);
Route::get('live-tv-urls', [HomeController::class, 'liveTvUrls']);

Route::post('get-live-tv-urls', [HomeController::class, 'getLiveTvUrls']);
 
Route::post('/feed', 'Api\Reels\FeedController@index'); 
Route::post('/search', 'Api\Reels\SearchController@index');
Route::post('/wallet/packages', 'Api\Reels\WalletController@packages');
Route::post('/wallet/razorpay/order', 'Api\Reels\WalletController@createRazorpayOrder');
Route::post('/wallet/razorpay/verify', 'Api\Reels\WalletController@verifyRazorpayRecharge');
Route::post('/wallet/recharge', 'Api\Reels\WalletController@recharge');
Route::post('/wallet/balance', 'Api\Reels\WalletController@balance');
Route::post('/wallet/history', 'Api\Reels\WalletController@history');
Route::post('/wallet/unlock-reel', 'Api\Reels\WalletController@unlockReel');
Route::post('/series', 'Api\Reels\SeriesController@index');
Route::post('/series/{series}', 'Api\Reels\SeriesController@show');
Route::post('/series/{series}/pricing', 'Api\Reels\SeriesController@pricing');
Route::post('/series/{series}/episodes', 'Api\Reels\SeriesController@episodes');
Route::post('/episodes/{episode}/like', 'Api\Reels\FeedController@like');
Route::post('/watch-progress', 'Api\Reels\WatchProgressController@store');











