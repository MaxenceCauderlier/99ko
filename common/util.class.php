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

class util
{
    ## Tri un tableau à 2 dimenssions
    ## $data => tableau
    ## $key => index du tableau sur lequel doit se faire le tri
    ## $mode => type de tri ('desc', 'asc', 'num')

    public static function sort2DimArray($data, $key, $mode)
    {
        if ($mode == 'desc')
            $mode = SORT_DESC;
        elseif ($mode == 'asc')
            $mode = SORT_ASC;
        elseif ($mode == 'num')
            $mode = SORT_NUMERIC;
        $temp = array();
        foreach ($data as $k => $v) {
            $temp[$k] = $v[$key];
        }
        array_multisort($temp, $mode, $data);
        return $data;
    }

    /**
     * Truncate an HTML content and keep only the <p> and <br> tags
     * It save the new lines and dont cut on a word
     *
     * @param  string $str      Content to truncate
     * @param  int    $length   Number of characters to keep
     * @param  string $add      Text to add after the content if truncated
     * @return string
     */
    public static function cutStr($str, $length, $add = '...')
    {
        $str = str_replace("<br />", "<br>", $str);
        $no_tags_content = strip_tags($str, '<p><br>');
        $no_tags_content = str_replace("<p>", "<br>", $no_tags_content);
        $no_tags_content = str_replace("</p>", "", $no_tags_content);
        if (strlen($no_tags_content) > $length) {
            return substr($no_tags_content, 0, strpos($no_tags_content, ' ', $length)) . $add;
        } else {
            return $no_tags_content;
        }
    }


    /**
     * Formate a string to an url
     * @param mixed $str
     * @return string Formatted string
     */
    public static function strToUrl($str)
    {
        $str = str_replace('&', 'et', $str);
        if ($str !== mb_convert_encoding(mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
            $str = mb_convert_encoding($str, 'UTF-8');
        $str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
        $str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '$1', $str);
        $str = preg_replace(array('`[^a-z0-9]`i', '`[-]+`'), '-', $str);
        return strtolower(trim($str, '-'));
    }

    ## Vérifie si la chaîne est un email valide

    public static function isEmail($email)
    {
		return (filter_var($email, FILTER_VALIDATE_EMAIL) === false) ? false : true;
    }

    ## Envoie un email

    public static function sendEmail($from, $reply, $to, $subject, $msg)
    {
        $headers = "From: " . $from . "\r\n";
        $headers .= "Reply-To: " . $reply . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= 'Content-Type: text/plain; charset="utf-8"' . "\r\n";
        $headers .= 'Content-Transfer-Encoding: 8bit';
        if (mail($to, $subject, $msg, $headers)) {
            return true;
        }
        logg('Error when sending mail', 'ERROR');
        return false;
    }

    ## Retourne l'extension d'un fichier présent sur le serveur

    public static function getFileExtension($file)
    {
        return substr(strtolower(strrchr(basename($file), ".")), 1);
    }

    /**
     * List a directory and return an array with files and folders (separated). This function is not recursive
     *
     * @param string $folder Path to scan
     * @param array $not Array of files to exclude
     * @return array $data['dir] : Directories / $data['file'] : Files
     */
    public static function scanDir(string $folder, array $not = [])
    {
        $data['dir'] = [];
        $data['file'] = [];
        $folder = rtrim($folder, '/') . '/';
        foreach (scandir($folder) as $file) {
            if ($file[0] != '.' && !in_array($file, $not)) {
                if (is_file($folder . $file))
                    $data['file'][] = $file;
                elseif (is_dir($folder . $file))
                    $data['dir'][] = $file;
            }
        }
        return $data;
    }

    ## Sauvegarde un tableau dans un fichier au format json

    public static function writeJsonFile($file, $data)
    {
        if (@file_put_contents($file, json_encode($data), LOCK_EX))
            return true;
        return false;
    }

    ## Retourne un tableau provenant d'un fichier au format json

    public static function readJsonFile($file, $assoc = true)
    {
        if (!file_exists($file)) {
            return false;
        }
        return json_decode(@file_get_contents($file), $assoc);
    }

    ## Upload

    public static function uploadFile($k, $dir, $name, $validations = array())
    {
        if (isset($_FILES[$k]) && $_FILES[$k]['name'] != '') {
            $extension = mb_strtolower(util::getFileExtension($_FILES[$k]['name']));
            if (isset($validations['extensions']) && !in_array($extension, $validations['extensions']))
                return 'extension error';
            $size = filesize($_FILES[$k]['tmp_name']);
            if (isset($validations['size']) && $size > $validations['size'])
                return 'size error';
            if (move_uploaded_file($_FILES[$k]['tmp_name'], $dir . $name . '.' . $extension))
                return 'success';
            else
                return 'upload error';
        }
        return 'undefined';
    }

    ## Formate une date

    public static function formatDate($date, $langFrom = 'en', $langTo = 'en')
    {
        $date = substr($date, 0, 10);
        $temp = preg_split('#[-_;\. \/]#', $date);
        if ($langFrom == 'en') {
            $year = $temp[0];
            $month = $temp[1];
            $day = $temp[2];
        } elseif ($langFrom == 'fr') {
            $year = $temp[2];
            $month = $temp[1];
            $day = $temp[0];
        }
        if ($langTo == 'en')
            $data = $year . '-' . $month . '-' . $day;
        elseif ($langTo == 'fr')
            $data = $day . '/' . $month . '/' . $year;
        return $data;
    }

    public static function getTimestampFromDate($date) {
        if (is_int($date)) {
            // Date from timestamp
            $dateObj = new DateTime();
            $dateObj->setTimestamp($date);
        } else {
            // Date from string, old version
            $dateObj = new DateTime($date);
        }
        return $dateObj->getTimestamp();
    }

    public static function getDateHour($date) {
        if (is_int($date)) {
            // Date from timestamp
            $dateObj = new DateTime();
            $dateObj->setTimestamp($date);
        } else {
            // Date from string, old version
            $dateObj = new DateTime($date);
        }
        return $dateObj->format(Lang::get("date-hour-format"));
    }

    public static function getDate($date) :string {
        if (is_int($date)) {
            // Date from timestamp
            $dateObj = new DateTime();
            $dateObj->setTimestamp($date);
        } else {
            // Date from string, old version
            $dateObj = new DateTime($date);
        }
        return $dateObj->format(Lang::get("date-only"));
    }

    public static function getNaturalDate($date) {
        if (is_int($date)) {
            // Date from timestamp
            $dateObj = new DateTime();
            $dateObj->setTimestamp($date);
        } else {
            // Date from string, old version
            $dateObj = new DateTime($date);
        }
        $cal = IntlCalendar::fromDateTime($dateObj->format('Y-m-d H:i:s'));
        return IntlDateFormatter::formatObject($cal, Lang::get("date-natural-date-hour-format"), Lang::get('locale'));
    }

    /**
     * Build absolute URL with siteURL saved in config.json
     *
     * @param  string URI
     * @param  bool   is Admin location
     * @return string URL
     */
    public static function urlBuild($uri, $admin = false)
    {
        if (isset(parse_url($uri)['host'])) {
            // Absolute URL
            return $uri;
        }
        $base = core::getInstance()->getConfigVal('siteUrl') . '/';
        if ($admin) {
            $base .= 'admin/';
        }
        $url = $base . ltrim($uri, '/');
        return str_replace('/./', '/', $url);
    }

    /**
     * Return current page URL
     *
     * @return string
     */
    public static function getCurrentURL()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $url = "https";
        } else {
            $url = "http";
        }
        $url .= "://";
        $url .= $_SERVER['HTTP_HOST'];
        $url .= $_SERVER['REQUEST_URI'];
        return $url;
    }

    /**
     * Format HTML for add missing id to <h1-6> Title, ready for anchors
     * @param string $htmlContent
     * @return string
     */
    public static function generateIdForTitle($htmlContent): string
    {
        $htmlContent = preg_replace_callback(
            '#<h([1-6])>(.*)<\/h[1-6]>#iUs',
            function ($matches) {
                return '<h' . $matches[1] . ' id="' . self::strToUrl($matches[2]) . '">' . $matches[2] . '</h' . $matches[1] . '>';
            },
            $htmlContent
        );
        return $htmlContent;
    }

    /**
     * Generate Table Of Content ready to display in the Content
     * @param string $htmlContent
     * @param string $title
     * @return string
     */
    public static function generateTableOfContents($htmlContent, $title): string
    {
        $toc = '<details class="toc-container">
        <summary><header><h4>' . $title . '</h4></header>';
        $inner = self::generateInnerTOC($htmlContent);
        if (!$inner) {
            return false;
        }
        $toc .= $inner;
        $toc .= '</summary></details>';
        return $toc;
    }

    /**
     * Generate Table Of Content ready to display in the Sidebar
     * @param mixed $htmlContent
     * @return string
     */
    public static function generateTableOfContentAsModule($htmlContent): string
    {
        $toc = '<details class="toc-container"><summary>';
        $inner = self::generateInnerTOC($htmlContent);
        if (!$inner) {
            return false;
        }
        $toc .= $inner;
        $toc .= '</summary></details>';
        return $toc;
    }

    /**
     * Generate ol, li and a tags for the TOC
     * @param string $htmlContent
     * @return string
     */
    protected static function generateInnerTOC($htmlContent): string
    {
        preg_match_all(
            '#<h([1-6]) *id="(.*)">(.*)<\/h[1-6]>#isU',
            $htmlContent,
            $headings,
            PREG_SET_ORDER
        );

        $toc = '';
        $current_level = 0;
        $items = 0;
        foreach ($headings as $heading) {
            $id = $heading[2];
            $text = $heading[3];
            $link = '<a href="#' . $id . '">' . $text . '</a>';
            $level = $heading[1];

            if ($level > $current_level) {
                // Create new ol and up to higher level
                for ($a = 0; $a < $level - $current_level; $a++) {
                    $toc .= "\n" . str_repeat("\t", $current_level * 2) . '<ol class="toc-level-' . $level . '">' . "\n" . str_repeat("\t", ($current_level * 2) + 1) . '<li>';
                }
                $toc .= $link;
                $items = 1;
            } elseif ($level === $current_level) {
                $toc .= ($items ? '</li>' . "\n" : '') . str_repeat("\t", ($level * 2) - 1) . '<li>' . $link;
                $items++;
            } else {
                // Close ol and down level
                for ($a = 0; $a < $current_level - $level; $a++) {
                    $toc .= "\n" . str_repeat("\t", ($level * 2) + 1) . '</li>' .
                        "\n" . str_repeat("\t", $level * 2) . '</ol>';
                }
                $toc .= "\n" . str_repeat("\t", ($level * 2) - 1) . '</li>' . "\n" . str_repeat("\t", ($level * 2) - 1) . '<li>' . $link;
                $items = 0;
            }
            $current_level = $level;
        }

        if (!isset($level)) {
            // No heading
            return false;
        }
        // Close all opened ol
        for ($a = $level - 1; $a >= 0; $a--) {
            $toc .= "\n" . str_repeat("\t", ($a * 2) + 1) . '</li>' .
                "\n" . str_repeat("\t", $a * 2) . '</ol>';
        }
        return $toc;
    }
}
