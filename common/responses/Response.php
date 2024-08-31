<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

abstract class Response {

    /**
     * Templates array
     * @var array Template
     */
    protected array $templates = [];

    abstract public function output() : string;

    /**
     * Construct
     */
    public function __construct() {
        
    }

    /**
     * Add a Template in content
     * @param Template $template
     * @return void
     */
    public function addTemplate(Template $template) {
        $this->templates[] = $template;
    }

    
}