<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

class BlogAdminCategoriesController extends AdminController {

    public BlogCategoriesManager $categoriesManager;

    public newsManager $newsManager;

    public function __construct() {
        parent::__construct();
        $this->categoriesManager = new BlogCategoriesManager();
        $this->newsManager = new newsManager();
    }

    public function addCategory() {
        $response = new ApiResponse();
        if (!$this->user->isAuthorized()) {
            $response->status = ApiResponse::STATUS_NOT_AUTHORIZED;
            return $response;
        }
        $label = filter_var($this->jsonData['label'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $parentId = filter_var($this->jsonData['parentId'], FILTER_SANITIZE_NUMBER_INT) ?? 0;
        $this->categoriesManager->createCategory($label, $parentId);
        $response->status = ApiResponse::STATUS_CREATED;
        return $response;
    }

    public function deleteCategory() {
        // Called by Ajax
        $response = new ApiResponse();
        $id = (int) $this->jsonData['id'] ?? 0;
        if (!$this->user->isAuthorized()) {
            $response->status = ApiResponse::STATUS_NOT_AUTHORIZED;
            return $response;
        }
        if ($this->categoriesManager->isCategoryExist($id)) {
            if ($this->categoriesManager->deleteCategory($id)) {
                $response->status = ApiResponse::STATUS_NO_CONTENT;
            } else {
                $response->status = ApiResponse::STATUS_NOT_FOUND;
            }
        }
        return $response;
    }

    public function editCategory() {
        // Called By Fancybox
        if (!$this->user->isAuthorized()) {
            echo 'forbidden';
            die();
        }
        $response = new StringResponse();
        $tpl = $response->createPluginTemplate('blog', 'admin-edit-category');
        $id = (int) $_POST['id'] ?? 0;
        if (!$this->categoriesManager->isCategoryExist($id)) {
            echo 'dont exist';
            die();
        }
        $category = $this->categoriesManager->getCategory($id);

        $tpl->set('categoriesManager', $this->categoriesManager);
        $tpl->set('category', $category);
        $tpl->set('token', $this->user->token);
        $response->addTemplate($tpl);
        return $response;
    }

    public function saveCategory($id) {
        // Called by Ajax
        $response = new ApiResponse();
        if (!$this->user->isAuthorized()) {
            $response->status = ApiResponse::STATUS_NOT_AUTHORIZED;
            return $response;
        }
        $label = filter_var($this->jsonData['label'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $parentId = (int)filter_var($this->jsonData['parentId'], FILTER_SANITIZE_NUMBER_INT) ?? 0;
        if (!$this->categoriesManager->isCategoryExist($id)) {
            $response->status = ApiResponse::STATUS_NOT_FOUND;
            return $response;
        }
        if ($parentId !== 0 && !$this->categoriesManager->isCategoryExist($parentId)) {
            $response->status = ApiResponse::STATUS_BAD_REQUEST;
            return $response;
        }
        $category = $this->categoriesManager->getCategory($id);
        $category->parentId = $parentId;
        $category->label = $label;
        $this->categoriesManager->saveCategory($category);
        $response->status = ApiResponse::STATUS_ACCEPTED;
        return $response;
    }

    public function listAjaxCategories() {
        echo $this->categoriesManager->outputAsList();
        die();
    }


}