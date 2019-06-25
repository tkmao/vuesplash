<?php
// 会員登録
Route::post('/register', 'Auth\RegisterController@register')->name('register');

// ログイン
Route::post('/login', 'Auth\LoginController@login')->name('login');

// ログアウト
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

// ログインユーザー
Route::get('/user', function () {
    return Auth::user();
})->name('user');

// トークンリフレッシュ
Route::get('/reflesh-token', function (Illuminate\Http\Request $request) {
    $request->session()->regenerateToken();

    return response()->json();
});

// 写真投稿
Route::post('/photos', 'PhotoController@create')->name('photo.create');
// 写真一覧
Route::get('/photos', 'PhotoController@indexUser')->name('photo.index');
// 写真詳細
Route::get('/photos/{id}', 'PhotoController@showUser')->name('photo.show');
// コメント
Route::post('/photos/{photo}/comments', 'PhotoController@addComment')->name('photo.comment');
// いいね
Route::put('/photos/{id}/like', 'PhotoController@like')->name('photo.like');
// いいね解除
Route::delete('/photos/{id}/like', 'PhotoController@unlike');

// ユーザ登録
Route::post('/user/{id}/store', 'PhotoController@storeUser')->name('user.storeuser');

// 勤務表取得
Route::post('/workschedule/get', 'WorkScheduleController@index')->name('workschedule.index');
// 勤務表登録
Route::post('/workschedule/store', 'WorkScheduleController@store')->name('workschedule.store');

// 勤務表提出状況取得
Route::post('/workschedulemonth/issubmitted', 'WorkScheduleMonthController@isSubmitted')->name('workschedulemonth.issubmitted');

// 週報取得
Route::post('/weeklyreport/get', 'WeeklyReportController@index')->name('weeklyreport.index');
// 勤務表取得（週報用）
Route::post('/workschedule/getweek', 'WorkScheduleController@getByWeekNumber')->name('workschedule.getweek');
// 週報登録
Route::post('/weeklyreport/store', 'WeeklyReportController@store')->name('weeklyreport.store');
// 最古の勤務表データを取得
Route::post('/workschedule/getoldestworkdate', 'WorkScheduleController@getOldestWorkdateByUserId')->name('workschedule.getoldestworkdate');

// 全ユーザ週報取得（週報管理用）
Route::post('/weeklyreport/getalluser', 'WeeklyReportController@getAllUser')->name('weeklyreport.getalluser');
// 全ユーザ勤務表取得（週報管理用）
Route::post('/workschedule/getalluser', 'WorkScheduleController@getAllUser')->name('workschedule.getalluser');

// プロジェクト一覧取得
Route::get('/project/getall', 'ProjectController@getAll')->name('project.getall');

// ユーザ情報取得
Route::post('/user/get', 'UserController@get')->name('user.get');
// ユーザ一覧取得
Route::get('/user/getall', 'UserController@getAll')->name('user.getall');
// ユーザ登録
Route::post('/user/store', 'UserController@store')->name('user.store');
// ユーザ編集
Route::post('/user/edit', 'UserController@edit')->name('user.edit');
// ユーザ削除
Route::post('/user/delete', 'UserController@delete')->name('user.delete');

// ユーザ契約登録
Route::post('/usercontract/store', 'UserContractController@store')->name('usercontract.store');
// ユーザ契約編集
Route::post('/usercontract/edit', 'UserContractController@edit')->name('usercontract.edit');
// ユーザ契約削除
Route::post('/usercontract/delete', 'UserContractController@delete')->name('usercontract.delete');

// 企業一覧取得
Route::get('/company/getall', 'CompanyController@getAll')->name('company.getall');
// 企業登録
Route::post('/company/store', 'CompanyController@store')->name('company.store');
// 企業編集
Route::post('/company/edit', 'CompanyController@edit')->name('company.edit');
// 企業削除
Route::post('/company/delete', 'CompanyController@delete')->name('company.delete');

// 休日一覧取得
Route::get('/holiday/getall', 'HolidayController@getAll')->name('holiday.getall');
// 休日登録
Route::post('/holiday/store', 'HolidayController@store')->name('holiday.store');
// 休日編集
Route::post('/holiday/edit', 'HolidayController@edit')->name('holiday.edit');
// 休日削除
Route::post('/holiday/delete', 'HolidayController@delete')->name('holiday.delete');

// ユーザタイプ一覧取得
Route::get('/usertype/getall', 'UserTypeController@getAll')->name('usertype.getall');
// ユーザタイプ登録
Route::post('/usertype/store', 'UserTypeController@store')->name('usertype.store');
// ユーザタイプ編集
Route::post('/usertype/edit', 'UserTypeController@edit')->name('usertype.edit');
// ユーザタイプ削除
Route::post('/usertype/delete', 'UserTypeController@delete')->name('usertype.delete');
