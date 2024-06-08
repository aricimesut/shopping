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
$router->get('/', function(){
    return app()->version();
});
$router->group(['prefix' => 'order'], function () use ($router) {
    $router->get("/","OrderController@index");
    $router->post("/","OrderController@add");
    $router->delete("/{order}","OrderController@destroy");
});

$router->get("/discount/{orderId}","DiscountController@index");

