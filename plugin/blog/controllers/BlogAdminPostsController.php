<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('Access denied!');

class BlogAdminPostsController extends AdminController
{

    public BlogCategoriesManager $categoriesManager;

    public newsManager $newsManager;

    public function __construct()
    {
        parent::__construct();
        $this->categoriesManager = new BlogCategoriesManager();
        $this->newsManager = new newsManager();
    }

    public function list()
    {
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('blog', 'admin-list');

        $tpl->set('newsManager', $this->newsManager);
        $tpl->set('token', $this->user->token);

        $response->addTemplate($tpl);
        return $response;
    }

    public function deletePost()
    {
        $response = new ApiResponse();
        if (!$this->user->isAuthorized()) {
            $response->status = ApiResponse::STATUS_NOT_AUTHORIZED;
            return $response;
        }
        $newsManager = new newsManager();
        $id = (int) $this->jsonData['id'] ?? 0;
        $item = $newsManager->create($id);
        if (!$item) {
            $response->status = ApiResponse::STATUS_NOT_FOUND;
            return $response;
        }
        $title = $item->getName();
        if ($newsManager->delNews($item)) {
            logg($this->user->email . ' deleted post ' . $title);
            $response->status = ApiResponse::STATUS_NO_CONTENT;
        } else {
            $response->status = ApiResponse::STATUS_BAD_REQUEST;
        }
        return $response;
    }

    public function editPost($id = false)
    {
        if ($id === false) {
            $news = new news();
            $showDate = false;
        } else {
            $news = $this->newsManager->create($id);
            $showDate = true;
            if ($news === false) {
                // News id dont exist
                show::msg(lang::get('blog-item-dont-exist'), 'error');
                $this->core->redirect($this->router->generate('admin-blog-list'));
            }
        }
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('blog', 'admin-edit');

        $contentEditor = new Editor('blogContent', $news->getContent(), lang::get('blog-content'));

        $tpl->set('contentEditor', $contentEditor);
        $tpl->set('news', $news);
        $tpl->set('news', $news);
        $tpl->set('showDate', $showDate);
        $tpl->set('categoriesManager', $this->categoriesManager);

        $response->addTemplate($tpl);
        return $response;
    }

    public function savePost()
    {
        if (!$this->user->isAuthorized()) {
            return $this->list();
        }
        $imgId = (isset($_POST['delImg'])) ? '' : $_REQUEST['imgId'];
        if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
            if ($this->pluginsManager->isActivePlugin('galerie')) {
                $galerie = new galerie();
                $img = new galerieItem(array('category' => ''));
                $img->setTitle($_POST['name'] . ' ('.lang::get('blog-featured-img').')');
                $img->setHidden(1);
                $galerie->saveItem($img);
                $imgId = $galerie->getLastId() . '.' . util::getFileExtension($_FILES['file']['name']);
            }
        }
        $contentEditor = new Editor('blogContent', '', lang::get('blog-content'));


        $news = ($_REQUEST['id']) ? $this->newsManager->create($_REQUEST['id']) : new news();
        $news->setName($_REQUEST['name']);
        $news->setContent($contentEditor->getPostContent());
        $news->setIntro($this->core->callHook('beforeSaveEditor', htmlspecialchars($_REQUEST['intro'])));
        $news->setSEODesc($_REQUEST['seoDesc']);
        $news->setDraft((isset($_POST['draft']) ? 1 : 0));
        if (!isset($_REQUEST['date']) || $_REQUEST['date'] == "")
            $news->setDate($news->getDate());
        else
            $news->setDate($_REQUEST['date']);
        $news->setImg($imgId);
        $news->setCommentsOff((isset($_POST['commentsOff']) ? 1 : 0));
        if ($this->newsManager->saveNews($news)) {
            $choosenCats = [];
            if (isset($_POST['categoriesCheckbox'])) {
                foreach ($_POST['categoriesCheckbox'] as $cat) {
                    $choosenCats[] = (int) $cat;
                }
            }
            $label = filter_input(INPUT_POST, 'category-add-label', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if ($label !== '') {
                $parentId = filter_input(INPUT_POST, 'category-add-parentId', FILTER_VALIDATE_INT) ?? 0;
                $choosenCats[] = $this->categoriesManager->createCategory($label, $parentId);
            }
            BlogCategoriesManager::saveItemToCategories($news->getId(), $choosenCats);
            show::msg(lang::get('core-changes-saved'), 'success');
        } else {
            show::msg(lang::get('core-changes-not-saved'), 'error');
        }
        $this->core->redirect($this->router->generate('admin-blog-edit-post', ['id' => $news->getId()]));
    }
}