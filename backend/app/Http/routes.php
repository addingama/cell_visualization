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
ini_set('memory_limit','256M');
ini_set('max_execution_time', 3600);
$app->get('/', 'App\Http\Controllers\MapController@home');
$app->get('/phones', 'App\Http\Controllers\MapController@getPhones');
$app->get('/filter/{date}/{number}', 'App\Http\Controllers\MapController@filterNumber');
$app->get('/filter/{date}/{numbers}/{start}', 'App\Http\Controllers\MapController@filterRange');

$app->get('/generatePivot/{numbers}/{start}', 'App\Http\Controllers\MapController@generatePivot');
