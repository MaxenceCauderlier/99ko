<?php

/**
 * @copyright (C) 2023, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

class BlogCategory extends Category {

    protected string $pluginId = 'blog';
    protected string $name = 'categories';
    protected bool $nested = true;
    protected bool $chooseMany = true;


}