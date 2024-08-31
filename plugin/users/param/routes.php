<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

$router = router::getInstance();

$router->map('GET', '/users/login[/?]', 'UsersLoginController#login', 'login');
$router->map('POST', '/users/login-send[/?]', 'UsersLoginController#loginSend', 'login-send');
$router->map('GET', '/users/logout[/?]', 'UsersLoginController#logout', 'logout');
$router->map('GET', '/users/lost-password[/?]', 'UsersLoginController#lostPassword', 'lost-password');
$router->map('POST', '/users/lost-password-send[/?]', 'UsersLoginController#lostPasswordSend', 'lost-password-send');
$router->map('GET', '/users/lost-password/confirm/[a:token][/?]', 'UsersLoginController#lostPasswordConfirm', 'lost-password-confirm');

$router->map('GET', '/admin/users[/?]', 'UsersAdminController#home', 'users-admin-home');
$router->map('GET', '/admin/users/add', 'UsersAdminManagementController#addUser', 'users-add');
$router->map('POST', '/admin/users/add/send', 'UsersAdminManagementController#addUserSend', 'users-add-send');
$router->map('GET', '/admin/users/edit/[i:id]', 'UsersAdminManagementController#edit', 'users-edit');
$router->map('POST', '/admin/users/edit/send', 'UsersAdminManagementController#editUserSend', 'users-edit-send');
$router->map('GET', '/admin/users/delete/[i:id]/[a:token]', 'UsersAdminManagementController#delete', 'users-delete');
