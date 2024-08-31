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
defined('ROOT') or exit('Access denied!');

class newsComment
{

    private $id;
    private $idNews;
    private $author;
    private $authorEmail;
    private $authorWebsite;
    private $date;
    private $content;

    /**
     * Array with ID of children comments
     */
    public $repliesId;

    public $replies = [];

    public function __construct($val = array())
    {
        if (count($val) > 0) {
            $this->id = $val['id'];
            $this->idNews = $val['idNews'];
            $this->content = $val['content'];
            $this->date = $val['date'];
            $this->author = $val['author'];
            $this->authorEmail = $val['authorEmail'];
            $this->authorWebsite = $val['authorWebsite'];
            $this->repliesId = $val['replies'] ?? [];
        } else {
            $this->id = time();
        }
    }

    public function hasReplies(): bool
    {
        return (!empty($this->replies));
    }

    public function show()
    {
        $tpl = new Template(PLUGINS . 'blog/template/comment.tpl');
        $tpl->set('comment', $this);
        echo $tpl->output();
    }

    public function setId($val)
    {
        $this->id = intval($val);
    }

    public function setIdNews($val)
    {
        $this->idNews = intval($val);
    }

    public function setAuthor($val)
    {
        $this->author = trim(htmlEntities($val, ENT_QUOTES));
    }

    public function setAuthorEmail($val)
    {
        $this->authorEmail = trim(htmlEntities($val, ENT_QUOTES));
    }

    public function setAuthorWebsite($val)
    {
        $this->authorWebsite = trim(htmlEntities($val, ENT_QUOTES));
    }

    public function setDate($val)
    {
        $val = trim($val);
        if ($val == '')
            $val = new DateTime();
        $this->date = $val->getTimestamp();
    }

    public function setContent($val)
    {
        $this->content = trim(htmlEntities($val, ENT_QUOTES));
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdNews()
    {
        return $this->idNews;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    public function getAuthorWebsite()
    {
        return $this->authorWebsite;
    }

    public function getAuthorAvatar()
    {
        return 'https://seccdn.libravatar.org/avatar/' . md5($this->authorEmail) . '?s=200';
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getContent()
    {
        return $this->content;
    }

}