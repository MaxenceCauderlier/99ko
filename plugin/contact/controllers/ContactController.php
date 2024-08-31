<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('Access denied!');

class ContactController extends PublicController
{

    public function home()
    {
        $antispam = ($this->pluginsManager->isActivePlugin('antispam')) ? new antispam() : false;

        $this->runPlugin->setMainTitle($this->runPlugin->getConfigVal('label'));
        $this->runPlugin->setTitleTag($this->runPlugin->getConfigVal('label'));
        $antispamField = ($antispam) ? $antispam->show() : '';

        $response = new PublicResponse();
        $tpl = $response->createPluginTemplate('contact', 'contact');

        $tpl->set('name', $_POST['name'] ?? '');
        $tpl->set('firstname', $_POST['firstname'] ?? '');
        $tpl->set('email', $_POST['email'] ?? '');
        $tpl->set('message', $_POST['message'] ?? '');
        $tpl->set('acceptation', (trim($this->runPlugin->getConfigVal('acceptation')) != '') ? true : false);
        $tpl->set('antispam', $antispam);
        $tpl->set('antispamField', $antispamField);
        $tpl->set('sendUrl', $this->router->generate('contact-send'));
        $response->addTemplate($tpl);
        return $response;
    }

    public function send()
    {
        $sendError = false;
        $antispam = ($this->pluginsManager->isActivePlugin('antispam')) ? new antispam() : false;
        // quelques contrôles et temps mort volontaire avant le send...
        sleep(2);
        if ($antispam) {
            if (!$antispam->isValid()) {
                show::msg(lang::get('antispam.invalid-captcha'), 'error');
                $sendError = true;
            }
        }
        if (!$sendError) {
            if ($_POST['_name'] == '' && strchr($_SERVER['HTTP_REFERER'], 'contact') !== false) {
                if (contactSend()) {
                    show::msg(lang::get('contact.msg-sent'), 'success');
                } else {
                    show::msg(lang::get('something-wrong'), 'error');
                    $sendError = true;
                }
            } else {
                show::msg(lang::get('contact.fiels-error'), 'error');
                $sendError = true;
            }
        }
        return $this->home();
    }
}