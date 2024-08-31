<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

abstract class Controller {
    
    /**
     * Core instance
     * @var core
     */
    protected core $core;
    
    /**
     * Router instance
     * @var router
     */
    protected router $router;

    /**
     * pluginsManager instance
     * @var pluginsManager
     */
    protected pluginsManager $pluginsManager;

    /**
     * JSON data sent by fetch, used for API
     * @var array
     */
    protected array $jsonData = [];
    
    public function __construct() {
        $this->core = core::getInstance();
        $this->router = router::getInstance();
        $this->pluginsManager = pluginsManager::getInstance();
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if ($contentType === "application/json") {
            $content = trim(file_get_contents("php://input"));
            $this->jsonData = json_decode($content, true);
        }
    }
}