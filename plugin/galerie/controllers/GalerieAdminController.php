<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('Access denied!');

class GalerieAdminController extends AdminController
{

    public function home()
    {
        $galerie = new galerie();
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('galerie', 'admin');

        $tpl->set('galerie', $galerie);
        $tpl->set('mode', 'list');
        $tpl->set('token', $this->user->token);
        $response->addTemplate($tpl);
        return $response;
    }

    public function edit() {
        $item = new galerieItem();
        return $this->renderEdit($item);
    }

    public function editId($id) {
        $galerie = new galerie();
        $item = $galerie->createItem($id);
        if ($item === false) {
            $this->core->error404();
        }
        return $this->renderEdit($item);
    }

    public function delete($id, $token) {
        if (!$this->user->isAuthorized()) {
            return $this->home();
        }
        $galerie = new galerie();
        $item = $galerie->createItem($id);
        if ($item !== false) {
            if ($galerie->delItem($item)) {
                show::msg(lang::get('core-item-deleted'), 'success');
            } else {
                show::msg(lang::get('core-item-not-deleted'), 'error');
            }
        } else {
            show::msg(lang::get('core-item-not-deleted'), 'error');
        }
        return $this->home();
    }

    public function save() {
        if (!$this->user->isAuthorized()) {
            return $this->home();
        }
        $galerie = new galerie();
        $contentEditor = new Editor('galContent', '', lang::get('galerie.content'));

        $item = ($_REQUEST['id']) ? $galerie->createItem($_REQUEST['id']) : new galerieItem();
        $item->setCategory($_REQUEST['category']);
        $item->setTitle($_REQUEST['title']);
        $item->setContent($contentEditor->getPostContent());
        $item->setDate($_REQUEST['date']);
        $item->setHidden((isset($_POST['hidden'])) ? 1 : 0);
        if ($galerie->saveItem($item)) {
            show::msg(lang::get('core-changes-saved'), 'success');
        } else {
            show::msg(lang::get('core-changes-not-saved'), 'error');
        }
        return $this->editId($item->getId());
    }

    public function saveConf() {
        if (!$this->user->isAuthorized()) {
            return $this->home();
        }
        $introEditor = new Editor('introduction', '', lang::get('galerie.introduction'), true);
        $this->runPlugin->setConfigVal('label', trim($_POST['label']));
        $this->runPlugin->setConfigVal('order', trim($_POST['order']));
        $this->runPlugin->setConfigVal('introduction', $introEditor->getPostContent() );
        $this->runPlugin->setConfigVal('showTitles', (isset($_POST['showTitles']) ? 1 : 0));
        $this->runPlugin->setConfigVal('size', trim($_POST['size']));
        if ($this->pluginsManager->savePluginConfig($this->runPlugin)) {
            show::msg(lang::get('core-changes-saved'), 'success');
        } else {
            show::msg(lang::get('core-changes-not-saved'), 'error');
        }
        return $this->home();
    }

    protected function renderEdit(galerieItem $item) {
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('galerie', 'admin');
        $contentEditor = new Editor('galContent', $item->getContent(), lang::get('galerie.content'));

        $galerie = new galerie();
        if ($item->getDate() == '')
            $item->setDate('');
        
        $tpl->set('contentEditor', $contentEditor);
        $tpl->set('galerie', $galerie);
        $tpl->set('item', $item);
        $tpl->set('mode', 'edit');
        $tpl->set('token', $this->user->token);
        $response->addTemplate($tpl);
        return $response;
    }
}