<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('Access denied!');

class PluginsManagerController extends AdminController
{

    public function list() {
        $priority = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
            7 => 7,
            8 => 8,
            9 => 9,
        );
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('pluginsmanager', 'list');

        $tpl->set('priority', $priority);
        $tpl->set('token', $this->user->token);

        $response->addTemplate($tpl);
        return $response;
    }

    public function save() {
        if (!$this->user->isAuthorized()) {
            return $this->list();
        }
        $error = false;
        foreach ($this->pluginsManager->getPlugins() as $k => $v) {
            if (isset($_POST['activate'][$v->getName()])) {
                if (!$v->isInstalled())
                    $this->pluginsManager->installPlugin($v->getName(), true);
                else
                    $v->setConfigVal('activate', 1);
            } else
                $v->setConfigVal('activate', 0);
            if ($v->isInstalled()) {
                $v->setConfigVal('priority', intval($_POST['priority'][$v->getName()]));
                if (!$this->pluginsManager->savePluginConfig($v)) {
                    $error = true;
                }
            }
        }
        if ($error) {
            show::msg(lang::get('core-changes-not-saved'), 'error');
        } else {
            show::msg(lang::get('core-changes-saved'), 'success');
        }
        $this->core->redirect($this->router->generate('pluginsmanager-list'));
    }

    public function maintenance($plugin, $token) {
        if (!$this->user->isAuthorized()) {
            return $this->list();
        }
        $this->pluginsManager->installPlugin($plugin, true);
        $this->core->redirect($this->router->generate('pluginsmanager-list'));
    }
}