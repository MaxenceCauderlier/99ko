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

$router->map('GET', '/contact[/?]', 'ContactController#home', 'contact-home');
$router->map('POST', '/contact/send.html', 'ContactController#send', 'contact-send');

$router->map('GET', '/admin/contact[/?]', 'ContactAdminController#home', 'contact-admin-home');
$router->map('POST', '/admin/contact/saveParams', 'ContactAdminController#saveParams', 'contact-saveParams');
$router->map('POST', '/admin/contact/saveConfig', 'ContactAdminController#saveConfig', 'contact-saveConfig');
$router->map('GET', '/admin/contact/emptyMails/[a:token]', 'ContactAdminController#emptyMails', 'contact-empty-mails');