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

$router->get('/poies',['uses'=>'POIController@index','as'=>'poies.index']);
$router->post('/poies',['uses'=>'POIController@store','as'=>'poies.store']);
$router->get('/poies/{id}',['uses'=>'POIController@show','as'=>'poies.show']);
$router->put('/poies/{id}',['uses'=>'POIController@update','as'=>'poies.update']);
$router->post('/poies/{id}',['uses'=>'POIController@destroy','as'=>'poies.destroy']);


$router->get('/spots',['uses'=>'SpotController@index','as'=>'spots.index']);
$router->post('/spots',['uses'=>'SpotController@store','as'=>'spots.store']);
