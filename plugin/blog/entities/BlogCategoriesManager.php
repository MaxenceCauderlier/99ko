<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

class BlogCategoriesManager extends CategoriesManager {

    protected string $pluginId = 'blog';
    protected string $name = 'categories';
    protected string $className = 'BlogCategory';
    protected bool $nested = true;
    protected bool $chooseMany = true;

    public function getAddCategoryUrl():string {
        return router::getInstance()->generate('admin-blog-add-category');
    }

    public function getDeleteUrl() :string {
        return router::getInstance()->generate('admin-blog-delete-category');
    }

    public function getAjaxDisplayListUrl():string {
        return router::getInstance()->generate('admin-blog-list-ajax-categories');
    }

    public function getEditUrl():string {
        return router::getInstance()->generate('admin-blog-edit-category');
    }

    public function outputAsList() {
        echo '<section id="categories_panel">';
        echo '<header>'. lang::get('blog-categories-management-title').'</header>';
        echo parent::outputAsList();
        echo '</section>';
    }
}