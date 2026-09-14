<?php
defined('PREVENT_DIRECT_SCRIPT_ACCESS') OR exit('No direct script access allowed');

$router->get('/', 'AuthController@login');
$router->get('/login', 'AuthController@login');
$router->post('/login/auth', 'AuthController@authenticate');
$router->get('/logout', 'AuthController@logout');

$router->get('/products', 'ProductController@index');
$router->get('/products/get_all', 'ProductController@get_all');
$router->post('/products/create', 'ProductController@create');
$router->post('/products/edit/{id}', 'ProductController@edit');
$router->post('/products/delete/{id}', 'ProductController@delete');