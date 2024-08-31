<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

class SEOAdminController extends AdminController {
    
    public function home() {
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('seo', 'admin');

        $tpl->set('position', $this->runPlugin->getConfigVal('position'));

        $response->addTemplate($tpl);
        return $response;
    }

    public function save() {
        if (!$this->user->isAuthorized()) {
            return $this->home();
        }
        $pos = $_POST['position'];
        $this->seoSavePositionMenu($pos);

        $this->runPlugin->setConfigVal('position', $pos);
        $this->runPlugin->setConfigVal('trackingId', trim($_POST['trackingId']));
        $this->runPlugin->setConfigVal('wt', trim($_POST['wt']));

        // Save Social adress
        $vars = seoGetSocialVars();
        foreach ($vars as $v) {
            $this->runPlugin->setConfigVal($v, trim($_POST[$v]));
        }

        if ($this->pluginsManager->savePluginConfig($this->runPlugin)) {
            show::msg(lang::get('core-changes-saved'), 'success');
        } else {
            show::msg(lang::get('core-changes-not-saved'), 'error');
        }
        $this->core->redirect($this->router->generate('seo-admin-home'));
    }

    protected function seoSavePositionMenu($position) {
        $arr = ['endFrontHead' => 'seoEndFrontHead'];
        switch ($position) {
            case 'menu':
                $tmp = ['endMainNavigation' => 'seoMainNavigation'];
                break;
            case 'footer':
                $tmp = ['footer' => 'seoFooter'];
                break;
            case 'endfooter':
                $tmp = ['endFooter' => 'seoFooter'];
                break;
            case 'float':
                $tmp = ['endFrontBody' => 'seoEndFrontBody'];
                break;
            default:
                $tmp = [];
        }
        $data = array_merge($arr, $tmp);
        util::writeJsonFile(PLUGINS . 'seo/param/hooks.json', $data);
    }
}