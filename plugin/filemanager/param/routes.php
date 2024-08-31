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

$router->map('GET', '/admin/filemanager[/?]', 'FileManagerAPIController#home', 'filemanager-home');
$router->map('POST', '/admin/filemanager/view-ajax/upload/[a:token]', 'FileManagerAPIController#upload', 'filemanager-upload');
$router->map('POST', '/admin/filemanager/view-ajax/delete/[a:token]', 'FileManagerAPIController#delete', 'filemanager-delete');
$router->map('POST', '/admin/filemanager/view-ajax/create/[a:token]', 'FileManagerAPIController#create', 'filemanager-create');

$router->map('POST', '/admin/filemanager/view', 'FileManagerAPIController#view', 'filemanager-view');
$router->map('POST', '/admin/filemanager/view-ajax', 'FileManagerAPIController#viewAjax', 'filemanager-view-ajax');
$router->map('GET', '/admin/filemanager/view-ajax/[a:token]/[a:editor]?', 'FileManagerAPIController#viewAjaxHome', 'filemanager-view-ajax-home');

$router->map('POST', '/admin/filemanager/api/upload/[a:token]', 'FileManagerAPIController#uploadAPI', 'filemanager-upload-api');
