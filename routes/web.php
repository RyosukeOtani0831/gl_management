<?php
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\HashController;

Route::get('/logtest', function () {
    \Log::emergency('ログテスト実行中！');
    return 'ログ書き込みテスト完了';
});

Route::get('/healthcheck', function() {
    return response('OK', 200);
});

//
// 管理画面関連
//
Route::group(['middleware' => 'basicauth'], function() {

    Route::get('/', function(){
        return Redirect::to('/login');
    });

    Route::get('/api/users/paginated', 'MainController@getUsersPaginated')->name('users.paginated');

    Route::get('login', 'LoginController@inputForm')->name('login');
    Route::post('loginCheck', 'LoginController@loginCheck')->name('loginCheck');

    Route::get('main', 'MainController@drawMain')->name('main');
    Route::post('main', 'MainController@drawMain');

    Route::get('/get-group-content', 'MainController@getGroupContent')->name('getGroupContent');
    Route::get('/get-group-admin-content', 'MainController@getGroupAdminContent')->name('getGroupAdminContent');

    Route::post('createUser', 'MainController@createUser');
    Route::post('createGroup', 'MainController@createGroup');
    Route::post('createTeam', 'MainController@createTeam');
    Route::post('createUserTemp', 'MainController@createUserTemp');

    Route::post('deleteUser', 'MainController@deleteUser');
    Route::post('deleteGroup', 'MainController@deleteGroup');
    Route::post('deleteGroupAdmin', 'MainController@deleteGroupAdmin');
    Route::post('deleteTeam', 'MainController@deleteTeam');
    Route::post('deleteUserTemp', 'MainController@deleteUserTemp');

    Route::post('editUser', 'MainController@editUser');
    Route::post('editGroup', 'MainController@editGroup');
    Route::post('editGroupAdmin', 'MainController@editGroupAdmin');
    Route::post('editTeam', 'MainController@editTeam');
    Route::post('editUserTemp', 'MainController@editUserTemp');

    Route::post('importUserCsv', 'CsvController@importUserCsv');
    Route::post('importUserEditCsv', 'CsvController@importUserEditCsv');
    Route::post('importUserDelCsv', 'CsvController@importUserDelCsv');
    Route::post('importGroupCsv', 'CsvController@importGroupCsv');
    Route::post('importTeamCsv', 'CsvController@importTeamCsv');
    Route::post('importUserTempCsv', 'CsvController@importUserTempCsv');

    Route::post('sendManagementMessage', 'MainController@sendManagementMessage');

    Route::post('/save-hash', [HashController::class, 'save']);
    Route::post('/set-hash', 'MainController@setHash')->name('setHash');
    Route::post('/group/close', 'MainController@updateGroupClose')->name('updateGroupClose');

    Route::get('/api/group/{groupId}/admin-members', 'MainController@getGroupAdminMembers')
        ->name('getGroupAdminMembers');
});

//
// ユーザ登録関連
//
Route::get('user', 'UserContentsController@main')->name('user');
Route::get('note', 'UserContentsController@note')->name('note');
Route::post('user', 'UserContentsController@createUserAccount');
Route::post('user', 'UserContentsController@registrateUser');



//
// 配信ページ関連
//
Route::get('loginDelivery', 'DeliveryController@login')->name('delivery_login');
Route::post('loginDeliveryCheck', 'DeliveryController@loginDeliveryCheck');

Route::get('delivery', 'DeliveryController@main')->name('delivery_main');
Route::post('delivery', 'DeliveryController@main');

Route::post('deliveryMessage', 'DeliveryController@deliveryMessage');
Route::get('deliveryLogout', 'DeliveryController@logout')->name('delivery_logout');

//
// 配信ページ関連（テスト）
//
Route::get('testLoginDelivery', 'TestDeliveryController@login')->name('test_delivery_login');
Route::post('testLoginDeliveryCheck', 'TestDeliveryController@loginDeliveryCheck');

Route::get('testDelivery', 'TestDeliveryController@main')->name('test_delivery_main');
Route::post('testDelivery', 'TestDeliveryController@main');

Route::post('testDeliveryMessage', 'TestDeliveryController@deliveryMessage');
Route::get('testDeliveryLogout', 'TestDeliveryController@logout')->name('test_delivery_logout');