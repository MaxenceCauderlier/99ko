<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

class File {
    
    public string $name = '';
    
    protected string $directory = '';
    
    public function __construct($name, $directory) {
        $this->name = $name;
        $this->directory = trim($directory, '/') . '/';
    }
    
    public function getUrl() {
        return util::urlBuild($this->directory . $this->name);
    }

    public function getRelUrl() {
        $parts = explode('/', $this->directory);
        $dir = '';
        foreach ($parts as $part) {
            if ($part === '.' || $part === '..' || $part === '') {
                continue;
            }
            $dir .= $part . '/';
        }
        return $dir . ltrim($this->name,'/');
    }
    
    public function isPicture() {
        if (in_array(util::getFileExtension($this->name), ['gif', 'jpg', 'jpeg','png','bmp'])) {
            return true;
        }
    }
    
    public function getFileMTime() {
        return filemtime($this->directory . $this->name);
    }
    
    public function delete() {
        return unlink($this->directory . $this->name);
    }
    
}