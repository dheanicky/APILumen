<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['middleware' => 'cors'], function ($router){
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->get('/profile', 'AuthController@me');

$router->get('/stuffs', 'StuffController@index');

// $router->post('/login', 'UserController@login');
// $router->get('/logout', 'UserController@logout');


$router->group(['prefix' => 'stuff/'], function() use ($router)
{
    // statis method
    $router->get('/data', 'StuffController@index');
    $router->post('/store', 'StuffController@store');
    $router->get('/trash', 'StuffController@trash');

    // dinamis method
    $router->get('{id}', 'StuffController@show');
    $router->patch('{id}', 'StuffController@update');
    $router->delete('/delete/{id}', 'StuffController@destroy');
    $router->get('/restore/{id}', 'StuffController@restore');
    $router ->get('/permanent/{id}', 'StuffController@deletePermanent');
});

$router->group(['prefix' => 'user/'], function() use ($router)
{
     // statis method
     $router->get('/data', 'UserController@index');
     $router->post('/', 'UserController@store');
     $router->get('/trash', 'UserController@trash');

 
     // dinamis method
     $router->get('{id}', 'UserController@show');
     $router->patch('/update/{id}', 'UserController@update');
     $router->delete('/delete/{id}', 'UserController@destroy');
     $router->get('/restore/{id}', 'UserController@restore');
     $router ->get('/permanent/{id}', 'UserController@deletePermanent');

});

$router->group(['prefix' => 'inbound-stuff/'], function() use ($router)
{
    // statis method
    // $router->get('/', 'InboundStuffController@index');
    $router->get('/data', 'InboundStuffController@index');
    $router->post('/store', 'InboundStuffController@store');
    $router->get('/restore/{id}', 'InboundStuffController@restore');

    // dinamis method
    $router->delete('/delete/{id}', 'InboundStuffController@destroy');
    $router->delete('/permanent/{id}', 'InboundStuffController@deletePermanent');
    $router->get('/trash', 'InboundStuffController@trash');
    $router->patch('update/{id}', 'InboundStuffController@update');
  
});

$router->group(['prefix' => 'lending/'], function() use ($router)
{
    $router->get('/data', 'LendingController@index');
    $router->post('/store', 'LendingController@store');
    $router->get('/show/{id}', 'LendingController@show');
    $router->patch('/update/{id}', 'LendingController@update');
    $router->delete('/delete', 'LendingController@destroy');
});

$router->group(['prefix' => 'restoration/'], function() use ($router) {
    $router->post('/store', 'RestorationsController@store');
    
});

$router->group(['prefix' => 'stuff-stock'], function() use ($router)
{
    // statis method
    $router->get('/data', 'StuffStockController@index');

    // dinamis method
    $router->post('add-stock/{id}', 'StuffStockController@addStock');
});

});



