<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

class BlogAdminCommentsController extends AdminController {

    public newsManager $newsManager;

    public function __construct()
    {
        parent::__construct();
        $this->newsManager = new newsManager();
    }

    public function listComments($id) {
        $this->newsManager->loadComments($id);
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('blog', 'admin-list-comments');

        $tpl->set('newsManager', $this->newsManager);
        $tpl->set('idPost', $id);
        $tpl->set('token', $this->user->token);

        $response->addTemplate($tpl);
        return $response;
    }

    public function deleteComment()
    {
        $response = new ApiResponse();
        if (!$this->user->isAuthorized()) {
            $response->status = ApiResponse::STATUS_NOT_AUTHORIZED;
            return $response;
        }
        $idPost = (int) $this->jsonData['idPost'] ?? 0;
        $idComment = (int) $this->jsonData['idComment'] ?? 0;

        $this->newsManager->loadComments($idPost);
        $comment = $this->newsManager->createComment($idComment);
        if (!$comment) {
            $response->status = ApiResponse::STATUS_NOT_FOUND;
            return $response;
        }
        if ($this->newsManager->delComment($comment)) {
            $response->status = ApiResponse::STATUS_NO_CONTENT;
        } else {
            $response->status = ApiResponse::STATUS_BAD_REQUEST;
        }
        return $response;
    }

    public function saveComment() {
        $response = new ApiResponse();
        $response->body = 'Updated';
        if (!$this->user->isAuthorized()) {
            $response->status = ApiResponse::STATUS_NOT_AUTHORIZED;
            return $response;
        }
        $idPost = (int) $this->jsonData['idPost'] ?? 0;
        $idComment = (int) $this->jsonData['idComment'] ?? 0;
        $content = $this->jsonData['content'] ?? '';
        $this->newsManager->loadComments($idPost);
        $comment = $this->newsManager->createComment($idComment);
        if (!$comment) {
            $response->status = ApiResponse::STATUS_NOT_FOUND;
            return $response;
        }
        $comment->setContent($content);
        if ($this->newsManager->saveComment($comment)) {
            $response->status = ApiResponse::STATUS_ACCEPTED;
            $response->body = 'Updated';
        } else {
            $response->status = ApiResponse::STATUS_BAD_REQUEST;
        }
        return $response;
    }
}