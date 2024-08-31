<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

require_once(PLUGINS . 'filemanager/lib/File.php');
require_once(PLUGINS . 'filemanager/lib/Folder.php');

class FileManager {
    
    /**
     * @var string Current directory
     */
    protected string $directory = '';
    
    /**
     * @var array Children directories
     */
    protected array $subDir = [];

    /**
     * @var array Children files
     */
    protected array $subFiles = [];
    
    public function __construct($directory) {
        $this->directory = trim($directory, '/') . '/';
        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0755);
        }
        $this->hydrateChildren();
    }
    
    protected function hydrateChildren() {
        $fileList = glob($this->directory . "*");
        for ($v = 0; $v < sizeof($fileList); $v++) {
            $name = str_replace($this->directory, "", $fileList[$v]);
            if (is_dir($fileList[$v])) {
                $this->subDir[$name] = new Folder($name, $this->directory);
            } else {
                $this->subFiles[$name] = new File($name, $this->directory);
            }
        }
    }
    
    public function getFolders() {
        return $this->subDir;
    }
    
    public function getFiles() {
        return $this->subFiles;
    }
    
    public function uploadFile($arrayName) {
        $file = $_FILES[$arrayName];
        $fileName = util::strToUrl(pathinfo($file['name'], PATHINFO_FILENAME));
        $fileExt  = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (move_uploaded_file($file['tmp_name'], $this->directory . $fileName . '-' . time() . '.' . $fileExt)) {
            $file = new File($fileName . '-' . time() . '.' . $fileExt, $this->directory);
            return $file->getUrl();
        } else {
            return false;
        }
    }
    
    public function deleteFile($filename) {
        if (isset($this->subFiles[$filename])) {
            $error = $this->subFiles[$filename]->delete();
            if (!$error) {
                unset($this->subFiles[$filename]);
                return true;
            }
        }
        return false;
    }
    
    public function deleteFolder($foldername) {
        if (isset($this->subDir[$foldername])) {
            $error = $this->subDir[$foldername]->delete();
            if (!$error) {
                unset($this->subDir[$foldername]);
                return true;
            }
        }
        return false;
    }
    
    public function deleteAllFiles() {
        $error = false;
        foreach ($this->subFiles as $file) {
            if (!$file->delete()) {
                $error = true;
            }
        }
        return $error;
    }
    
    public function deleteAllFolders() {
        $error = false;
        foreach ($this->subDir as $folder) {
            if (!$folder->delete()) {
                $error = true;
            }
        }
        return $error;
    }
    
    public function createFolder($name) {
        return mkdir($this->directory . $name, 0755);
    }
}
