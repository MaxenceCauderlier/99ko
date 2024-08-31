<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('Access denied!');

class FileManagerAPIController extends AdminController {

    protected ?string $dir = null;

    protected array $dirParts = [];

    protected string $fullDir = '';

    protected bool $ajaxView = false;

    protected bool $api = false;

    protected FileManager $filemanager;

    protected $editor = false;

    public function home() {
        return $this->render();
    }

    public function view() {
        return $this->render();
    }

    public function upload($token) {
        if (!$this->user->isAuthorized()) {
            echo json_encode(['success' => 0]);
            die();
        }
        $this->getSentDir();
        $this->filemanager = new FileManager($this->fullDir);
        if (isset($_FILES['image']['name']) != '') {
            $image = $_FILES['image']['name'];
            if ($this->filemanager->uploadFile('image') !== false) {
                echo json_encode(['success' => 1]);
                die();
            } else {
                echo json_encode(['success' => 0]);
                die();
            }
        }
    }

    public function uploadAPI($token) {
        if (!$this->user->isAuthorized()) {
            header("HTTP/1.1 500 Server Error");
            die();
        }
        $temp = current($_FILES);
        if (is_uploaded_file($temp['tmp_name'])) {
            // Sanitize input
            if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
                header("HTTP/1.1 400 Invalid file name.");
                return;
            }

            // Verify extension
            if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), 
                    ["gif", "jpg", "png", "ico", "bmp", "jpeg"])) {
                header("HTTP/1.1 400 Invalid extension.");
                return;
            }

            $tinyManager = new FileManager(UPLOAD . 'files/API');
            
            $uploaded = $tinyManager->uploadFile('file');
            if ($uploaded !== false) {
                echo json_encode(['location' => $uploaded]);
                die();
            }

            $imageFolder = UPLOAD;

            $filetowrite = $imageFolder . $temp['name'];
            move_uploaded_file($temp['tmp_name'], $filetowrite);

            $baseurl = util::urlBuild('');

            echo json_encode(['location' => $baseurl . $filetowrite]);
            die();
        }
        header("HTTP/1.1 500 Server Error");
        die();
    }

    public function delete($token) {
        if (!$this->user->isAuthorized()) {
            echo json_encode(['success' => 0]);
            die();
        }
        $this->getSentDir();
        $this->filemanager = new FileManager($this->fullDir);
        if (isset($_POST['filename'])) {
            // Delete File
            $deleted = $this->filemanager->deleteFile($_POST['filename']);
            echo json_encode(['success' => $deleted]);
            die();
        } elseif (isset($_POST['foldername'])) {
            $deleted = $this->filemanager->deleteFolder($_POST['foldername']);
            echo json_encode(['success' => $deleted]);
            die();
        }
    }

    public function create($token) {
        if (!$this->user->isAuthorized()) {
            echo json_encode(['success' => 0]);
            die();
        }
        $this->getSentDir();
        $this->filemanager = new FileManager($this->fullDir);
        $created = $this->filemanager->createFolder($_POST['folderName']);
        echo json_encode(['success' => $created]);
        die();
    }

    public function viewAjax() {
        if (!$this->user->isAuthorized()) {
            echo json_encode(['success' => 0]);
            die();
        }
        $this->ajaxView = true;
        $this->editor = $_POST['editor'];

        return $this->render();
    }

    public function viewAjaxHome($token, $editor = false) {
        if (!$this->user->isAuthorized()) {
            echo json_encode(['success' => 0]);
            die();
        }
        if ($editor === ''){
            $editor = false;
        }
        $this->editor = $editor;
        $this->ajaxView = true;
        $this->dir = '';
        return $this->render();
    }

    protected function render() {
        $this->getSentDir();
        $this->filemanager = new FileManager($this->fullDir);
        if ($this->ajaxView) {
            $response = new StringResponse();
        } else {
            $response = new AdminResponse();
        }
        $tpl = $response->createPluginTemplate('filemanager', 'listview');

        $tpl->set('token', $this->user->token);
        $tpl->set('dir', $this->dir);
        $tpl->set('dirParts', $this->dirParts);
        $tpl->set('manager', $this->filemanager);
        $tpl->set('ajaxView', $this->ajaxView);
        $tpl->set('uploadUrl', $this->router->generate('filemanager-upload', ['token' => $this->user->token]));
        $tpl->set('deleteUrl', $this->router->generate('filemanager-delete', ['token' => $this->user->token]));
        $tpl->set('createUrl', $this->router->generate('filemanager-create', ['token' => $this->user->token]));
        $tpl->set('redirectUrl', $this->router->generate('filemanager-view'));
        $tpl->set('redirectAjaxUrl', $this->router->generate('filemanager-view-ajax'));
        $tpl->set('editor', $this->editor);

        $response->addTemplate($tpl);
        return $response;
    }

    protected function getSentDir() {
        if (!isset($this->dir)) {
            $this->dir = $_POST['fmFolderToSee'] ?? '';
        }
        if ($this->dir === 'Back%To%Home%') {
            $this->dir = '';
        }
        $this->dir = trim($this->dir, '/');
        if ($this->dir !== '') {
            $this->dirParts = explode('/', $this->dir);
            if (end($this->dirParts) === '..') {
                // Up to parent folder
                array_pop($this->dirParts);
                array_pop($this->dirParts);
            }

            $this->dir = implode('/', $this->dirParts);
        } else {
            $this->dirParts = [];
        }
        $this->fullDir = UPLOAD . 'files/' . trim($this->dir, '/');
    }

}