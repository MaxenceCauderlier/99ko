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
        if (empty($this->imbricatedCategories)) {
            $noCategoriesText = lang::get('blog.categories.none');
            $addCategoryLink = '<a href="index.php?p=categories&plugin=' . $this->pluginId . '">' . lang::get('blog.categories.addOne') . '</a>';
            echo $noCategoriesText . ' ' . $addCategoryLink;
            return;
        }
        ?>
        <select name="<?php echo $fieldName; ?>" id="<?php echo $fieldName; ?>">
            <option value="0">{{ Lang.blog.categories.none }}</option>
        
        <?php
        foreach ($this->imbricatedCategories as $cat) {
            $cat->outputAsSelectOne($itemId);
        }
        ?>
        </select>
        <?php
        break;
    case 'sub':
        // Categories
        ?>
        <option value="<?php echo $this->id; ?>" <?php if (in_array($itemId, $this->items)) echo ' selected'; ?> >
            <?php echo str_repeat("-", ($this->depth * 2)) . ' ' . $this->label; ?>
        </option>
        <?php if ($this->hasChildren) {
            foreach ($this->children as $child) {
                $child->outputAsSelectOne($itemId);
            }
        }
        break;
}