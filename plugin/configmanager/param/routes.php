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

$router->map('GET', '/admin/configmanager[/?]', 'ConfigManagerAdminController#home', 'configmanager-admin');
$router->map('POST', '/admin/configmanager/save', 'ConfigManagerAdminController#save', 'configmanager-admin-save');
$router->map('GET', '/admin/configmanager/cacheclear/[a:token]', 'ConfigManagerAdminController#clearCache', 'configmanager-admin-cache-clear');
$router->map('GET', '/admin/configmanager/update/[a:token]', 'ConfigManagerUpdateController#process', 'configmanager-update');
$router->map('GET', '/admin/configmanager/delete-install/[a:token]', 'ConfigManagerAdminController#deleteInstall', 'configmanager-delete-install');
