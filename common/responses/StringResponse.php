<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

class StringResponse extends Response {

    /**
     * Current theme name
     * @var string
     */
    protected string $themeName;

    public function __construct() {
        parent::__construct();
        $this->themeName = core::getInstance()->getConfigVal('theme');
    }

    /**
     * Return the response
     * @return string Content of all template
     */
    public function output():string
    {
        $content = '';
        foreach ($this->templates as $tpl) {
            $content .= $tpl->output();
        }
        return $content;
    }

    /**
     * Create a new Template, from plugin
     * Eg : if plugin is 'blog' & asked template is 'read', look for 'THEMES/theme/blog.read.tpl'
     * else create tpl with PLUGINS/blog/template/read.tpl
     * @param string $pluginName
     * @param string $templateName
     * @return Template
     */
    public function createPluginTemplate(string $pluginName, string $templateName):Template {
        $themeFile = THEMES . $this->themeName . '/' . $pluginName . '.' . $templateName . '.tpl';
        if (file_exists($themeFile)) {
            $tpl = new Template($themeFile);
        } else {
            $tpl = new Template(PLUGINS . $pluginName .'/template/' . $templateName . '.tpl');
        }
        return $tpl;
    }

}