<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

abstract class Category implements JsonSerializable {

    public $id;
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
     * Categories can be nested or not. For example, for 'tags', $nested will be set to false
     * @var bool
     */
    protected bool $nested;

    /**
     * If user can choose multiple categories for an item
     * @var bool
     */
    protected bool $chooseMany;
    public $items = [];
    public $label = '';
    public int $parentId;
    public array $childrenId = [];
    public array $children = [];
    public bool $isChild = false;
    public bool $hasChildren = false;
    public int $depth = 0;
    
    public array $pluginArgs = [];

    protected static string $file;

    public function __construct(int $id = -1) {
        $this->id = $id;
        self::$file = DATA_PLUGIN . $this->pluginId . '/categories-' . $this->name . '.json';
        if ($this->id !== -1) {
            $metas = util::readJsonFile(self::$file);
            $this->items = $metas[$this->id]['items'] ?? [];
            $this->label = $metas[$this->id]['label'];
            $this->childrenId = $metas[$this->id]['childrenId'];
            $this->parentId = $metas[$this->id]['parentId'];
            $this->pluginArgs = $metas[$this->id]['pluginArgs'] ?? [];
        }
    }


    public function jsonSerialize():array {
        return
                ['items' => $this->items,
                    'label' => $this->label,
                    'id' => $this->id,
                    'parentId' => $this->parentId,
                    'childrenId' => $this->childrenId,
                    'pluginArgs' => $this->pluginArgs];
    }

    public function outputAsCheckbox($itemId) {
        $catDisplay = 'sub';
        require COMMON . 'categories/template/checkboxCategories.php';
    }
    
    public function outputAsSelect($parentId, $categorieId) {
        $catDisplay = 'sub';
        require COMMON . 'categories/template/selectCategory.php';
    }
    
    public function outputAsSelectOne($itemId) {
        $catDisplay = 'sub';
        require COMMON . 'categories/template/selectOneCategory.php';
    }

    public function outputAsList() {
        $catDisplay = 'sub';
        require COMMON . 'categories/template/listCategories.php';
    }

    public function getCategoryById(int $id) {
        if ($id === $this->id) {
            // We search this categorie
            return $this;
        }
        if (empty($this->children)) {
            // No child
            return false;
        }
        foreach ($this->children as $parent) {
            // Search in childs
            $res = $parent->getCategoryById($id);
            if (is_object($res) && get_class($res) === get_class($this)) {
                return $res;
            }
        }
        return false;
    }

    public function addChild(Category $category) {
        $category->isChild = true;
        $this->hasChildren = true;
        $this->children[$category->id] = $category;
    }

    public function getPrefix() {
        return $this->pluginId . '.' . $this->name . '.';
    }

}
