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

$router->map('GET', '/admin/seo[/?]', 'SEOAdminController#home', 'seo-admin-home');
$router->map('POST', '/admin/seo/save', 'SEOAdminController#save', 'seo-admin-save');