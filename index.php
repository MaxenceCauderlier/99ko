<?php

/**
 * @copyright (C) 2024, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 *
 * @package 299Ko https://github.com/299Ko/299ko
 */

const ROOT = './';

include_once ROOT . 'common/common.php';

if (!$core->isInstalled()) {
    header('location:' . ROOT . 'install.php');
    die();
}

define('IS_LOGGED', UsersManager::isLogged());

// For futures versions
define('IS_ADMIN', IS_LOGGED);

Template::addGlobal('IS_LOGGED', IS_LOGGED);
Template::addGlobal('IS_ADMIN', IS_ADMIN);

$match = $router->match();
if (is_array($match)) {
    if ($runPlugin) {
        $runPlugin->loadControllers();
    }
    list($controller, $action) = explode('#', $match['target']);
    if (method_exists($controller, $action)) {
        $obj = new $controller();
        $core->callHook('beforeRunPlugin');
        $response = call_user_func_array([$obj,$action], $match['params']);
        echo $response->output();
        die();
    } else {
        // unreachable target
        $core->error404();
    }
}

$core->error404();
