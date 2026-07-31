<?php

use Illuminate\Support\Facades\Route;
use App\Jobs\SendENewsNotificationJob; 
use App\Jobs\SendGlobalPushJob;
use Illuminate\Support\Facades\Log;

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

Route::get('admin/artisan', function () {

    Artisan::call('config:clear');
    // Artisan::call('config:cache');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return "All Clear";
});

// Login-Logout
Route::get('admin/login', 'Auth\AdminController@getLogin')->name('adminLogin');
Route::post('login', 'Auth\AdminController@postLogin')->name('adminLoginPost');
Route::get('admin/logout', 'Auth\AdminController@logout')->name('adminLogout');

Route::get('payment/cashfree-payment', 'Auth\AdminController@cashfree_payment')->name('cashfree_payment');

// Pages
Route::get('pages/{id}', 'Admin\AdminController@Page');

Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'authadmin'], function () {

    Route::group(['middleware' => 'checkadmin'], function () {

        // Type
        Route::post('type/save', 'TypeController@save')->name('typeSave');
        Route::post('type/update', 'TypeController@update')->name('typeUpdate');
        Route::get('type/delete/{id}', 'TypeController@delete')->name('deleteType');
        // Event
        Route::post('event/save', 'EventController@save')->name('eventSave');
        Route::post('event/update', 'EventController@update')->name('eventUpdate');
        Route::get('event/delete/{id}', 'EventController@delete')->name('deleteEvent');
        // Category
        Route::post('category/save', 'CategoryController@save')->name('categorySave');
        Route::post('category/update', 'CategoryController@update')->name('categoryUpdate');
        Route::get('category/delete/{id}', 'CategoryController@delete')->name('deleteCategory');
         // Ads
        Route::post('ads/save', 'AdsController@save')->name('adsSave');
        Route::post('ads/update', 'AdsController@update')->name('adsUpdate');
        Route::get('ads/delete/{id}', 'AdsController@delete')->name('deleteAds');
        // Ad Assignments
        Route::post('ad-assignments/save', 'AdAssignmentController@save')->name('adAssignmentsSave');
        Route::post('ad-assignments/update', 'AdAssignmentController@update')->name('adAssignmentsUpdate');
        Route::post('video/assign-ads/save', 'AdAssignmentController@saveVideoAds')->name('videoAdsSave');
        Route::post('livetv/assign-ads/save', 'AdAssignmentController@saveLiveTvAds')->name('livetv.ads.save');
        Route::get('ad-assignments/delete/{id}', 'AdAssignmentController@delete')->name('deleteAdAssignment');

        Route::get('ads', 'AdsController@index')->name('ads');
        Route::get('ads/data', 'AdsController@data')->name('adsData');
        Route::get('ads/add', 'AdsController@add')->name('adsAdd');
        Route::get('ads/edit/{id}', 'AdsController@edit')->name('editAds');
        // Ad Assignments
        Route::get('ad-assignments', 'AdAssignmentController@index')->name('adAssignments');
        Route::get('ad-assignments/data', 'AdAssignmentController@data')->name('adAssignmentsData');
        Route::get('ad-assignments/add', 'AdAssignmentController@add')->name('adAssignmentsAdd');
        Route::get('ad-assignments/edit/{id}', 'AdAssignmentController@edit')->name('editAdAssignment');
        // Avatar
        Route::post('avatar/save', 'AvatarController@save')->name('AvatarSave');
        Route::post('avatar/update', 'AvatarController@update')->name('AvatarUpdate');
        Route::get('avatar/delete/{id}', 'AvatarController@delete')->name('deleteAvatar');
        // Language
        Route::post('language/save', 'LanguageController@save')->name('languageSave');
        Route::post('language/update', 'LanguageController@update')->name('languageUpdate');
        Route::get('language/delete/{id}', 'LanguageController@delete')->name('deleteLanguage');
        // Session
        Route::post('session/save', 'SessionController@save')->name('sessionSave');
        Route::post('session/update', 'SessionController@update')->name('sessionUpdate');
        Route::get('session/delete/{id}', 'SessionController@delete')->name('deleteSession');
        // Page
        Route::post('page/update', 'PageController@update')->name('PageUpdate');
        // Channel
        Route::post('channel/save', 'ChannelController@save')->name('channelSave');
        Route::post('channel/update', 'ChannelController@update')->name('channelUpdate');
        Route::get('channel/delete/{id}', 'ChannelController@delete')->name('deleteChannel');
        // Channel Banner
        Route::post('Channel-Banner/save', 'ChannelBannerlController@save')->name('ChannelBannerSave');
        Route::post('Channel-Banner/update', 'ChannelBannerlController@update')->name('ChannelBannerUpdate');
        Route::get('Channel-Banner/delete/{id}', 'ChannelBannerlController@delete')->name('deleteChannelBanner');
        // User
        Route::post('user/save', 'UserController@save')->name('userSave');
        Route::post('user/update', 'UserController@update')->name('userUpdate');
        Route::get('user/delete/{id}', 'UserController@delete')->name('deleteUser');
        // Cast
        Route::post('cast/save', 'CastController@save')->name('castSave');
        Route::post('cast/update', 'CastController@update')->name('castUpdate');
        Route::get('cast/delete/{id}', 'CastController@delete')->name('deleteCast');
        // Video
        Route::post('video/save', 'VideoController@save')->name('videoSave');
        Route::post('video/update', 'VideoController@update')->name('videoUpdate');
        Route::get('video/delete/{id}', 'VideoController@delete')->name('deleteVideo');
        // Chunk route
        Route::any('saveChunk', 'VideoController@saveChunk');
        // TV Show
        Route::post('TVShow/save', 'TVShowController@save')->name('TVShowSave');
        Route::post('TVShow/update', 'TVShowController@update')->name('TVShowUpdate');
        Route::get('TVShow/delete/{id}', 'TVShowController@delete')->name('deleteTVShow');
        // TV Show Video
        Route::post('TVShow/videos/save', 'TVShowController@TVShowvideosave')->name('TVShow_video_Save');
        Route::post('TVShow/video/update/{show_id}/{id}', 'TVShowController@TVShowvideoupdate')->name('TVShow_videoUpdate');
        Route::get('TVShow/video/delete/{id}', 'TVShowController@TVShowvideodelete')->name('deleteTVShow_video');
        // Rent
        Route::post('rent/save', 'RentVideoController@save')->name('RentVideoSave');
        Route::post('rent/update', 'RentVideoController@update')->name('RentVideoUpdate');
        Route::get('rent/delete/{id}', 'RentVideoController@delete')->name('deleteRentVideo');
        // Packages (plan Subscription)
        Route::post('packages/save', 'PackageController@save')->name('packageSave');
        Route::post('packages/update', 'PackageController@update')->name('packageUpdate');
        Route::get('packages/delete/{id}', 'PackageController@delete')->name('deletePackage');
        // Payment
        Route::get('payment/edit/{id}', 'PaymentOptionController@edit')->name('editPayment');
        Route::post('payment/update', 'PaymentOptionController@update')->name('PaymentUpdate');
        
        // Notification
        Route::post('notification/save', 'NotificationController@save')->name('notificationSave');
        Route::get('notification/setting', 'NotificationController@setting')->name('notificationSetting');
        Route::post('notification/setting/add', 'NotificationController@settingsave')->name('notificationSettingsave');
        Route::get('notification/delete/{id}', 'NotificationController@delete')->name('deleteNotification');
        // Coupons
        Route::post('coupon/save', 'CouponController@save')->name('couponSave');
        Route::post('coupon/random/save', 'CouponController@randomSave')->name('couponRandomSave');
        Route::post('coupon/update', 'CouponController@update')->name('couponUpdate');
        Route::get('coupon/delete/{id}', 'CouponController@delete')->name('deleteCoupon');
        // Upcoming Video
        Route::post('upcomingvideo/save', 'UpcomingVideoController@save')->name('upcomingvideoSave');
        Route::post('upcomingvideo/update', 'UpcomingVideoController@update')->name('upcomingvideoUpdate');
        Route::get('upcomingvideo/delete/{id}', 'UpcomingVideoController@delete')->name('upcomingvideoDelete');
        // Upcoming TV Show
        Route::post('upcomingtvshow/save', 'UpcomingTVShowController@save')->name('upcomingtvshowSave');
        Route::post('upcomingtvshow/update', 'UpcomingTVShowController@update')->name('upcomingtvshowUpdate');
        Route::get('upcomingtvshow/delete/{id}', 'UpcomingTVShowController@delete')->name('upcomingtvshowDelete');
        // Upcoming TV Show Video
        Route::post('upcomingtvshow/videos/save', 'UpcomingTVShowController@TVShowvideosave')->name('upcomingtvshowvideoSave');
        Route::post('upcomingtvshow/video/update/{show_id}/{id}', 'UpcomingTVShowController@TVShowvideoupdate')->name('upcomingtvshowvideoUpdate');
        Route::get('upcomingtvshow/video/delete/{id}', 'UpcomingTVShowController@TVShowvideodelete')->name('upcomingtvshowvideoDelete');

        Route::get('web-series', 'WebSeriesController@index')->name('web-series.index'); 
        Route::get('web-series/create', 'WebSeriesController@create')->name('web-series.create'); 
        Route::post('web-series', 'WebSeriesController@store')->name('web-series.store'); 
        Route::get('web-series/edit/{id}', 'WebSeriesController@edit')->name('web-series.edit'); 
        Route::post('web-series/update', 'WebSeriesController@update')->name('web-series.update'); 
        Route::get('web-series/delete/{id}', 'WebSeriesController@destroy')->name('web-series.destroy'); 

        Route::get('web-series/reports/{id}', 'WebSeriesController@reports')->name('web-series.reports'); 
        Route::get('web-series/filter-reports', 'WebSeriesController@filterReport')->name('web-series.filterReport'); 

 
        Route::get('seasons', 'SeasonController@index')->name('seasons.index'); 
        Route::get('seasons/create', 'SeasonController@create')->name('seasons.create'); 
        Route::post('seasons', 'SeasonController@store')->name('seasons.store'); 
        Route::get('seasons/edit/{id}', 'SeasonController@edit')->name('seasons.edit'); 
        Route::post('seasons/update', 'SeasonController@update')->name('seasons.update'); 
        Route::get('seasons/delete/{id}', 'SeasonController@destroy')->name('seasons.destroy'); 


        Route::get('seasons/trailers', 'SeasonController@trailers')->name('seasons.trailers'); 
        Route::get('trailers/add', 'SeasonController@trailers_add')->name('seasons.trailers_add'); 
        Route::post('trailer/store', 'SeasonController@trailerStore')->name('trailer.store'); 
        Route::get('trailer/edit/{id}', 'SeasonController@trailerEdit')->name('trailer.edit'); 
        Route::post('trailer/update', 'SeasonController@trailerUpdate')->name('trailer.update'); 
        Route::get('trailer/delete/{id}', 'SeasonController@trailerDestroy')->name('trailer.destroy'); 

        
 
        Route::get('episodes', 'EpisodeController@index')->name('episodes.video');
        Route::get('episodes/data', 'EpisodeController@data')->name('episodes.videoData');
        Route::get('episodes/add', 'EpisodeController@add')->name('episodes.videoAdd');
        Route::post('episodes/store', 'EpisodeController@store')->name('episodes.store');
        Route::get('episodes/edit/{id}', 'EpisodeController@edit')->name('episodes.editVideo');
        Route::post('episodes/update', 'EpisodeController@update')->name('episodes.update');
        Route::get('episodes/details/{id}', 'EpisodeController@detail')->name('episodes.videoDetail'); 
        Route::get('episodes/delete/{id}', 'EpisodeController@delete')->name('episodes.delete');
        Route::get('get-seasons', 'EpisodeController@getSeasons')->name('get.seasons');  


    });
    
    Route::get('web-series/data', 'WebSeriesController@data')->name('WebSeriesData'); 
    Route::get('seasons/data', 'SeasonController@data')->name('SeasonData');
    Route::get('trailers/data', 'SeasonController@trailersData')->name('trailers.data'); 
    // Dashboard
    Route::get('dashboard', 'AdminController@dashboard')->name('dashboard');
    // Type
    Route::get('type', 'TypeController@index')->name('type');
    Route::get('type/data', 'TypeController@data')->name('typeData');
    Route::get('type/add', 'TypeController@add')->name('typeAdd');
    Route::get('type/edit/{id}', 'TypeController@edit')->name('editType');
    Route::post('type/update_sequence', 'TypeController@update_sequence')->name('updateTypeSequence');
    // Category
    Route::get('category', 'CategoryController@index')->name('category');
    Route::get('category/data', 'CategoryController@data')->name('categoryData');
    Route::get('category/add', 'CategoryController@add')->name('categoryAdd');
    Route::get('category/edit/{id}', 'CategoryController@edit')->name('editCategory');
    // Avatar
    Route::get('avatar', 'AvatarController@index')->name('Avatar');
    Route::get('avatar/data', 'AvatarController@data')->name('AvatarData');
    Route::get('avatar/add', 'AvatarController@add')->name('AvatarAdd');
    Route::get('avatar/edit/{id}', 'AvatarController@edit')->name('editAvatar');
    // Language
    Route::get('language', 'LanguageController@index')->name('language');
    Route::get('language/data', 'LanguageController@data')->name('languageData');
    Route::get('language/add', 'LanguageController@add')->name('languageAdd');
    Route::get('language/edit/{id}', 'LanguageController@edit')->name('editLanguage');
    // Session
    Route::get('session', 'SessionController@index')->name('session');
    Route::get('session/data', 'SessionController@data')->name('sessionData');
    Route::get('session/add', 'SessionController@add')->name('sessionAdd');
    Route::get('session/edit/{id}', 'SessionController@edit')->name('editSession');
    // Page
    Route::get('page', 'PageController@index')->name('Page');
    Route::get('page/data', 'PageController@data')->name('PageData');
    Route::get('page/edit/{id}', 'PageController@edit')->name('editPage');
    // Video Banner
    Route::get('banner', 'BannerController@index')->name('Banner');
    Route::post('banner/typebyvideo', 'BannerController@TypeByVideo')->name('BannerTypeByVideo');
    Route::post('banner/list', 'BannerController@BannerList')->name('BannerList');
    Route::post('banner/save', 'BannerController@save')->name('BannerSave');
    Route::post('banner/delete/{id}', 'BannerController@delete')->name('deleteBanner');
    // Video Section
    Route::get('VideoSection', 'VideoSectionController@index')->name('VideoSection');
    Route::post('VideoSection/data', 'VideoSectionController@GetSectionData')->name('GetSectionData');
    Route::get('VideoSection/get_lang_or_cat', 'VideoSectionController@get_all_data')->name('GetLangOrCat');
    Route::post('VideoSection/save', 'VideoSectionController@save')->name('VideoSectionSave');
    Route::post('VideoSection/edit', 'VideoSectionController@edit')->name('VideoSectionUpdate');
    Route::post('VideoSection/update', 'VideoSectionController@update')->name('VideoSectionUpdate1');
    Route::get('VideoSection/delete/{id}', 'VideoSectionController@delete')->name('deleteVideoSection');
	Route::post('VideoSection/update_sequence', 'VideoSectionController@update_sequence')->name('updateVideoSectionSequence');
	Route::post('VideoSection/update_video_sequence', 'VideoSectionController@update_video_sequence')->name('updateVideoSectionSequenceVideos');
    // Channel
    Route::get('channel', 'ChannelController@index')->name('channel');
    Route::get('channel/data', 'ChannelController@data')->name('channelData');
    Route::get('channel/add', 'ChannelController@add')->name('channelAdd');
    Route::get('channel/edit/{id}', 'ChannelController@edit')->name('editChannel');
    // Channel Banner
    Route::get('Channel-Banner', 'ChannelBannerlController@index')->name('ChannelBanner');
    Route::get('Channel-Banner/data', 'ChannelBannerlController@data')->name('ChannelBannerData');
    Route::get('Channel-Banner/add', 'ChannelBannerlController@add')->name('ChannelBannerAdd');
    Route::get('Channel-Banner/edit/{id}', 'ChannelBannerlController@edit')->name('editChannelBanner');
    // Channel Section
    Route::get('ChannelSection', 'ChannelSectionController@index')->name('ChannelSection');
    Route::post('ChannelSection/save', 'ChannelSectionController@save')->name('ChannelSectionSave');
    Route::post('ChannelSection/update', 'ChannelSectionController@update')->name('ChannelSectionUpdate');
    Route::post('ChannelSection/update1', 'ChannelSectionController@update1')->name('ChannelSectionUpdate1');
    Route::get('ChannelSection/delete/{id}', 'ChannelSectionController@delete')->name('deleteChannelSection');
    Route::get('ChannelSection/get_lang_or_cat', 'ChannelSectionController@get_lang_or_cat')->name('ChannelGetLangOrCat');
    Route::post('ChannelSection/data', 'ChannelSectionController@GetSectionData')->name('ChannelGetSectionData');
    // User
    Route::get('user', 'UserController@index')->name('user');
    Route::get('user/data', 'UserController@data')->name('userData');
    Route::get('user/add', 'UserController@add')->name('userAdd');
    Route::get('user/edit/{id}', 'UserController@edit')->name('editUser');
    // Cast
    Route::get('cast', 'CastController@index')->name('cast');
    Route::get('cast/data', 'CastController@data')->name('castData');
    Route::get('cast/add', 'CastController@add')->name('castAdd');
    Route::get('cast/edit/{id}', 'CastController@edit')->name('editCast');
    //Event
    //Route::get('event', 'EventController@index')->name('event');
    Route::get('event', 'EventController@index')->name('event');
    Route::get('event/data', 'EventController@data')->name('eventData');
    Route::get('event/add', 'EventController@add')->name('eventAdd');
    Route::get('event/edit/{id}', 'EventController@edit')->name('editevent');
    Route::get('event/delete/{id}', 'EventController@delete')->name('deleteEvent');
    Route::post('event/update', 'EventController@update')->name('EventUpdate');
    Route::post('event/update_sequence', 'EventController@update_sequence')->name('updateEventSequence');
    // Video
    Route::get('video', 'VideoController@index')->name('video');
    Route::get('video/data', 'VideoController@data')->name('videoData');
    Route::get('video/add', 'VideoController@add')->name('videoAdd');
    Route::get('video/edit/{id}', 'VideoController@edit')->name('editVideo');
    Route::get('video/details/{id}', 'VideoController@detail')->name('videoDetail');
     Route::get('video/assign-ads/{id}', 'AdAssignmentController@videoAds')->name('videoAssignAds');
    Route::post('search_related_videos', 'VideoController@search_related_videos')->name('search_related_videos');
    // TV Show
    Route::get('TVShow', 'TVShowController@index')->name('TVShow');
    Route::get('TVShow/data', 'TVShowController@data')->name('TVShowData');
    Route::get('TVShow/add', 'TVShowController@add')->name('TVShowAdd');
    Route::get('TVShow/edit/{id}', 'TVShowController@edit')->name('editTVShow');
    Route::get('TVShow/details/{id}', 'TVShowController@TVShowDetail')->name('TVShowDetail');
    // TV Show Video
    Route::get('TVShow/videos/{id}', 'TVShowController@TVShowvideo')->name('TVShowvideo');
    Route::get('TVShow/videos/add/{id}', 'TVShowController@TVShowvideoadd')->name('TVShowvideoAdd');
    Route::get('TVShow/video/edit/{show_id}/{id}', 'TVShowController@TVShowvideoedit')->name('editTVShow_video');
    Route::post('TVShow/video/sortable', 'TVShowController@TVShowvideosortable')->name('TVShowVideoSortable');
    // Rent
    Route::get('rent', 'RentVideoController@index')->name('RentVideo');
    Route::get('rent/data', 'RentVideoController@data')->name('RentVideoData');
    Route::get('rent/add', 'RentVideoController@add')->name('RentVideoAdd');
    Route::get('rent/edit/{id}', 'RentVideoController@edit')->name('editRentVideo');
    // Setting (Master)
    Route::get('setting', 'SettingController@index')->name('setting');
    Route::post('setting/app', 'SettingController@app')->name('settingapp');
    Route::post('setting/currency', 'SettingController@currency')->name('settingcurrency');
    Route::post('setting/imdbkey', 'SettingController@saveImdbKey')->name('settingimdbkey');
    Route::post('setting/changepassword', 'SettingController@changepassword')->name('settingchangepassword');
    Route::post('setting/admob', 'SettingController@admob_android')->name('settingadmob_android');
    Route::post('setting/admob-ios', 'SettingController@admob_ios')->name('settingadmob_ios');
    Route::post('setting/facebookad', 'SettingController@facebookad')->name('settingfacebookad');
    Route::post('setting/facebookad-ios', 'SettingController@facebookad_ios')->name('settingfacebookad_ios');
    Route::post('setting/sociallink', 'SettingController@SaveSocialLink')->name('settingSocialLink');
	Route::post('setting/subscription', 'DeveloperSettingController@subscription')->name('settingsubscription');
    // SMTP
    Route::get('setting/smtp', 'SettingController@smtpindex')->name('settingsmtpindex');
    Route::post('setting/smtp', 'SettingController@smtp')->name('settingsmtp');
    // Packages (plan Subscription)
    Route::get('packages', 'PackageController@index')->name('package');
    Route::get('packages/data', 'PackageController@data')->name('packageData');
    // Payment
    Route::get('payment', 'PaymentOptionController@index')->name('Payment');
    Route::get('payment/data', 'PaymentOptionController@data')->name('PaymentData');
    Route::get('packages/add', 'PackageController@add')->name('packageAdd');
    Route::get('packages/edit/{id}', 'PackageController@edit')->name('editPackage');
    // Notification
    Route::get('notification', 'NotificationController@index')->name('notification');
    Route::get('notification/data', 'NotificationController@data')->name('notificationData');
    Route::get('notification/add', 'NotificationController@add')->name('notificationAdd');
    // IMDB (Video)
    Route::post('serachname/{txtVal}', 'VideoController@SerachName')->name('SerachName');
    Route::post('getdata/{id}', 'VideoController@GetData')->name('GetData');
    // IMDB (TVShow)
    Route::post('tvshow/serachname/{txtVal}', 'TVShowController@SerachName')->name('TVshowSerachName');
    Route::post('tvshow/getdata/{id}', 'TVShowController@GetData')->name('TVshowGetData');
    // Transaction
    Route::get('transaction', 'TransactionController@index')->name('transaction');
    Route::get('transaction/data', 'TransactionController@data')->name('TransactionData');
    Route::get('transaction/add', 'TransactionController@add')->name('transactionAdd');
    Route::any('transaction_save', 'TransactionController@save')->name('transactionSave');
    Route::any('search_user', 'UserController@searchUser')->name('searchUser');
    // Rent Transaction
    Route::get('renttransaction', 'RentTransactionController@index')->name('RentTransaction');
    Route::get('renttransaction/data', 'RentTransactionController@data')->name('RentTransactionData');
    Route::get('renttransaction/add', 'RentTransactionController@add')->name('RenttransactionAdd');
    Route::any('renttransaction_save', 'RentTransactionController@save')->name('RenttransactionSave');
    Route::any('rentsearchuser', 'RentTransactionController@searchUser')->name('rentSearchUser');
    // Coupons
    Route::get('coupon', 'CouponController@index')->name('coupon');
    Route::get('coupon/data', 'CouponController@data')->name('couponData');
    Route::get('coupon/add', 'CouponController@add')->name('couponAdd');
    Route::get('coupon/edit/{id}', 'CouponController@edit')->name('editCoupon');
    Route::get('coupon/random/add', 'CouponController@randomAdd')->name('couponRandomAdd');
    // Upcoming Video
    Route::get('upcomingvideo', 'UpcomingVideoController@index')->name('upcomingvideo');
    Route::get('upcomingvideo/data', 'UpcomingVideoController@data')->name('upcomingvideoData');
    Route::get('upcomingvideo/add', 'UpcomingVideoController@add')->name('upcomingvideoAdd');
    Route::get('upcomingvideo/edit/{id}', 'UpcomingVideoController@edit')->name('upcomingvideoEdit');
    Route::get('upcomingvideo/details/{id}', 'UpcomingVideoController@detail')->name('upcomingvideoDetail');
    // Upcoming TV Show
    Route::get('upcomingtvshow', 'UpcomingTVShowController@index')->name('upcomingTVShow');
    Route::get('upcomingtvshow/data', 'UpcomingTVShowController@data')->name('upcomingTVShowData');
    Route::get('upcomingtvshow/add', 'UpcomingTVShowController@add')->name('upcomingtvshowAdd');
    Route::get('upcomingtvshow/edit/{id}', 'UpcomingTVShowController@edit')->name('upcomingtvshowEdit');
    Route::get('upcomingtvshow/details/{id}', 'UpcomingTVShowController@TVShowDetail')->name('upcomingTVShowDetail');
    // Upcoming TV Show Video
    Route::get('upcomingtvshow/videos/{id}', 'UpcomingTVShowController@TVShowvideo')->name('upcomingTVShowVideo');
    Route::get('upcomingtvshow/videos/add/{id}', 'UpcomingTVShowController@TVShowvideoadd')->name('upcomingtvshowvideoAdd');
    Route::get('upcomingtvshow/video/edit/{show_id}/{id}', 'UpcomingTVShowController@TVShowvideoedit')->name('upcomingtvshowvideoEdit');
	//developer setting
	Route::get('developer_setting', 'DeveloperSettingController@index')->name('developer_setting');
	Route::get('export_video_views', 'VideoController@export_video_views')->name('export_video_views');

    //e_newspaper
    Route::prefix('enews')->group(function () { 
        Route::get('/', 'ENewspaperController@index')->name('enews.index');
        Route::get('enews-data', 'ENewspaperController@enewsData')->name('enews.data'); 
        Route::get('/create', 'ENewspaperController@create')->name('enews.create'); 
        Route::post('/store', 'ENewspaperController@store')->name('enews.store');  
        Route::get('/edit/{id}', 'ENewspaperController@edit')->name('enews.edit');  
        Route::post('/update/{id}', 'ENewspaperController@update')->name('enews.update');   
        Route::get('/delete/{id}','ENewspaperController@destroy')->name('enews.delete'); 
        Route::get('/views_report','ENewspaperController@viewReads')->name('views.report'); 
        Route::get('/filter_reads','ENewspaperController@filterReport')->name('filter.report');  
    });
	
  //OptionController
    Route::prefix('options')->group(function () { 
        Route::get('/', 'OptionController@index')->name('option.index');
        Route::get('option-data', 'OptionController@optionData')->name('option.data'); 
        Route::get('/create', 'OptionController@create')->name('option.create'); 
        Route::post('/store', 'OptionController@store')->name('option.store');  
        Route::get('/edit/{id}', 'OptionController@edit')->name('option.edit');  
        Route::post('/update/{id}', 'OptionController@update')->name('option.update');   
        Route::get('/delete/{id}','OptionController@destroy')->name('option.delete');     
    });
    Route::prefix('tv_report')->group(function () { 
        Route::get('/', 'TvReportController@index')->name('enews.index');
        Route::get('enews-data', 'TvReportController@enewsData')->name('enews.data'); 
        Route::get('/create', 'TvReportController@create')->name('enews.create'); 
        Route::post('/store', 'TvReportController@store')->name('enews.store');  
        Route::get('/edit/{id}', 'TvReportController@edit')->name('enews.edit');  
        Route::post('/update/{id}', 'TvReportController@update')->name('enews.update');   
        Route::get('/delete/{id}','TvReportController@destroy')->name('enews.delete'); 
        Route::get('/tv_report','TvReportController@viewReads')->name('views.tv_report'); 
        Route::get('/filter_reads','TvReportController@filterReport')->name('tv.filter.report');  
    });
    
    Route::prefix('admin/livetv')->group(function () {

        Route::get('/', 'LiveTvController@index')->name('livetv.index');
        Route::get('/data', 'LiveTvController@liveTvData')->name('livetv.data');

        Route::get('/create', 'LiveTvController@create')->name('livetv.create');
        Route::post('/store', 'LiveTvController@store')->name('livetv.store');

        Route::get('/edit/{id}', 'LiveTvController@edit')->name('livetv.edit');
        Route::post('/update/{id}', 'LiveTvController@update')->name('livetv.update');
        Route::get('/assign-ads/{id}', 'AdAssignmentController@liveTvAds')->name('livetv.assignAds');

        Route::get('/delete/{id}', 'LiveTvController@destroy')->name('livetv.delete');

        
    });
    
    Route::get('reports/', 'LiveTvController@reports')->name('livetv.reports');
    Route::get('reports/filter-reports', 'LiveTvController@filterReport')->name('livetv.filterReport');

    Route::get('user_report', 'ReportUserController@index')->name('user.report');
    
    Route::get('user_report/data', 'ReportUserController@data')->name('userReportData');
    Route::get('export-users-report', 'ReportUserController@exportExcel')->name('export.report');
    
// Route::get('enews/run', function () {
//     $title = "Today’s Newspaper is Live";
//     $body  = "Hindi & English E-Newspapers are now available on First India Plus";
//     $formattedDate = now()->format('Y-m-d H:i:s');
 
//     SendENewsNotificationJob::dispatch(
//          $title,
//          $body,
//          [
//              'type' => 'enews',
//              'date' => $formattedDate
//         ]
//     )->onQueue('enews');

//     return 'E-News job dispatched successfully!';
// }); 

    Route::get('enews/run', function () {
        $title = "Exclusive Keynote";
        $body  = "Jagadguru Swami Rambhadracharya Ji with Mr. Pawan Arora on Dharma, culture, and India’s future.";
      
        SendGlobalPushJob::dispatch(
            $title,
            $body
        )->onQueue('enews');
        
        Log::info('SendGlobalPushJob Route');

        return 'ENews push job dispatched';
    });

    
    Route::get('/wallet', 'Reels\WalletController@edit')->name('wallet.edit');
    Route::put('/wallet', 'Reels\WalletController@update')->name('wallet.update');
    Route::resource('series', 'Reels\SeriesController');
    Route::resource('series.episodes', 'Reels\EpisodeController')->except(['index', 'show']);

     




});

