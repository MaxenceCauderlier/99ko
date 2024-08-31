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

$router->map('GET', '/galerie[/?]', 'GalerieController#home', 'galerie-home');

$router->map('GET', '/admin/galerie[/?]', 'GalerieAdminController#home', 'admin-galerie-home');
$router->map('GET', '/admin/galerie/edit', 'GalerieAdminController#edit', 'admin-galerie-edit');
$router->map('GET', '/admin/galerie/edit/[a:id]', 'GalerieAdminController#editId', 'admin-galerie-edit-id');
$router->map('GET', '/admin/galerie/delete/[a:id]/[a:token]', 'GalerieAdminController#delete', 'admin-galerie-delete');
$router->map('POST', '/admin/galerie/save', 'GalerieAdminController#save', 'admin-galerie-save');
$router->map('POST', '/admin/galerie/saveConf', 'GalerieAdminController#saveConf', 'admin-galerie-save-config');
