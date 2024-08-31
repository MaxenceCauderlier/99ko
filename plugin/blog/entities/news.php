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

class news
{

    private $id;
    private $name;
    private $date;
    private $content;
    private $intro;
    private $seoDesc;
    private $draft;
    private $img;
    private $commentsOff;

    /**
     * Array with Id of categories
     * @var array
     */
    public array $categories = [];

    public function __construct($val = array())
    {
        if (count($val) > 0) {
            $this->id = $val['id'];
            $this->name = $val['name'];
            $this->content = $val['content'];
            $this->intro = $val['intro'] ?? '';
            $this->seoDesc = $val['seoDesc'] ?? '';
            $this->date = $val['date'];
            $this->draft = $val['draft'];
            $this->img = (isset($val['img']) ? $val['img'] : '');
            $this->commentsOff = (isset($val['commentsOff']) ? $val['commentsOff'] : 0);
            $this->categories = $val['categories'] ?? [];
        }
    }

    public function setId($val)
    {
        $this->id = intval($val);
    }

    public function setName($val)
    {
        $this->name = trim($val);
    }

    public function setContent($val)
    {
        $this->content = trim($val);
    }

    public function setIntro($val)
    {
        $this->intro = trim($val);
    }

    public function setSEODesc($val)
    {
        $this->seoDesc = trim($val);
    }

    public function setDate($val)
    {
        if ($val === null || empty($val)) {
            $val = date('Y-m-d');
        }
        $val = trim($val);
        $this->date = $val;
    }

    public function setDraft($val)
    {
        $this->draft = trim($val);
    }

    public function setImg($val)
    {
        $this->img = trim($val);
    }

    public function setCommentsOff($val)
    {
        $this->commentsOff = trim($val);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getUrl()
    {
        return router::getInstance()->generate('blog-read', ['name' => util::strToUrl($this->name), 'id' => $this->id]);
    }

    public function getIntro()
    {
        return ($this->intro === '' ? false : $this->intro);
    }

    public function getSEODesc()
    {
        return ($this->seoDesc === '' ? false : $this->seoDesc);
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getReadableDate() {
        return util::getDate($this->date);
    }

    public function getDraft()
    {
        return $this->draft;
    }

    public function getImg()
    {
        return $this->img;
    }

    public function getImgUrl()
    {
        return util::urlBuild(UPLOAD . 'galerie/' . $this->img);
    }

    public function getCommentsOff()
    {
        return $this->commentsOff;
    }

}