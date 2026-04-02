<?php

$router->get('/', 'HomeController@index');
$router->get('/about', 'AboutController@about');

// Assistant AI routes
$router->get('/assistantai', 'AssistantAIController@index');
$router->get('/ai/text-to-speech', 'AiTTSController@index');
$router->get('/ai/image-generator', 'AiImagesController@index');
// Routes mới cho User
$router->get('/register', 'UserController@showRegister');
$router->post('/register', 'UserController@register');
$router->get('/login', 'UserController@showLogin');
$router->post('/login', 'UserController@login');
$router->get('/logout', 'UserController@logout');  // Tùy chọn
// Profile routes (POST update đã có)
$router->get('/profile', 'ProfileController@index');
$router->post('/profile', 'ProfileController@update');  // Giờ handle file
//AI chat route
$router->get('/ai-chat', 'AiChatController@index');
$router->post('/ai-chat', 'AiChatController@index');
//AI homework solver route
$router->get('/ai/homework-solver', 'AiHomeworkController@index');
$router->post('/ai/homework-solver', 'AiHomeworkController@index');
//AI quiz generator route
$router->get('/ai/quiz-generator', 'AiQuizController@index');
$router->post('/ai/quiz-generator', 'AiQuizController@index');
//AI summarizer route
$router->get('/ai/summarizer', 'AiSummarizerController@index');
$router->post('/ai/summarizer', 'AiSummarizerController@index');
// Friendship routes
$router->get('/friends', 'FriendshipController@connect');
$router->post('/friend/send', 'FriendshipController@send');
$router->post('/friend/accept', 'FriendshipController@accept');
$router->post('/friend/decline', 'FriendshipController@decline');
$router->post('/friend/remove', 'FriendshipController@remove');
// Chat routes
$router->post('/chat/send', 'ChatController@sendMessage');
$router->get('/chat', 'ChatController@chatWithFriend'); // nhận $_GET['friend_id']
// admin
$router->get('/management', 'ManagementController@dashboard');
//games
$router->get('/games', 'GamesController@index');
$router->get('/games/memory', 'GamesController@memory');
$router->get('/games/math', 'GamesController@math');
$router->get('/games/speed', 'GamesController@speed');
$router->get('/games/samurai', 'GamesController@samurai');
// VIP
$router->get('/vip/upgrade', 'VipController@upgrade');
$router->get('/vip/sheet-proxy', 'VipController@sheetProxy');
$router->post('/vip/confirm', 'VipController@confirm');
$router->get('/vip/check', 'VipController@check');

$router->get('/admin', 'AdminController@users');
$router->get('/admin/users', 'AdminController@users');
$router->get('/admin/vip', 'AdminController@vip');
$router->get('/admin/payments', 'AdminController@payments');
$router->get('/admin/settings', 'AdminController@settings');

// actions (POST)
$router->post('/admin/users/update-role', 'AdminController@updateRole');
$router->post('/admin/users/toggle-vip', 'AdminController@toggleVip');
$router->post('/admin/users/delete', 'AdminController@deleteUser');

$router->post('/admin/settings/save', 'AdminController@saveSettings');
$router->get('/management', 'AdminController@management');
$router->post('/admin/users/create', 'AdminController@createUser');
$router->post('/admin/users/edit', 'AdminController@editUser');
$router->post('/admin/vip/remove', 'AdminController@removeVip');
$router->post('/admin/vip/add', 'AdminController@addVip');


