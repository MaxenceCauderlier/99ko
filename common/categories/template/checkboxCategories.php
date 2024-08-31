<?php
/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

switch ($catDisplay) {
    case 'root':
        // Categories Container
        ?>
        <div class="list-item-container">
            <?php
            if (empty($this->imbricatedCategories)) {
                echo lang::get($this->getPrefix() . 'none') . ' <a data-fancybox href="#" data-src="#categories_panel">' . lang::get($this->getPrefix() . 'addOne') . '</a>';
            } else { ?>
            <ul class="list-item-list">
                <?php
                foreach ($this->imbricatedCategories as $cat) {
                    $cat->outputAsCheckbox($itemId);
                }
                ?>
            </ul>
            <?php } ?>
        </div>
        <?php
        break;
    case 'sub':
        // Categories
        if ($this->hasChildren) {
            echo "<li class='list-item-hasChildren'>";
        } else {
            echo "<li>";
        }
        echo "<div><input type='checkbox' id='cat_" . $this->id . "' name='categoriesCheckbox[]' value='" . $this->id . "' ";
        
        if (in_array($itemId, $this->items)) {
            echo ' checked';
        }
        echo "/><label for='cat_" . $this->id . "'>" . $this->label . "</label></div>";
        if ($this->hasChildren) {
            echo "<i class='fa-solid fa-caret-down'></i>";
            echo "<ul class='list-item-list-sub'>";
            foreach ($this->children as $child) {
                $child->outputAsCheckbox($itemId);
            }
            echo "</ul>";
        }
        echo "</li>";
        break;
}