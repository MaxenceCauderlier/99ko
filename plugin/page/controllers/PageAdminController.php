<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('Access denied!');

class PageAdminController extends AdminController {

    public function list() {
        $page = new page();
        // Recherche des pages perdues
        $parents = [];
        $lost = '';
        foreach ($page->getItems() as $k => $v)
            if ((int) $v->getParent() == 0) {
                $parents[] = $v->getId();
            }
        foreach ($page->getItems() as $k => $v)
            if ((int) $v->getParent() > 0) {
                if (!in_array($v->getParent(), $parents))
                    $lost .= $v->getId() . ',';
            }
        // Suite...
        if (!$page->createHomepage() && $this->pluginsManager->getPlugin('page')->getIsDefaultPlugin()) {
            show::msg(lang::get('page.no-homepage-defined'), 'warning');
        }

        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('page', 'list');

        $tpl->set('token', $this->user->token);
        $tpl->set('page', $page);
        $tpl->set('lost', $lost);
        
        $response->addTemplate($tpl);
        return $response;
    }

    public function delete($id, $token) {
        if (!$this->user->isAuthorized()) {
            return $this->list();
        }
        $page = new page();
        $pageItem = $page->create($id);
        if ($page->del($pageItem)) {
            show::msg(lang::get('core-item-deleted'), 'success');
        } else {
            show::msg(lang::get('core-item-not-deleted'), 'error');
        }
        return $this->list();
    }

    public function maintenance($id, $token) {
        $page = new page();
        $ids = explode(',', $id);
        foreach ($ids as $k => $v) {
            if ($v != '') {
                $pageItem = $page->create($v);
                $page->del($pageItem);
            }
        }
        return $this->list();
    }

    public function pageUp($id, $token) {
        if (!$this->user->isAuthorized()) {
            return $this->list();
        }
        $page = new page();
        $pageItem = $page->create($id);
        $newPos = $pageItem->getPosition() - 1.5;
        $pageItem->setPosition($newPos);
        $page->save($pageItem);
        $page = new page();
        return $this->list();
    }

    public function pageDown($id, $token) {
        if (!$this->user->isAuthorized()) {
            return $this->list();
        }
        $page = new page();
        $pageItem = $page->create($id);
        $newPos = $pageItem->getPosition() + 1.5;
        $pageItem->setPosition($newPos);
        $page->save($pageItem);
        $page = new page();
        return $this->list();
    }

    public function new() {
        $page = new page();
        $pageItem = new pageItem();
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('page', 'edit');

        $contentEditor = new Editor('pageContent', '', lang::get('page.content'), true);
        $tpl->set('page', $page);
        $tpl->set('token', $this->user->token);
        $tpl->set('pageItem', $pageItem);
        $tpl->set('contentEditor', $contentEditor);
        
        $response->addTemplate($tpl);
        return $response;
    }

    public function newLink() {
        $page = new page();
        $pageItem = new pageItem();
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('page', 'edit-link');
        $pageItem->setTarget('url');
        $tpl->set('page', $page);
        $tpl->set('token', $this->user->token);
        $tpl->set('pageItem', $pageItem);
        
        $response->addTemplate($tpl);
        return $response;
    }

    public function newParent() {
        $pageItem = new pageItem();
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('page', 'edit-parent');
        $pageItem->setTarget('parent');

        $tpl->set('token', $this->user->token);
        $tpl->set('pageItem', $pageItem);
        
        $response->addTemplate($tpl);
        return $response;
    }

    public function edit($id) {
        $page = new page();
        $pageItem = $page->create($id);
        if (!$pageItem) {
            return $this->list();
        }
        $response = new AdminResponse();
        if ($pageItem->targetIs() === 'url' || $pageItem->targetIs() === 'plugin') {
            $tpl = $response->createPluginTemplate('page', 'edit-link');
        } elseif ($pageItem->targetIs() === 'parent') {
            $tpl = $response->createPluginTemplate('page', 'edit-parent');
        } else {
            $tpl = $response->createPluginTemplate('page', 'edit');
            $contentEditor = new Editor('pageContent', $pageItem->getContent(), lang::get('page.content'), true);
            $tpl->set('contentEditor', $contentEditor);
        }
        $tpl->set('page', $page);
        $tpl->set('token', $this->user->token);
        $tpl->set('pageItem', $pageItem);
        
        $response->addTemplate($tpl);
        return $response;
    }

    public function save() {
        if (!$this->user->isAuthorized()) {
            return $this->list();
        }
        $page = new page();
        $contentEditor = new Editor('pageContent', '', lang::get('page.content'), true);
        $imgId = (isset($_POST['delImg'])) ? '' : $_REQUEST['imgId'] ?? '';
        if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
            if ($this->pluginsManager->isActivePlugin('galerie')) {
                $galerie = new galerie();
                $img = new galerieItem(array('category' => ''));
                $img->setTitle($_POST['name'] . ' (' . lang::get('galerie.featured-image') . ')');
                $img->setHidden(1);
                $galerie->saveItem($img);
                $imgId = $galerie->getLastId() . '.' . util::getFileExtension($_FILES['file']['name']);
            }
        }
        if ($_POST['id'] != '')
            $pageItem = $page->create($_POST['id']);
        else
            $pageItem = new pageItem();
        $pageItem->setName($_POST['name']);
        $pageItem->setPosition($_POST['position']);
        $pageItem->setIsHomepage((isset($_POST['isHomepage'])) ? 1 : 0);
        $pageItem->setContent($contentEditor->getPostContent());
        $pageItem->setFile((isset($_POST['file'])) ? $_POST['file'] : '');
        $pageItem->setIsHidden((isset($_POST['isHidden'])) ? 1 : 0);
        $pageItem->setMainTitle((isset($_POST['mainTitle'])) ? $_POST['mainTitle'] : '');
        $pageItem->setMetaDescriptionTag((isset($_POST['metaDescriptionTag'])) ? $_POST['metaDescriptionTag'] : '');
        $pageItem->setMetaTitleTag((isset($_POST['metaTitleTag'])) ? $_POST['metaTitleTag'] : '');
        $pageItem->setTarget((isset($_POST['target'])) ? $_POST['target'] : '');
        $pageItem->setTargetAttr((isset($_POST['targetAttr'])) ? $_POST['targetAttr'] : '');
        $pageItem->setNoIndex((isset($_POST['noIndex'])) ? 1 : 0);
        $pageItem->setParent((isset($_POST['parent'])) ? $_POST['parent'] : '');
        $pageItem->setCssClass($_POST['cssClass']);
        $pageItem->setImg($imgId);
        if (isset($_POST['_password']) && $_POST['_password'] != '')
            $pageItem->setPassword($_POST['_password']);
        if (isset($_POST['resetPassword']))
            $pageItem->setPassword('');
        if ($page->save($pageItem))
            show::msg(lang::get('core-changes-saved'), 'success');
        else
            show::msg(lang::get('core-changes-not-saved'), 'error');
        header('location:.?p=page&action=edit&id=' . $pageItem->getId());
        die();
    }
}