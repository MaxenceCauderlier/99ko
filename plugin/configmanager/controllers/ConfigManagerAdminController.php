<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('Access denied!');

class ConfigManagerAdminController extends AdminController {
    
    public function home() {

        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('configmanager', 'config');

        $tpl->set('link', $this->router->generate('configmanager-admin-save'));
        $tpl->set('cacheClearLink', $this->router->generate('configmanager-admin-cache-clear', ['token' => $this->user->token]));


        $response->addTemplate($tpl);
        return $response;
    }

    public function save() {
        if (!$this->user->isAuthorized()) {
            return $this->home();
        }
        if (array_key_exists($_POST['siteLang'], lang::$availablesLocales)) {
            $lang = $_POST['siteLang'];
        } else {
            $lang = lang::getLocale();
        }
        $config = array(
            'siteName' => (trim($_POST['siteName']) != '') ? trim($_POST['siteName']) : 'Demo',
            'siteDesc' => (trim($_POST['siteDesc']) != '') ? trim($_POST['siteDesc']) : '',
            'siteLang' => $lang,
            'siteUrl' => (trim($_POST['siteUrl']) != '') ? rtrim(trim($_POST['siteUrl']), '/') : $this->core->getConfigVal('siteUrl'),
            'theme' => $_POST['theme'],
            'defaultPlugin' => $_POST['defaultPlugin'],
            'hideTitles' => (isset($_POST['hideTitles'])) ? true : false,
            'debug' => (isset($_POST['debug'])) ? true : false,
            'defaultAdminPlugin' => $_POST['defaultAdminPlugin']
        );

        if (!$this->core->saveConfig($config, $config)) {
            show::msg(lang::get("core-changes-not-saved"), 'error');
        } else {
            show::msg(lang::get("core-changes-saved"), 'success');
        }
        $this->core->saveHtaccess($_POST['htaccess']);
        $this->core->redirect($this->router->generate('configmanager-admin'));
    }

    public function deleteInstall($token) {
        if (!$this->user->isAuthorized()) {
            return $this->home();
        }
        $del = unlink(ROOT . 'install.php');
        if ($del) {
            show::msg(lang::get('configmanager-deleted-install'), 'success');
        } else {
            show::msg(lang::get('configmanager-error-deleting-install'), 'error');
        }
        return $this->home();
    }

    public function clearCache($token) {
        if (!$this->user->isAuthorized()) {
            return $this->home();
        }
        $updaterManager = new UpdaterManager();
        $updaterManager->clearCache();
        show::msg(lang::get('configmanager-cache-cleared'), 'success');
        return $this->home();
    }
}