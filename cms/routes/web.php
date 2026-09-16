<?php

use App\Http\Controllers\BadgeImagingController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HabbletController;
use App\Http\Controllers\HomesController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\HousekeepingAssetController;
use App\Http\Controllers\HousekeepingController;
use App\Http\Controllers\HousekeepingToolsController;
use App\Http\Controllers\HousekeepingUsersController;
use App\Http\Controllers\MinimailController;
use App\Http\Controllers\WebGalleryController;
use Illuminate\Support\Facades\Route;

Route::get('/web-gallery/{path}', WebGalleryController::class)
    ->where('path', '.*')
    ->name('web-gallery');

Route::middleware('hotel.guest')->group(function () {
    Route::get('/', [HotelController::class, 'landing'])->name('hotel.landing');
    Route::post('/account/submit', [HotelController::class, 'login'])->middleware('throttle:8,1');
    Route::get('/account/password/forgot', [HotelController::class, 'forgot']);
    Route::post('/account/password/forgot', [HotelController::class, 'forgotSubmit'])->middleware('throttle:5,1');
    Route::get('/forgot', [HotelController::class, 'forgot']);
    Route::get('/register', [HotelController::class, 'register']);
    Route::post('/register', [HotelController::class, 'registerSubmit'])->middleware('throttle:8,1');
    Route::get('/register/cancel', fn () => redirect('/'));
    Route::get('/login_popup', [HotelController::class, 'loginPopup']);
    Route::get('/account/popup', [HotelController::class, 'loginPopup']);
});

Route::get('/account/logout', [HotelController::class, 'logout']);
Route::get('/logout', [HotelController::class, 'logout']);
Route::get('/articles/rss.xml', [HotelController::class, 'rss']);
Route::get('/account/unloadclient', [HotelController::class, 'unloadClient']);
Route::get('/cacheCheck', [HotelController::class, 'cacheCheck']);
Route::any('/clientlog/{path?}', [HotelController::class, 'clientLog'])->where('path', '.*');
Route::get('/email', [HotelController::class, 'emailVerify']);
Route::get('/security_check', [HotelController::class, 'securityCheck'])->middleware('hotel.auth');
Route::get('/security_check_token', [HotelController::class, 'securityCheck']);
Route::get('/intermediate', [HotelController::class, 'intermediate']);
Route::get('/tryout', [HotelController::class, 'tryout'])->middleware('hotel.optional');
Route::get('/credits/club/tryout', [HotelController::class, 'tryout'])->middleware('hotel.optional');
Route::get('/client_popup/{key}', [HotelController::class, 'clientUtils'])->middleware('hotel.optional');
Route::get('/client_{key}', [HotelController::class, 'clientUtils'])->middleware('hotel.optional')->where('key', 'install_shockwave|upgrade_shockwave|error|connection_failed');
Route::get('/account/reauthenticate', [HotelController::class, 'reauthenticate'])->middleware('hotel.auth');
Route::post('/account/reauthenticate', [HotelController::class, 'reauthenticateSubmit'])->middleware(['hotel.auth', 'throttle:8,1']);
Route::get('/reauthenticate', [HotelController::class, 'reauthenticate'])->middleware('hotel.auth');

Route::middleware('hotel.optional')->group(function () {
    Route::get('/community', [HotelController::class, 'community']);
    Route::get('/articles/{article}', [HotelController::class, 'articles'])->where('article', '[0-9]+-.*');
    Route::get('/articles', [HotelController::class, 'articles']);
    Route::get('/credits', [HotelController::class, 'credits']);
    Route::get('/club', [HotelController::class, 'club']);
    Route::get('/credits/club/{page?}', [HotelController::class, 'club']);
    Route::get('/credits/collectables', [HotelController::class, 'collectables']);
    Route::get('/collectables.php', [HotelController::class, 'collectables']);
    Route::get('/credits/pixels', [HotelController::class, 'pixels']);
    Route::get('/pixels', [HotelController::class, 'pixels']);
    Route::get('/tag/{tag?}', [HotelController::class, 'tag']);
    Route::get('/tag', [HotelController::class, 'tag']);
    Route::get('/papers/{page?}', [HotelController::class, 'papers']);
    Route::get('/help/faqsearch', [HotelController::class, 'helpSearch']);
    Route::post('/help/faqsearch', [HotelController::class, 'helpSearch']);
    Route::get('/help/{id?}', [HotelController::class, 'help']);
    Route::get('/help', [HotelController::class, 'help']);
    Route::get('/iot/go', [HotelController::class, 'iot']);
    Route::post('/iot/go', [HotelController::class, 'iotSubmit'])->middleware('throttle:8,1');
    Route::get('/articles/archive', [HotelController::class, 'articles']);
    Route::get('/articles/category/{category}', [HotelController::class, 'articles']);
    Route::get('/home/{id}/id', [HotelController::class, 'homeById'])->whereNumber('id');
    Route::get('/home/{name}', [HotelController::class, 'home'])->where('name', '[^/]+');
    Route::get('/groups/{id}/id/discussions/{thread}/id/page/{page}', [GroupController::class, 'discussions'])->whereNumber('id')->whereNumber('thread')->whereNumber('page');
    Route::get('/groups/{id}/id/discussions/page/{page}', [GroupController::class, 'discussions'])->whereNumber('id')->whereNumber('page');
    Route::get('/groups/{id}/id/discussions/{thread}/id', [GroupController::class, 'discussions'])->whereNumber('id')->whereNumber('thread');
    Route::get('/groups/{id}/id/discussions', [GroupController::class, 'discussions'])->whereNumber('id');
    Route::get('/groups/{id}/id', [GroupController::class, 'show'])->whereNumber('id');
    Route::get('/groups/actions/join', [GroupController::class, 'join']);
    Route::get('/groups/actions/leave', [GroupController::class, 'leave']);
    Route::post('/groups/actions/join', [GroupController::class, 'join']);
    Route::post('/groups/actions/leave', [GroupController::class, 'leave']);
    Route::match(['get', 'post'], '/groups/actions/group_settings', [GroupController::class, 'settings']);
    Route::post('/groups/actions/update_group_settings', [GroupController::class, 'updateSettings']);
    Route::match(['get', 'post'], '/groups/actions/show_badge_editor/{id?}', [GroupController::class, 'badgeEditor']);
    Route::post('/groups/actions/update_group_badge', [GroupController::class, 'updateBadge']);
    Route::post('/groups/actions/check_group_url', [GroupController::class, 'checkUrl']);
    Route::match(['get', 'post'], '/groups/actions/confirm_leave', [GroupController::class, 'confirmLeave']);
    Route::match(['get', 'post'], '/groups/actions/confirm_delete_group', [GroupController::class, 'confirmDelete']);
    Route::post('/groups/actions/delete_group', [GroupController::class, 'deleteGroup']);
    Route::match(['get', 'post'], '/groups/actions/confirm_select_favorite', [GroupController::class, 'confirmFavorite']);
    Route::match(['get', 'post'], '/groups/actions/confirm_deselect_favorite', [GroupController::class, 'confirmDeselectFavorite']);
    Route::post('/groups/actions/select_favorite', [GroupController::class, 'selectFavorite']);
    Route::post('/groups/actions/deselect_favorite', [GroupController::class, 'selectFavorite']);
    Route::get('/groups/{alias}', [GroupController::class, 'showAlias'])->where('alias', '[A-Za-z0-9_-]+');
});

Route::middleware('hotel.auth')->group(function () {
    Route::get('/me', [HotelController::class, 'me']);
    Route::get('/welcome', [HotelController::class, 'welcome']);
    Route::get('/profile', [HotelController::class, 'profile']);
    Route::post('/profile', [HotelController::class, 'updateProfile']);
    Route::get('/client', [HotelController::class, 'client']);
    Route::get('/credits/history', [HotelController::class, 'history']);
    Route::get('/quickmenu/{key}', [HabbletController::class, 'quickmenu']);
});

Route::post('/habblet/ajax/namecheck', [HabbletController::class, 'namecheck'])->middleware('throttle:20,1');
Route::post('/habblet/ajax/emailcheck', [HabbletController::class, 'emailcheck'])->middleware('throttle:20,1');
Route::post('/habblet/ajax/password', [HabbletController::class, 'passwordcheck'])->middleware('throttle:20,1');
Route::post('/habblet/ajax/updatemotto', [HabbletController::class, 'updateMotto'])->middleware(['hotel.auth', 'throttle:20,1']);
Route::match(['get', 'post'], '/habblet/ajax/redeemvoucher', [HabbletController::class, 'redeemVoucher'])->middleware('hotel.auth');
Route::post('/habblet/ajax/collectiblesConfirm', [HabbletController::class, 'collectiblesConfirm'])->middleware('hotel.auth');
Route::post('/habblet/ajax/collectiblesPurchase', [HabbletController::class, 'collectiblesPurchase'])->middleware('hotel.auth');
Route::post('/habblet/ajax/confirmAddFriend', [HabbletController::class, 'confirmAddFriend'])->middleware('hotel.auth');
Route::post('/habblet/ajax/addFriend', [HabbletController::class, 'addFriend'])->middleware('hotel.auth');
Route::post('/habblet/ajax/load_events', [HabbletController::class, 'loadEvents'])->middleware('hotel.auth');
Route::match(['get', 'post'], '/habblet/ajax/habboclub_enddate', [HabbletController::class, 'clubEnddate'])->middleware('hotel.auth');
Route::match(['get', 'post'], '/habblet/ajax/habboclub_gift', [HabbletController::class, 'clubGift'])->middleware('hotel.auth');
Route::match(['get', 'post'], '/habblet/ajax/tagsearch', [HabbletController::class, 'tagSearch'])->middleware('hotel.optional');
Route::post('/habblet/ajax/tagmatch', [HabbletController::class, 'tagMatch'])->middleware('hotel.auth');
Route::post('/habblet/ajax/tagfight', [HabbletController::class, 'tagFight'])->middleware('hotel.optional');
Route::match(['get', 'post'], '/habblet/ajax/mgmgetinvitelink', [HabbletController::class, 'inviteLink'])->middleware('hotel.auth');
Route::post('/habblet/ajax/removeFeedItem', [HabbletController::class, 'removeFeedItem'])->middleware('hotel.auth');
Route::post('/habblet/habbosearchcontent', [HabbletController::class, 'habboSearch'])->middleware(['hotel.optional', 'throttle:20,1']);
Route::get('/habblet/mytagslist', [HabbletController::class, 'myTags'])->middleware('hotel.optional');
Route::post('/habblet/mytagslist', [HabbletController::class, 'myTags'])->middleware('hotel.optional');
Route::post('/habblet/report_user', [HabbletController::class, 'reportUser'])->middleware('hotel.auth');
Route::match(['get', 'post'], '/habboclub/habboclub_confirm', [HabbletController::class, 'clubSubscribe'])->middleware('hotel.auth');
Route::match(['get', 'post'], '/habboclub/habboclub_subscribe', [HabbletController::class, 'clubSubscribe'])->middleware('hotel.auth');
Route::match(['get', 'post'], '/habboclub/habboclub_reminder_remove', [HabbletController::class, 'removeFeedItem'])->middleware('hotel.auth');
Route::match(['get', 'post'], '/credits/habboclub', [HabbletController::class, 'clubGift'])->middleware('hotel.auth');
Route::post('/profile/wardrobeStore', [HabbletController::class, 'wardrobeStore'])->middleware('hotel.auth');
Route::get('/mod/localizations', [HabbletController::class, 'localizations']);
Route::post('/mod/add_{type}_report', [HabbletController::class, 'addReport'])->middleware('hotel.auth');
Route::get('/trax/song/{id}', [HabbletController::class, 'traxUnavailable']);
Route::match(['get', 'post'], '/friendmanagement/ajax/viewcategory', [HabbletController::class, 'friendManagement'])->middleware('hotel.auth');
Route::match(['get', 'post'], '/friendmanagement/ajax/deletefriends', [HabbletController::class, 'friendManagementRefuse'])->middleware('hotel.auth');
Route::match(['get', 'post'], '/friendmanagement/ajax/{action}', [HabbletController::class, 'friendManagementRefuse'])->middleware('hotel.auth');
Route::match(['get', 'post'], '/discussions/actions/newtopic', [HabbletController::class, 'discussionsNewtopic'])->middleware('hotel.auth');
Route::post('/discussions/actions/previewtopic', [HabbletController::class, 'discussionsPreview'])->middleware('hotel.auth');
Route::post('/discussions/actions/previewpost', [HabbletController::class, 'discussionsPreview'])->middleware('hotel.auth');
Route::match(['get', 'post'], '/discussions/actions/confirm_delete_topic', [HabbletController::class, 'discussionsConfirmDelete'])->middleware('hotel.auth');
Route::match(['get', 'post'], '/discussions/actions/pingsession', [HabbletController::class, 'discussionsPing'])->middleware('hotel.auth');
Route::match(['get', 'post'], '/discussions/actions/opentopicsettings', [HabbletController::class, 'discussionsRefuse'])->middleware('hotel.auth');
Route::match(['get', 'post'], '/discussions/actions/{action}', [HabbletController::class, 'discussionsRefuse'])->middleware('hotel.auth');
Route::get('/components/updateHabboCount', [HabbletController::class, 'habboCount']);
Route::get('/xml/promo_habbos.xml', [HabbletController::class, 'promoHabbos']);
Route::get('/xml/promo_habbos_v2.xml', [HabbletController::class, 'promoHabbos']);
Route::get('/habbo-imaging/badge.php', BadgeImagingController::class);
Route::get('/habbo-imaging/badge/{badge}', BadgeImagingController::class)->where('badge', '[A-Za-z0-9.]+');
Route::middleware('hotel.optional')->group(function () {
    Route::get('/grouppurchase/group_create_form', [HabbletController::class, 'groupCreateForm']);
    Route::post('/grouppurchase/purchase_confirmation', [HabbletController::class, 'groupConfirm']);
    Route::post('/grouppurchase/purchase_ajax', [HabbletController::class, 'groupPurchase']);
});

Route::middleware('hotel.auth')->group(function () {
    Route::get('/myhabbo/startSession/{id}', [HomesController::class, 'startSession'])->whereNumber('id');
    Route::get('/myhabbo/cancel/{id}', [HomesController::class, 'cancel'])->whereNumber('id');
    Route::post('/myhabbo/save', [HomesController::class, 'save']);
    Route::match(['get', 'post'], '/groups/actions/startEditingSession/{id?}', [HomesController::class, 'groupStart'])->whereNumber('id');
    Route::match(['get', 'post'], '/groups/actions/cancelEditingSession', [HomesController::class, 'groupCancel']);
    Route::post('/groups/actions/saveEditingSession', [HomesController::class, 'groupSave']);

    Route::post('/myhabbo/store/main', [HomesController::class, 'storeMain']);
    Route::post('/myhabbo/store/items', [HomesController::class, 'storeItems']);
    Route::post('/myhabbo/store/preview', [HomesController::class, 'storePreview']);
    Route::post('/myhabbo/store/purchase_confirm', [HomesController::class, 'storePurchaseConfirm']);
    Route::post('/myhabbo/store/purchase', [HomesController::class, 'storePurchase']);
    Route::post('/myhabbo/store/background_warning', [HomesController::class, 'backgroundWarning']);
    Route::post('/myhabbo/store/inventory', [HomesController::class, 'inventory']);
    Route::post('/myhabbo/store/inventory_items', [HomesController::class, 'inventoryItems']);
    Route::post('/myhabbo/store/inventory_preview', [HomesController::class, 'inventoryPreview']);

    Route::post('/myhabbo/sticker/place_sticker', [HomesController::class, 'placeSticker']);
    Route::post('/myhabbo/sticker/remove_sticker', [HomesController::class, 'removeSticker']);
    Route::post('/myhabbo/stickie/edit', [HomesController::class, 'stickieEdit']);
    Route::post('/myhabbo/stickie/delete', [HomesController::class, 'stickieDelete']);
    Route::post('/myhabbo/noteeditor/editor', [HomesController::class, 'noteEditor']);
    Route::post('/myhabbo/noteeditor/place', [HomesController::class, 'notePlace']);
    Route::post('/myhabbo/noteeditor/preview', [HomesController::class, 'notePreview']);
    Route::post('/myhabbo/widget/add', [HomesController::class, 'widgetAdd']);
    Route::post('/myhabbo/widget/delete', [HomesController::class, 'widgetDelete']);
    Route::post('/myhabbo/widget/edit', [HomesController::class, 'widgetEdit']);
    Route::post('/myhabbo/guestbook/add', [HomesController::class, 'guestbookAdd']);
    Route::post('/myhabbo/guestbook/list', [HomesController::class, 'guestbookList']);
    Route::post('/myhabbo/guestbook/preview', [HomesController::class, 'guestbookPreview']);
    Route::post('/myhabbo/guestbook/remove', [HomesController::class, 'guestbookRemove']);
    Route::post('/myhabbo/guestbook/configure', [HomesController::class, 'guestbookConfigure']);
    Route::post('/myhabbo/avatarlist/friendsearchpaging', [HomesController::class, 'friendSearch']);
    Route::post('/myhabbo/avatarlist/avatarinfo', [HomesController::class, 'avatarInfo']);
    Route::post('/myhabbo/avatarlist/membersearchpaging', [HomesController::class, 'memberSearch']);
    Route::post('/myhabbo/groups/groupinfo', [HomesController::class, 'groupInfo']);
    Route::post('/myhabbo/groups/memberlist', [HomesController::class, 'memberSearch']);
    Route::post('/myhabbo/tag/add', [HomesController::class, 'tagRefuse']);
    Route::post('/myhabbo/tag/remove', [HomesController::class, 'tagRefuse']);
    Route::post('/myhabbo/tag/list', [HomesController::class, 'tagRefuse']);
    Route::post('/myhabbo/tag/addgrouptag', [HomesController::class, 'tagRefuse']);
    Route::post('/myhabbo/tag/removegrouptag', [HomesController::class, 'tagRefuse']);
    Route::post('/myhabbo/tag/listgrouptags', [HomesController::class, 'tagRefuse']);
    Route::post('/myhabbo/friends/add', [HomesController::class, 'friendAddRefuse']);
    Route::match(['get', 'post'], '/myhabbo/rating/rate', [HomesController::class, 'rate']);
    Route::match(['get', 'post'], '/myhabbo/rating/reset_ratings', [HomesController::class, 'resetRatings']);
    Route::match(['get', 'post'], '/myhabbo/linktool/search', [HomesController::class, 'linktool']);
    Route::match(['get', 'post'], '/myhabbo/traxplayer/select_song', [HabbletController::class, 'traxUnavailable']);
    Route::match(['get', 'post'], '/myhabbo/groups/batch_confirm_accept', [GroupController::class, 'memberBatchConfirm'])->defaults('op', 'accept');
    Route::match(['get', 'post'], '/myhabbo/groups/batch_confirm_decline', [GroupController::class, 'memberBatchConfirm'])->defaults('op', 'decline');
    Route::match(['get', 'post'], '/myhabbo/groups/batch_confirm_give_rights', [GroupController::class, 'memberBatchConfirm'])->defaults('op', 'give_rights');
    Route::match(['get', 'post'], '/myhabbo/groups/batch_confirm_remove', [GroupController::class, 'memberBatchConfirm'])->defaults('op', 'remove');
    Route::match(['get', 'post'], '/myhabbo/groups/batch_confirm_revoke_rights', [GroupController::class, 'memberBatchConfirm'])->defaults('op', 'revoke_rights');
    Route::match(['get', 'post'], '/myhabbo/groups/batch_accept', [GroupController::class, 'memberBatchRefuse']);
    Route::match(['get', 'post'], '/myhabbo/groups/batch_decline', [GroupController::class, 'memberBatchRefuse']);
    Route::match(['get', 'post'], '/myhabbo/groups/batch_give_rights', [GroupController::class, 'memberBatchRefuse']);
    Route::match(['get', 'post'], '/myhabbo/groups/batch_remove', [GroupController::class, 'memberBatchRefuse']);
    Route::match(['get', 'post'], '/myhabbo/groups/batch_revoke_rights', [GroupController::class, 'memberBatchRefuse']);

    Route::post('/minimail/loadMessages', [MinimailController::class, 'loadMessages']);
    Route::post('/minimail/loadMessage', [MinimailController::class, 'loadMessage']);
    Route::post('/minimail/sendMessage', [MinimailController::class, 'sendMessage']);
    Route::post('/minimail/deleteMessage', [MinimailController::class, 'deleteMessage']);
    Route::post('/minimail/undeleteMessage', [MinimailController::class, 'undeleteMessage']);
    Route::post('/minimail/emptyTrash', [MinimailController::class, 'emptyTrash']);
    Route::post('/minimail/preview', [MinimailController::class, 'preview']);
    Route::post('/minimail/recipients', [MinimailController::class, 'recipients']);
    Route::post('/minimail/report', [MinimailController::class, 'reportRefuse']);
    Route::post('/minimail/confirmReport', [MinimailController::class, 'reportRefuse']);
});

Route::get('/housekeeping/images/{path}', HousekeepingAssetController::class)
    ->where('path', '.*');
Route::get('/housekeeping/favicon.ico', fn () => app(HousekeepingAssetController::class)(request(), 'favicon.ico'));

Route::get('/housekeeping', [HousekeepingController::class, 'index']);
Route::post('/housekeeping', [HousekeepingController::class, 'login'])->middleware('throttle:8,1');
Route::get('/housekeeping/logout', [HousekeepingController::class, 'logout']);

Route::middleware('hotel.staff')->group(function () {
    Route::get('/housekeeping/dashboard', [HousekeepingController::class, 'dashboard']);
    Route::match(['get', 'post'], '/housekeeping/logs', [HousekeepingController::class, 'logs']);
    Route::get('/housekeeping/about', [HousekeepingController::class, 'about']);
    Route::get('/housekeeping/cache', [HousekeepingController::class, 'cache']);
    Route::match(['get', 'post'], '/housekeeping/settings', [HousekeepingController::class, 'settings']);
    Route::get('/housekeeping/updates', [HousekeepingController::class, 'updates']);
    Route::get('/housekeeping/auditlog', [HousekeepingController::class, 'auditlog']);
    Route::match(['get', 'post'], '/housekeeping/staffsessions', [HousekeepingController::class, 'staffsessions']);
    Route::get('/housekeeping/twofactor', [HousekeepingController::class, 'twofactor']);
    Route::match(['get', 'post'], '/housekeeping/maintenance', [HousekeepingController::class, 'maintenance']);
    Route::get('/housekeeping/permissions', [HousekeepingController::class, 'permissions']);

    Route::match(['get', 'post'], '/housekeeping/campaigns', [HousekeepingToolsController::class, 'campaigns']);
    Route::match(['get', 'post'], '/housekeeping/news', [HousekeepingToolsController::class, 'news']);
    Route::match(['get', 'post'], '/housekeeping/banners', [HousekeepingToolsController::class, 'banners']);
    Route::match(['get', 'post'], '/housekeeping/catalogue', [HousekeepingToolsController::class, 'catalogue']);
    Route::match(['get', 'post'], '/housekeeping/collectables', [HousekeepingToolsController::class, 'collectables']);
    Route::match(['get', 'post'], '/housekeeping/faq', [HousekeepingToolsController::class, 'faq']);
    Route::match(['get', 'post'], '/housekeeping/newsletter', [HousekeepingToolsController::class, 'newsletter']);
    Route::match(['get', 'post'], '/housekeeping/recommended', [HousekeepingToolsController::class, 'recommended']);
    Route::match(['get', 'post'], '/housekeeping/vouchers', [HousekeepingToolsController::class, 'vouchers']);

    Route::match(['get', 'post'], '/housekeeping/reports', [HousekeepingUsersController::class, 'reports']);
    Route::match(['get', 'post'], '/housekeeping/bans', [HousekeepingUsersController::class, 'bans']);
    Route::match(['get', 'post'], '/housekeeping/alerts', [HousekeepingUsersController::class, 'alerts']);
    Route::get('/housekeeping/search', [HousekeepingUsersController::class, 'search']);
    Route::match(['get', 'post'], '/housekeeping/help', [HousekeepingUsersController::class, 'help']);
    Route::match(['get', 'post'], '/housekeeping/users', [HousekeepingUsersController::class, 'users']);
});

require __DIR__.'/health.php';
