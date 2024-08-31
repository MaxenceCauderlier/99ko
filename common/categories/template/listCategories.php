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
            <div class="list-item-list">
                <div><?php echo lang::get($this->getPrefix() . 'name'); ?></div>
                <div><?php echo lang::get($this->getPrefix() . 'itemsNumber'); ?></div>
                <div><?php echo lang::get($this->getPrefix() . 'actions'); ?></div>
            </div>
            <?php
            if (empty($this->imbricatedCategories)) {
                echo '<div class="list-item-list">' . lang::get($this->getPrefix() . 'none') .'</div>';
            } else {
                foreach ($this->imbricatedCategories as $cat) {
                    $cat->outputAsList();
                }
            }
            ?>
        </div>
        <footer id="list-item-endlist">
            <div id="categorie-add-form-container">
                <h4 id="head-add-cat"><?php echo lang::get($this->getPrefix() . 'addCategory'); ?></h4>
                <?php show::tokenField(); ?> 
                <div class="input-field">
                    <label for="category-list-add-label"><?php echo lang::get($this->getPrefix() . 'categoryName'); ?></label>
                    <input type="text" name="category-list-add-label" id="category-list-add-label" required/>
                    <label for="categoy-list-add-parentId"><?php echo lang::get($this->getPrefix() . 'categoryParent'); ?></label>
                    <select name="category-list-add-parentId" id="category-list-add-parentId">
                        <option value="0"><?php echo lang::get($this->getPrefix() . 'none'); ?></option>
                        <?php
                        if (!empty($this->imbricatedCategories)) {
                            foreach($this->imbricatedCategories as $cat) {
                                $cat->outputAsSelectOne(0);
                            }
                        }
                        ?>
                    </select>
                </div>
                <button onclick="BlogAddCategory()" id="list-item-add-btn"><?php echo lang::get($this->getPrefix() . 'addCategory'); ?></button>
            </div>
        </footer>
        <script>
            function BlogEditCategoryDisplay(id) {
                let data = "id=" + id + "&token=" + '<?php echo UsersManager::getCurrentUser()->token; ?>';
                new Fancybox([
                {
                    src: '<?php echo $this->getEditUrl(); ?>',
                    type: "ajax",
                    ajax : data
                },
                ],);
            };

            async function BlogAddCategory() {
                let url = '<?php echo $this->getAddCategoryUrl(); ?>';
                let data = {
                    label: document.querySelector('#category-list-add-label').value,
                    parentId: document.querySelector('#category-list-add-parentId').value,
                    token: '<?php echo UsersManager::getCurrentUser()->token; ?>'
                };
                let response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                let result = await response;
                    if (result.status === 201) {
                    Toastify({
                        text: "<?php echo lang::get('core-item-added'); ?>",
                        className: "success"		
                    }).showToast();
                    // Refresh list
                    Fancybox.close();
                    Fancybox.show([
                        {
                            src: "<?php echo $this->getAjaxDisplayListUrl(); ?>",
                            type: "ajax",
                        },
                    ]);
                } else {
                    Toastify({
                        text: "<?php echo lang::get('core-item-not-added'); ?>",
                        className: "error"		
                    }).showToast();
                }	
            };

            async function BlogDeleteCategory(id) {
                if (confirm('<?php echo lang::get('confirm.deleteItem'); ?>') === true) {
                    let url = '<?php echo $this->getDeleteUrl(); ?>';
                    let data = {
                        id: id,
                        token: '<?php echo UsersManager::getCurrentUser()->token; ?>'
                    };
                    let response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });
                    
                    let result = await response;
                     if (result.status === 204) {
                        Toastify({
                            text: "<?php echo lang::get('core-item-deleted'); ?>",
                            className: "success"		
                        }).showToast();
                        // Refresh list
                        Fancybox.close();
                        Fancybox.show([
                            {
                                src: "<?php echo $this->getAjaxDisplayListUrl(); ?>",
                                type: "ajax",
                            },
                        ]);
                    } else {
                        Toastify({
                            text: "<?php echo lang::get('core-item-not-deleted'); ?>",
                            className: "error"		
                        }).showToast();
                    }		
                };
            }
            function CategoriesDeployChild(item) {
                nextToggle = item.parentNode.nextSibling;
                item.classList.toggle('rotate-180');
                nextToggle.slideToggle(400);
            }
        </script>
        <?php
        break;
    case 'sub':
        // Categories
        echo '<div id="category-' . $this->id . '" class="list-item-list ';
        if ($this->hasChildren) {
            echo 'hasChildren"><i style="left:' . ($this->depth * 15 + 5 ) . 'px;" onclick="CategoriesDeployChild(this)" class="fa-solid fa-chevron-up list-item-toggle" title="'.lang::get($this->getPrefix() . 'collapseExpandChildren').'"></i>';
        } else {
            echo '">';
        }
        echo '<div style="padding-left:' . ($this->depth * 15 + 10) . 'px;">' . str_repeat("-", ($this->depth * 2)) . ' ' . $this->label . '</div>';
        echo '<div>' . count($this->items) . '</div>';
        echo '<div role="group">';
        echo '<a class="button small" title="'.lang::get($this->getPrefix() . 'editCategory').'" onclick="BlogEditCategoryDisplay(\'' . $this->id . '\')"" ><i class="fa-solid fa-pencil"></i></a>';
        echo '<a class="button alert small" title="'.lang::get($this->getPrefix() . 'deleteCategory').'" onclick="BlogDeleteCategory('. $this->id .')"><i class="fa-solid fa-folder-minus"></i></a></div>';
        echo '</div>';
        if ($this->hasChildren) {
            echo '<div class="toggle">';
            foreach ($this->children as $child) {
                $child->outputAsList();
            }
            echo '</div>';
        }
        break;
}

    