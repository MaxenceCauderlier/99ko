<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

abstract class CategoriesManager {

  
    /**
     * plugin ID, like 'blog'
     * @var string
     */
    protected string $pluginId;

    /**
     * Name of categories, like 'categories' or 'tags'
     * @var string
     */
    protected string $name;

    /**
     * className is the name of class who is be used to create new categories, like 'Category' or 'BlogCategory'
     * @var string
     */
    protected string $className;

    /**
     * Categories can be nested or not. For example, for 'tags', $nested will be set to false
     * @var bool
     */
    protected bool $nested;

    /**
     * If user can choose multiple categories for an item
     * @var bool
     */
    protected bool $chooseMany;

    protected $categories;

    protected $imbricatedCategories;

    protected static $file;

    public function __construct() {
        self::$file = DATA_PLUGIN . $this->pluginId . '/categories-' . $this->name . '.json';
        $this->getCategoriesFromMetas();
    }

    public function getCategory(int $id) {
        if (isset($this->categories[$id])) {
            return clone $this->categories[$id];
        }
        return false;
    }
    
    public function getCategories() {
        return $this->categories;
    }
    
    public function getNestedCategories() {
        return $this->imbricatedCategories;
    }

    public function isCategoryExist(int $categoryId) {
        return (isset($this->categories[$categoryId]));
    }

    public function createCategory($label, int $parentId) {
        $cat = new $this->className();
        $cat->label = $label;
        $cat->parentId = $parentId;
        $cat->id = $this->findNextId();
        $this->categories[$cat->id] = $cat;
        if ($parentId !== 0) {
            // Have a parent
            array_push($this->categories[$parentId]->childrenId, $cat->id);
            $this->categories[$parentId]->childrenId = array_values(array_unique($this->categories[$parentId]->childrenId));
        }
        $this->imbricateCategories();
        $this->saveCategories();
        return $cat->id;
    }

    abstract public function getAddCategoryUrl():string;

    abstract public function getDeleteUrl():string;

    abstract public function getAjaxDisplayListUrl():string;

    public function saveCategory(Category $category) {
        $oldCategory = $this->categories[$category->id];
        if ($oldCategory->parentId !== 0) {
            // We will modify old parent
            $key = array_search($category->id, $this->categories[$oldCategory->parentId]->childrenId, true);
            if ($key !== false) {
                // Our categorie is here, we delete it
                unset($this->categories[$oldCategory->parentId]->childrenId[$key]);
            }
        }
        if ($category->parentId !== 0) {
            // We will register the categorie in the new parent
            array_push($this->categories[$category->parentId]->childrenId, $category->id);
            $this->categories[$category->parentId]->childrenId = array_values(array_unique($this->categories[$category->parentId]->childrenId));
        }
        $this->categories[$category->id] = $category;
        $this->imbricateCategories();
        $this->saveCategories();
        return true;
    }

    public function getCategoriesWhereItem(int $itemId) {
        $cats = [];
        foreach ($this->categories as $cat) {
            if (in_array($itemId, $cat->items)) {
                $cats[$cat->id] = $cat;
            }
        }
        return $cats;
    }

    public function deleteCategory($id) {
        if (!isset($this->categories[$id])) {
            return false;
        }
        $cat = $this->categories[$id];
        foreach ($cat->childrenId as $childId) {
            // Childs Categories are affected to the category deleted parent
            $this->categories[$childId]->parentId = $cat->parentId;
        }
        if ($cat->parentId !== 0) {
            // Have a parent, we delete the category in here
            $key = array_search($id, $this->categories[$cat->parentId]->childrenId, true);
            if ($key !== false) {
                // Our category is here, we delete it
                unset($this->categories[$cat->parentId]->childrenId[$key]);
                // And we add the children in the parent
                array_push($this->categories[$cat->parentId]->childrenId, $cat->childrenId);
                $this->categories[$cat->parentId]->childrenId = array_values(array_unique($this->categories[$cat->parentId]->childrenId));
            }
        }
        core::getInstance()->callHook('categoriesDeleteCategory', [$id, $this->pluginId]);
        //Delete the categorie
        unset($this->categories[$id]);
        $this->imbricateCategories();
        $this->saveCategories();
        return true;
    }

    protected function saveCategories() {
        $metas = [];
        foreach ($this->categories as $cat) {
            $metas[$cat->id] = $cat;
        }
        util::writeJsonFile(self::$file, $metas);
    }

    protected function getCategoriesFromMetas() {
        $this->categories = [];
        if (!is_file(self::$file)) {
            return [];
        }
        $metas = util::readJsonFile(self::$file);
        foreach ($metas as $k => $v) {
            $this->categories[$k] = new $this->className($k);
        }
        // Order categories by label ASC
        uasort($this->categories, function($a, $b) {
            return strcmp($a->label, $b->label);
        });
        $this->imbricateCategories();
    }

    protected function imbricateCategories() {
        if ($this->nested === false) {
            return;
        }
        $categories = $this->categories;
        foreach ($categories as $k => $category) {
            if ($category->parentId != 0) {
                foreach ($this->categories as $cat) {
                    $res = $cat->getCategoryById($category->parentId);
                    if (is_object($res)) {
                        $res->addChild($category);
                        unset($categories[$k]);
                        break 1;
                    }
                }
            }
        }
        $this->imbricatedCategories = $categories;
        $this->calcDepth($this->imbricatedCategories, 0);
    }
    
    protected function calcDepth(&$categories, int $depth = 0) {
        foreach ($categories as &$cat) {
            $cat->depth = $depth;
            if ($cat->hasChildren) {
                $this->calcDepth($cat->children, $depth + 1);
            }
        }
    }

    public function outputAsCheckbox($itemId = 0) {
        $catDisplay = 'root';
        ob_start();
        require COMMON . 'categories/template/checkboxCategories.php';
        return ob_get_clean();
    }

    public function outputAsSelect($parentId, $categoryId, $fieldName = "parentId") {
        $catDisplay = 'root';
        ob_start();
        require COMMON . 'categories/template/selectCategory.php';
        return ob_get_clean();
    }
    
    public function outputAsSelectOne($itemId, $fieldName = 'category-one') {
        $catDisplay = 'root';
        ob_start();
        require COMMON . 'categories/template/selectOneCategory.php';
        return ob_get_clean();
    }

    public function outputAsList() {
        $catDisplay = 'root';
        ob_start();
        require COMMON . 'categories/template/listCategories.php';
        return ob_get_clean();
    }

    public function getPrefix() {
        return $this->pluginId . '.' . $this->name . '.';
    }

    public static function getAllCategoriesPluginId() {
        $metas = self::getMetas();
        return array_keys($metas);
    }

    public static function saveItemToCategories($itemId, $categoriesId) {
        $metas = self::getMetas();
        if (!is_array($categoriesId)) {
            $categoriesId = [$categoriesId];
        }
        $categories = [];
        foreach ($metas as $cat) {
            $key = array_search($itemId, $cat['items'], true);
            if ($key !== false) {
                // Item is here. We delete it before see if it is in categorie anymore
                unset($cat['items'][$key]);
            }
            if (in_array($cat['id'], $categoriesId)) {
                // Categorie has been checked
                array_push($cat['items'], $itemId);
                $cat['items'] = array_values(array_unique($cat['items']));
            }
            $categories[$cat['id']] = $cat;
        }
        util::writeJsonFile(self::$file, $categories);
    }
    
    
    public static function deleteItemFromCategories($itemId, $categoriesId) {
        $metas = self::getMetas();
        if (!is_array($categoriesId)) {
            $categoriesId = [$categoriesId];
        }
        $categories = [];
        foreach ($metas as $cat) {
            $key = array_search($itemId, $cat['items'], true);
            if ($key !== false) {
                // Item is here. We delete it
                unset($cat['items'][$key]);
            }
            $categories[$cat['id']] = $cat;
        }
        util::writeJsonFile(self::$file, $categories);
    }

    protected static function getMetas() {
        if (!is_file(self::$file)) {
            return [];
        }
        return util::readJsonFile(self::$file);
    }

    protected function findNextId(): int {
        if (!file_exists(self::$file)) {
            return 1;
        }
        $cats = util::readJsonFile(self::$file);
        if (empty($cats)) {
            return 1;
        }
        return max(array_column($cats, 'id')) + 1;
    }

}
