<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

/**
 * Editor is a class to simplify how plugins works with textarea and the editor
 */
class Editor {
    
    /**
     * Textarea ID
     * @var string Unique ID (will be the name too)
     */
    protected $id;
    
    /**
     * Display a label before the textarea
     * @var string Text of the textarea label
     */
    protected $label;
    
    /**
     * Textarea Content.
     * See constructor, setContent and getPostContent to set and get it.
     * @var string Textarea Content
     */
    protected $content;

    /**
     * Display a button to pick files into editor
     */
    protected bool $displayFileManagerButton;
    
    /**
     * Core is only used to call hooks
     * @var \core 299Ko Core
     */
    protected $core;

    protected static int $nbInstances = 0;

    /**
     * Construct a new textarea editor
     * 
     * @param string ID
     * @param string Content (facultative)
     * @param string label (facultative)
     * @param bool Display Button to pick files into editor
     */
    public function __construct($id, $content = '', $label = false, $displayFileManagerButton = true) {
        $this->id = $id ;
        $this->label = $label;
        $this->core = core::getInstance();
        $this->setContent($content);
        $this->displayFileManagerButton = $displayFileManagerButton;
    }
    
    /**
     * Allow to display the editor like this : echo $editor;
     * 
     * @return string
     */
    public function __toString() {
        if (self::$nbInstances === 0) {
            // First Editor displayed
            $this->core->callHook('insertCodeBeforeFirstEditor');
        }
        $str = '';
        if ($this->label) {
            $str.= '<label for="' . $this->id . '">' . $this->label . '</label><br>';
        }
        $str .= '<textarea name="' . $this->id . '" id="' . $this->id . '" class="editor">'. $this->content . '</textarea><br/>';
        if ($this->displayFileManagerButton) {
            $str .= filemanagerDisplayManagerButton($this->id);
        }
        self::$nbInstances++;
        return $str;
    }
    
    /**
     * Get the textarea content as it was posted, after the core hooks.
     * Return false if no POST data
     * 
     * @return mixed Textarea Posted Content
     */
    public function getPostContent() {
        if (!isset($_POST[$this->id])) {
            return false;
        }
        return $this->core->callHook('beforeSaveEditor', htmlspecialchars($_POST[$this->id]));
    }
    
    /**
     * Re-set the textarea content. Core hooks are called after setting content.
     * 
     * @param string Content
     */
    public function setContent($content) {
        $this->content = $this->core->callHook('beforeEditEditor', $content);
    }
}