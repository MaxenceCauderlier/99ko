<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('Access denied!');

class ConfigManagerUpdateController extends AdminController {

    public function process($token) {
        $updaterManager = new UpdaterManager();
        if ($updaterManager->isReady) {
            $nextVersion = $updaterManager->getNextVersion();
        } else {
            $nextVersion = false;
        }
        if ($nextVersion && $this->user->isAuthorized()) {
            $updaterManager->update();
            show::msg(Lang::get('configmanager-updated', $nextVersion), 'success');
            $updaterManager->clearCache();
            $this->core->redirect($this->router->generate('configmanager-admin'));
        }
    }

}