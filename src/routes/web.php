<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::get('/chatkun','ChatkunController@index');
Route::get('/chatkun/server','ChatkunController@pusher');
Route::get('/chatkun/history/{to_user_id}','ChatkunController@viewHistory');
Route::get('/chatkun/send','ChatkunController@pushMessage');
Route::post('/chatkun/upload','ChatkunController@uploadFile');
Route::get('/chatkun/download/{chat_message_id}/{token}','ChatkunController@download');

Route::post('/chatkun_auth',function(){
    if(Auth::check()){
        $pusher = new Pusher('71baa59665e9ba7ac6e9', '8efbc5d481a62566ace8', '344751');
        $presence_data = array('name' => Auth::user()->name);
        echo $pusher->presence_auth($_POST['channel_name'], $_POST['socket_id'], Auth::user()->id, $presence_data);
    }else{
        header('', true, 403);
        echo( "Forbidden" );
    }
});
