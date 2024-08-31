<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

require_once(PLUGINS . 'filemanager/lib/FileManager.php');

/**
 * Install function
 */
function filemanagerInstall() {
    if (!file_exists(DATA_PLUGIN . 'filemanager/files.json')) {
        @mkdir(UPLOAD . 'files/');
        @chmod(UPLOAD . 'files', 0755);
        util::writeJsonFile(DATA_PLUGIN . 'filemanager/files.json', []);
    }
}

/**
 * Function to display the button to manage files by Ajax
 */
function filemanagerDisplayManagerButton($textareaId = false):string {
    return '<a class="button fmUploadButton" data-fancybox data-type="ajax" '
    . 'href="' . router::getInstance()->generate('filemanager-view-ajax-home', 
    ['token' => UsersManager::getCurrentUser()->token,
    'editor' => $textareaId]) .'"/><i class="fa-solid fa-file-image"></i> '. lang::get('filemanager.button-label').'</a>';
    
}
