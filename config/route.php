<?php
/* 
 * 路由配置文件
 */  
use framework\lib\Route;
use framework\lib\Controller;

//home
Route::get('/', 'home\controller\Index@index');
Route::get('i/(:num)', 'home\controller\Index@index?p=:1');

Route::get('a/(:num)', 'home\controller\Article@posts?id=:1');

Route::get('c/(:any)', 'home\controller\Article@column?a=:1');
Route::get('c/(:any)/(:num)', 'home\controller\Article@column?a=:1&p=:2');

Route::get('download', 'home\controller\Download@index');

Route::get('search', 'home\controller\Search@index');
Route::post('/search/(:any)', 'admin\controller\Search@index?k=:1');

Route::get('page', 'home\controller\Index@page');
Route::get('view/(:num)', 'home\controller\Index@view?id=:1');


//forum
Route::get('/forum', 'forum\controller\Index@index');
Route::get('/login', 'forum\controller\Index@login');
Route::get('/reg', 'forum\controller\Index@reg');
Route::get('/check', 'forum\controller\Index@check');
Route::get('/forget', 'forum\controller\Index@forget');
Route::get('/my/home', 'forum\controller\Main@home');
Route::get('/my/center', 'forum\controller\Main@center');
Route::get('/my/profile', 'forum\controller\Main@profile');
Route::get('/my/message', 'forum\controller\Main@message');

Route::post('/login', 'forum\controller\Index@login');
Route::post('/reg', 'forum\controller\Index@reg');

Route::get('/message/index', 'forum\controller\Message@index');

Route::error(function() {
	Controller::error();
});