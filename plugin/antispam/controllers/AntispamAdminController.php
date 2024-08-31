<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('Access denied!');

class AntispamAdminController extends AdminController
{
    public function home() {
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('antispam', 'config');
        $tpl->set('useText', ($this->runPlugin->getConfigVal('type') === 'useText') ? 'checked' : '' );
        $tpl->set('useRecaptcha', ($this->runPlugin->getConfigVal('type') === 'useRecaptcha') ? 'checked' : '' );
        $response->addTemplate($tpl);
        return $response;
    }

    public function save() {
        if (!$this->user->isAuthorized()) {
            $this->core->error404();
        }
        if ($_POST['captcha'] === 'useRecaptcha') {
            // Use ReCaptcha
            if (!isset($_POST['recaptchaPublicKey']) || !isset($_POST['recaptchaSecretKey']) ||
                    trim($_POST['recaptchaPublicKey']) == '' || trim($_POST['recaptchaSecretKey']) == '') {
                // Empty keys
                show::msg(Lang::get('antispam.google-captcha-empty'), 'error');
                $this->core->redirect($this->router->generate('antispam-admin'));
            }
            // Save ReCaptcha
            $this->runPlugin->setConfigVal('recaptchaPublicKey', trim($_POST['recaptchaPublicKey']));
            $this->runPlugin->setConfigVal('recaptchaSecretKey', trim($_POST['recaptchaSecretKey']));
        }
        // Save Type
        $this->runPlugin->setConfigVal('type', trim($_POST['captcha']));
        $this->pluginsManager->savePluginConfig($this->runPlugin);

        show::msg(Lang::get('core-changes-saved'), 'success');
        $this->core->redirect($this->router->generate('antispam-admin'));
    }
}