<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

class Folder {
    
    public string $name = '';
    
    protected string $directory = '';
    
    public function __construct($name, $directory) {
        $this->name = $name;
        $this->directory = trim($directory, '/') . '/';
    }
    
    public function delete() {
        if (!is_dir($this->directory . $this->name)) {
            return false;
        }
        $manager = new FileManager($this->directory . $this->name);
        
        $error = $manager->deleteAllFiles();
        if ($error) {
            return false;
        }
        
        $error = $manager->deleteAllFolders();
        if ($error) {
            return false;
        }
        
        return rmdir($this->directory . $this->name);        
    }
    
}