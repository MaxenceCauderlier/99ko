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

$router->map('GET', '/admin/pluginsmanager[/?]', 'PluginsManagerController#list', 'pluginsmanager-list');
$router->map('POST', '/admin/pluginsmanager/save', 'PluginsManagerController#save', 'pluginsmanager-save');
$router->map('GET', '/admin/pluginsmanager/[a:plugin]/[a:token]', 'PluginsManagerController#maintenance', 'pluginsmanager-maintenance');