<?php

/**
 * @copyright (C) 2023, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('Access denied!');

class CoreController extends Controller
{

    protected plugin $defaultPlugin;

    public function __construct() {
        parent::__construct();
        $pluginName = $this->core->getPluginToCall();

        if (pluginsManager::isActivePlugin($pluginName)) {
            $this->defaultPlugin = $this->pluginsManager->getPlugin($pluginName);
        } else {
            $this->core->error404();
        }
    }

    public function renderHome()
    {
        if ($this->defaultPlugin->getIsCallableOnPublic()) {
            $callback = $this->defaultPlugin->getCallablePublic();
            if (method_exists($callback[0], $callback[1])) {
                $obj = new $callback[0]();
                $response = call_user_func([$obj, $callback[1]]);
                return $response;
            }
        }
        core::getInstance()->error404();
    }

    public function renderAdminHome()
    {
        if ($this->defaultPlugin->getIsCallableOnAdmin()) {
            $callback = $this->defaultPlugin->getCallableAdmin();
            if (method_exists($callback[0], $callback[1])) {
                $obj = new $callback[0]();
                $response = call_user_func([$obj, $callback[1]]);
                return $response;
            }
        }
        core::getInstance()->error404();
    }
}