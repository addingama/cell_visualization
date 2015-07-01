<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
ini_set('memory_limit','1024M');
$app->get('/', 'App\Http\Controllers\MapController@home');
$app->get('/index', 'App\Http\Controllers\MapController@index');
$app->get('/tanggal', 'App\Http\Controllers\MapController@getDates');
$app->get('/tanggal/{date}/phones', 'App\Http\Controllers\MapController@getPhoneByDate');
$app->get('/phones', 'App\Http\Controllers\MapController@getPhones');


$app->get('/filter/{date}/{number}', 'App\Http\Controllers\MapController@filterNumber');
$app->get('/filter/{date}/{numbers}/{start}', 'App\Http\Controllers\MapController@filterRange');

