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
Broadcast::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/contacts', 'ContactsController@get')->name('contacts');

Route::get('/conversation/{id}', 'ContactsController@getMessagesFor');

Route::post('/conversation/send', 'ContactsController@send');


Route::post('/createGroup', 'ContactsController@createGroup');

Route::get('/getGroups', 'ContactsController@getGroups');


Route::get('/conversation/group/{id}', 'ContactsController@getMessagesForGroup');

Route::post('/conversation/group/send', 'ContactsController@groupSend');




Route::post('/delete/group/msg/{id}', 'ContactsController@deleteGroupMsg');
Route::post('/delete/msg/{id}', 'ContactsController@deleteMsg');



Route::get('/getGroupInfo/{id}', 'ContactsController@getGroupInfo');

Route::post('/deleteGroup/{id}', 'ContactsController@deleteGroup');

Route::post('/addUsersToGroup/{id}', 'ContactsController@addUsersToGroup');