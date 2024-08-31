<?php

/**
 * @copyright (C) 2022, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

class newsManager {

    private $items;
    private $comments;

    /**
     * All comments from a news, non imbricated
     */
    protected $flatComments;

    private int $nbItemsToPublic;

    public function __construct() {
        $categoriesManager = new BlogCategoriesManager();
        $i = 0;
        $data = [];
        if (file_exists(ROOT . 'data/plugin/blog/blog.json')) {
            $temp = util::readJsonFile(ROOT . 'data/plugin/blog/blog.json');
            if (!is_array($temp)) {
                $temp = [];
            }
            $temp = util::sort2DimArray($temp, 'date', 'desc');
            foreach ($temp as $k => $v) {
                $categories = [];
                foreach ($categoriesManager->getCategories() as $cat) {
                    if (in_array($v['id'], $cat->items)) {
                        $categories['categories'][$cat->id] = [
                            'label' => $cat->label,
                            'url' => router::getInstance()->generate('blog-category', ['name' => util::strToUrl($cat->label), 'id' => $cat->id]),
                        ];
                    }
                }
                $v = array_merge($v, $categories);
                $data[] = new news($v);
                if ($v['draft'] === "0") {
                    $i++;
                }
            }
        }
        $this->nbItemsToPublic = $i;
        $this->items = $data;
    }

    // News

    public function getItems() {
        return $this->items;
    }


    /**
     * Summary of create
     * @param mixed $id
     * @return \news | boolean
     */
    public function create($id) {
        foreach ($this->items as $obj) {
            if ($obj->getId() == $id)
                return $obj;
        }
        return false;
    }

    public function saveNews($obj) {
        $id = intval($obj->getId());
        if ($id < 1) {
            $obj->setId($this->makeId());
            $this->items[] = $obj;
        } else {
            foreach ($this->items as $k => $v) {
                if ($id == $v->getId())
                    $this->items[$k] = $obj;
            }
        }
        return $this->save();
    }

    /**
     * Delete a News from blog & her comments
     * @param news $obj
     * @return bool News correctly deleted
     */
    public function delNews(\news $obj):bool
    {
        $id = $obj->getId();
        foreach ($this->items as $k => $v) {
            if ($id == $v->getId())
                unset($this->items[$k]);
        }
        if ($this->save()) {
            return $this->deleteCommentsFromNews($id);
        }
        return false;
    }

    public function count() {
        return count($this->items);
    }

    /**
     * Return number of news who can be displayed in public mode
     */
    public function getNbItemsToPublic() {
        return $this->nbItemsToPublic;
    }

    public function rss() {
        $core = core::getInstance();
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss version="2.0">';
        $xml .= '<channel>';
        $xml .= ' <title>' . $core->getConfigVal('siteName') . ' - ' . pluginsManager::getPluginConfVal('blog', 'label') . '</title>';
        $xml .= ' <link>' . $core->getConfigVal('siteUrl') . '/</link>';
        $xml .= ' <description>' . $core->getConfigVal('siteDescription') . '</description>';
        $xml .= ' <language>' . $core->getConfigVal('siteLang') . '</language>';
        foreach ($this->getItems() as $k => $v)
            if (!$v->getDraft()) {
                $xml .= '<item>';
                $xml .= '<title><![CDATA[' . $v->getName() . ']]></title>';
                $xml .= '<link>' . $core->getConfigVal('siteUrl') . '/news/' . util::strToUrl($v->getName()) . '-' . $v->getId() . '.html</link>';
                $xml .= '<pubDate>' . (date("D, d M Y H:i:s O", strtotime($v->getDate()))) . '</pubDate>';
                $xml .= '<description><![CDATA[' . $v->getContent() . ']]></description>';
                $xml .= '</item>';
            }
        $xml .= '</channel>';
        $xml .= '</rss>';
        header('Cache-Control: must-revalidate, pre-check=0, post-check=0, max-age=0');
        header('Content-Type: application/rss+xml; charset=utf-8');
        echo $xml;
        die();
    }

    private function makeId() {
        $ids = array(0);
        foreach ($this->items as $obj) {
            $ids[] = $obj->getId();
        }
        return max($ids) + 1;
    }

    private function save() {
        $data = array();
        foreach ($this->items as $k => $v) {
            $data[] = array(
                'id' => $v->getId(),
                'name' => $v->getName(),
                'content' => $v->getContent(),
                'intro' => $v->getIntro(),
                'seoDesc' => $v->getSEODesc(),
                'date' => $v->getDate(),
                'draft' => $v->getDraft(),
                'img' => $v->getImg(),
                'commentsOff' => $v->getCommentsOff(),
            );
        }
        if (util::writeJsonFile(ROOT . 'data/plugin/blog/blog.json', $data))
            return true;
        return false;
    }

    // Comments

    public function getComments() {
        return $this->comments;
    }

    public function getFlatComments() {
        return $this->flatComments;
    }

    public function createComment($id) {
        foreach ($this->flatComments as $obj) {
            if ($obj->getId() == $id)
                return $obj;
        }
        return false;
    }

    public function loadComments($idNews) {
        if (!file_exists(DATA_PLUGIN . 'blog/comments.json'))
            util::writeJsonFile(DATA_PLUGIN . 'blog/comments.json', []);
        $temp = util::readJsonFile(DATA_PLUGIN . 'blog/comments.json');
        $comments = $temp[$idNews] ?? [];
        $temp = util::sort2DimArray($comments, 'id', 'asc');
        $data = [];
        foreach ($temp as $v) {
            $data[$v['id']] = new newsComment($v);
        }
        $this->flatComments = $data;
        $this->comments = $data;
        foreach($this->comments as $k => &$com) {
            foreach($com->repliesId as $comId) {
                $com->replies[$comId] = $data[$comId];
                unset($this->comments[$comId]);   
            }
            foreach($com->replies as &$comment) {
                $comment = $this->hydrateReplies($comment);
            }
        }
    }

    protected function deleteCommentsFromNews($idNews) {
        $temp = util::readJsonFile(DATA_PLUGIN . 'blog/comments.json');
        unset($temp[$idNews]);
        return util::writeJsonFile(DATA_PLUGIN . 'blog/comments.json', $temp);
    }

    public static function getLatestComments(int $nbComments = 10) {
        $newsManager = new newsManager();
        $rawComments = util::readJsonFile(DATA_PLUGIN . 'blog/comments.json');
        $timeComments = [];
        foreach ($rawComments as $idNews => $comments) {
            $news = $newsManager->create($idNews);
            foreach ($comments as $comment) {
                $timeComments[util::getTimestampFromDate($comment['date'])] = ['comment' => new newsComment($comment),'news' => $news];
            }
        }
        krsort($timeComments);
        return array_slice($timeComments, 0, $nbComments, true);
    }

    protected function hydrateReplies(\newsComment $comment):newsComment {
        if (!empty($comment->repliesId)) {
            foreach($comment->repliesId as $comId) {
                $comment->replies[$comId] = $this->comments[$comId];
                unset($this->comments[$comId]);
            }
            foreach($comment->replies as &$childComment) {
                $childComment = $this->hydrateReplies($childComment);
            }
        }
        return $comment;

    }

    public function countComments($idNews = 0) {
        if ($idNews == 0)
            return count($this->flatComments);
        else {
            $this->loadComments($idNews);
            return count($this->flatComments);
        }
    }

    public function saveComment(\newsComment $comment) {
        
        $this->flatComments[$comment->getId()] = $comment;
        $this->saveComments($comment->getIdNews());
        return true;
    }

    public function addReplyToComment(\newsComment $comment, int $parentId):void {
        if (isset($this->flatComments[$parentId]) && $comment->getIdNews()) {
            $this->flatComments[$parentId]->repliesId[] = $comment->getId();
            $this->flatComments[$parentId]->repliesId = array_unique($this->flatComments[$parentId]->repliesId);
            $this->saveComments($comment->getIdNews());
        }
    }

    protected function saveComments($idNews):void {
        $rawComments = util::readJsonFile(DATA_PLUGIN . 'blog/comments.json');
        $objData = [];
        foreach ($rawComments as $newsId => $comment) {
            foreach ($comment as $idComment => $v) {
                $objData[$newsId][$idComment] = new newsComment($v);
            }
        }
        $objData[$idNews] = $this->flatComments;
        $data = [];
        foreach ($objData as $newsId) {
            foreach ($newsId as $k => $v) {
                logg($newsId);
                $data[$v->getIdNews()][$k] = [
                    'id' => $v->getId(),
                    'idNews' => $v->getIdNews(),
                    'content' => $v->getContent(),
                    'date' => $v->getDate(),
                    'author' => $v->getAuthor(),
                    'authorEmail' => $v->getAuthorEmail(),
                    'authorWebsite' => $v->getAuthorWebsite(),
                    'replies' => $v->repliesId
                ];
            }
        }
        util::writeJsonFile(DATA_PLUGIN . 'blog/comments.json', $data);
    }

    /**
     * Delete a comment and all occurences from this comment in others it was a reply
     * and save the comments
     * @param newsComment $obj
     * @return bool Comment deleted
     */
    public function delComment(\newsComment $obj):bool {
        $rawComments = util::readJsonFile(DATA_PLUGIN . 'blog/comments.json');
        if (isset($rawComments[$obj->getIdNews()][$obj->getId()])) {
            $idNews = $obj->getIdNews();
            $newsComments = &$rawComments[$idNews];
            foreach ($newsComments as $idComment => &$comment) {
                if (!is_array($comment['replies'])) {
                    $comment['replies'] = [];
                }
                if (in_array($obj->getId(), $comment['replies'] )) {
                    $comment['replies'] = array_diff($comment['replies'], [$obj->getId()]);
                }
            }
            unset($rawComments[$obj->getIdNews()][$obj->getId()]);
        }
        return util::writeJsonFile(DATA_PLUGIN . 'blog/comments.json', $rawComments);
    }
}