<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

class BlogAdminConfigController extends AdminController {

    public function saveConfig() {
        if ($this->user->isAuthorized()) {
            $this->runPlugin->setConfigVal('label', trim($_REQUEST['label']));
            $this->runPlugin->setConfigVal('itemsByPage', trim(intval($_REQUEST['itemsByPage'])));
            $this->runPlugin->setConfigVal('hideContent', (isset($_POST['hideContent']) ? 1 : 0));
            $this->runPlugin->setConfigVal('comments', (isset($_POST['comments']) ? 1 : 0));
            $this->runPlugin->setConfigVal('displayTOC', filter_input(INPUT_POST, 'displayTOC', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $this->runPlugin->setConfigVal('displayAuthor', (isset($_POST['displayAuthor']) ? 1 : 0));
            $this->runPlugin->setConfigVal('authorName', trim($_POST['authorName']));
            $this->runPlugin->setConfigVal('authorAvatar', trim($_POST['authorAvatar']));
            $this->runPlugin->setConfigVal('authorBio', $this->core->callHook('beforeSaveEditor',htmlspecialchars($_POST['authorBio'])));
            if ($this->pluginsManager->savePluginConfig($this->runPlugin)) {
                show::msg(lang::get('core-changes-saved'), 'success');
            } else {
                show::msg(lang::get('core-changes-not-saved'), 'error');
            }            
        }
        // Open the posts list
        $controller = new BlogAdminPostsController();
        return $controller->list();
    }
}