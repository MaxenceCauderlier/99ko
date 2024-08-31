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
        <select name="<?php echo $fieldName; ?>" id="<?php echo $fieldName; ?>">
            <option value="0" <?php if ($parentId == 0) echo ' selected'; ?> >-- Pas de catégorie parente</option>
        
        <?php
        foreach ($this->imbricatedCategories as $cat) {
            if ($cat->id == $categoryId) {
                // We dont display itself
                continue;
            }
            $cat->outputAsSelect($parentId, $categoryId);
        }
        ?>
        </select>
        <?php
        break;
    case 'sub':
        // Categories
        ?>
        <option value="<?php echo $this->id; ?>" <?php if ($parentId == $this->id) echo ' selected'; ?> >
            <?php echo str_repeat("-", ($this->depth * 2)) . ' ' . $this->label; ?>
        </option>
        <?php if ($this->hasChildren) {
            foreach ($this->children as $child) {
                if ($child->id == $categorieId) {
                    // We dont display the current categorie xor his children
                    continue;
                }
                $child->outputAsSelect($parentId, $categoryId);
            }
        }
        break;
}