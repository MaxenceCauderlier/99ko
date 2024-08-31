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

class show {

    /**
     * @var array Modules
     */
    protected static $sidebarPublicModules = [];
    
    /**
     * Add a message to display in the next view, saved in session
     * 
     * Class can be error, success, info (default), warning
     *
     * @param string Message Content
     * @param string Class Message
     */
    public static function msg($content, $class = 'info') {
        if (function_exists('msg')) {
            call_user_func('msg', $content, $class);
            return;
        }

        if (!isset($_SESSION['flash_msg']) || !is_array($_SESSION['flash_msg'])) {
            $_SESSION['flash_msg'] = [];
        }
        $_SESSION['flash_msg'][] = [
            'class' => $class,
            'content' => $content
        ];
    }

    /**
     * Display all messages added with 'msg' method, who were saved in session
     */
    public static function displayMsg() {
        if (function_exists('displayMsg')) {
            call_user_func('displayMsg');
            return;
        }
        if (!isset($_SESSION['flash_msg']) || !is_array($_SESSION['flash_msg'])) {
            return;
        }
        foreach ($_SESSION['flash_msg'] as $msg) {
            echo '<div class="msg ' . $msg['class'] . '"><p>' . $msg['content'] . '</p><a href="javascript:" class="msg-button-close"><i class="fa-solid fa-xmark"></i></a></div>';
        }
        unset($_SESSION['flash_msg']);
    }

    ## Affiche les balises "link" type css (admin + theme)

    public static function linkTags() {
        if (function_exists('linkTags'))
            call_user_func('linkTags');
        else {
            $core = core::getInstance();
            $pluginsManager = pluginsManager::getInstance();
            foreach ($core->getCss() as $k => $v) {
                echo '<link href="' . util::urlBuild($v) . '" rel="stylesheet" type="text/css" />';
            }
            foreach ($pluginsManager->getPlugins() as $k => $plugin)
                if ($plugin->getConfigval('activate') == 1) {
                    if (!ADMIN_MODE && $plugin->getConfigVal('activate') && $plugin->getPublicCssFile())
                        echo '<link href="' . util::urlBuild($plugin->getPublicCssFile()) . '" rel="stylesheet" type="text/css" />';
                    elseif (ADMIN_MODE && $plugin->getConfigVal('activate') && $plugin->getAdminCssFile())
                        echo '<link href="' . $plugin->getAdminCssFile() . '" rel="stylesheet" type="text/css" />';
                }
            if (!ADMIN_MODE)
                echo '<link href="' . $core->getConfigVal('siteUrl') . '/' . 'theme/' . $core->getConfigVal('theme') . '/styles.css" rel="stylesheet" type="text/css" />';
        }
    }

    ## Affiche les balises "script" type javascript (admin + theme)

    public static function scriptTags() {
        if (function_exists('scriptTags'))
            call_user_func('scriptTags');
        else {
            $core = core::getInstance();
            $pluginsManager = pluginsManager::getInstance();
            foreach ($core->getJs() as $k => $v) {
                echo '<script type="text/javascript" src="' . util::urlBuild($v) . '"></script>';
            }
            foreach ($pluginsManager->getPlugins() as $k => $plugin)
                if ($plugin->getConfigval('activate') == 1) {
                    if (!ADMIN_MODE && $plugin->getConfigVal('activate') && $plugin->getPublicJsFile())
                        echo '<script type="text/javascript" src="' . util::urlBuild($plugin->getPublicJsFile()) . '"></script>';
                    elseif (ADMIN_MODE && $plugin->getConfigVal('activate') && $plugin->getAdminJsFile())
                        echo '<script type="text/javascript" src="' . $plugin->getAdminJsFile() . '"></script>';
                }
            if (!ADMIN_MODE)
                echo '<script type="text/javascript" src="' . $core->getConfigVal('siteUrl') . '/' . 'theme/' . $core->getConfigVal('theme') . '/scripts.js' . '"></script>';
        }
    }
    
    public static function showMetas() {
        $str = '';
        $core = core::getInstance();
        foreach ($core->getMetas() as $meta) {
            $str .= $meta . "\n";
        }
        echo $str;
    }

    ## Affiche un champ de formulaire contenant le jeton de session (admin)

    public static function tokenField() {
        $user = UsersManager::getCurrentUser();
        if ($user === null) {
            return "";
        }
        echo '<input type="hidden" name="token" value="' . $user->token . '" />';
    }

    ## Affiche le contenu de la meta title (theme)

    public static function titleTag() {
        if (function_exists('titleTag'))
            call_user_func('titleTag');
        else {
            $core = core::getInstance();
            global $runPlugin;
            if (!$runPlugin)
                echo Lang::get('core-404-title');
            else
                echo $runPlugin->getTitleTag() . ' - ' . $core->getConfigVal('siteName');
        }
    }

    ## Affiche le contenu de la meta description (theme)

    public static function metaDescriptionTag() {
        if (function_exists('metaDescriptionTag'))
            call_user_func('metaDescriptionTag');
        else {
            $core = core::getInstance();
            global $runPlugin;
            if (!$runPlugin)
                echo '404';
            else
                echo $runPlugin->getMetaDescriptionTag();
        }
    }

    ## Affiche le titre de page (theme)

    public static function mainTitle($format = '<h1>[mainTitle]</h1>') {
        if (function_exists('mainTitle'))
            call_user_func('mainTitle', $format);
        else {
            $core = core::getInstance();
            global $runPlugin;
            $data = $format;
            if (!$runPlugin)
                $data = str_replace('[mainTitle]', Lang::get('core-404-title'), $data);
            else {
                if ($core->getConfigVal('hideTitles') == 0 && $runPlugin->getMainTitle() != '') {
                    $data = $format;
                    $data = str_replace('[mainTitle]', $runPlugin->getMainTitle(), $data);
                } else
                    $data = '';
            }
            echo $data;
        }
    }

    ## Affiche le nom du site (theme)

    public static function siteName() {
        if (function_exists('siteName'))
            call_user_func('siteName');
        else {
            $core = core::getInstance();
            echo $core->getConfigVal('siteName');
        }
    }
    
    public static function siteDesc() {
        if (function_exists('siteDesc'))
            call_user_func('siteDesc');
        else {
            $core = core::getInstance();
            echo $core->getConfigVal('siteDesc');
        }
    }

    ## Affiche l'url du site (theme)

    public static function siteUrl() {
        if (function_exists('siteUrl'))
            call_user_func('siteUrl');
        else {
            $core = core::getInstance();
            echo $core->getConfigVal('siteUrl');
        }
    }

    ## Affiche la navigation principale (theme)

    public static function mainNavigation($format = '<li><a class="[cssClass]" href="[target]" target="[targetAttribut]">[label]</a>[childrens]</li>') {
        if (function_exists('mainNavigation'))
            call_user_func('mainNavigation', $format);
        else {
            $pluginsManager = pluginsManager::getInstance();
            $core = core::getInstance();
            $data = '';
            foreach ($pluginsManager->getPlugins() as $k => $plugin)
                if ($plugin->getConfigval('activate') == 1) {
                    foreach ($plugin->getNavigation() as $k2 => $item)
                        if ($item['label'] != '') {
                            if ($item['parent'] < 1) {
                                $temp = $format;
                                $temp = str_replace('[target]', $item['target'], $temp);
                                $temp = str_replace('[label]', $item['label'], $temp);
                                $temp = str_replace('[targetAttribut]', $item['targetAttribut'], $temp);
                                $temp = str_replace('[cssClass]', $item['cssClass'], $temp);
                                $data2 = '<ul>';
                                $i = 0;
                                foreach ($plugin->getNavigation() as $k3 => $item2)
                                    if ($item2['label'] != '' && $item2['parent'] == $item['id']) {
                                        $temp2 = $format;
                                        $temp2 = str_replace('[target]', $item2['target'], $temp2);
                                        $temp2 = str_replace('[label]', $item2['label'], $temp2);
                                        $temp2 = str_replace('[targetAttribut]', $item2['targetAttribut'], $temp2);
                                        $temp2 = str_replace('[cssClass]', $item2['cssClass'], $temp2);
                                        $temp2 = str_replace('[childrens]', '', $temp2);
                                        $data2 .= $temp2;
                                        $i++;
                                    }
                                $data2 .= '</ul>';
                                if ($i == 0)
                                    $data2 = '';
                                $temp = str_replace('[childrens]', $data2, $temp);
                                $data .= $temp;
                            }
                        }
                }
            echo $data;
        }
    }

    /**
     * Display the Administration items
     * 
     * It only display the <li> items. You have to put it between <ul>.
     * Links are sorted by plugin's name, and current plugin had li.activePlugin class
     */
    public static function adminNavigation() {
        if (function_exists('adminNavigation'))
            call_user_func('adminNavigation');
        else {
            $pluginsManager = pluginsManager::getInstance();
            $data = '';
            $arrPlugins = [];
            foreach ($pluginsManager->getPlugins() as $k => $v) {
                if ($v->getConfigVal('activate') && $v->getIsCallableOnAdmin()) {
                    $arrPlugins[$v->getInfoVal('name')]['name'] = $v->getName();
                    $arrPlugins[$v->getInfoVal('name')]['icon'] = $v->getInfoVal('icon');
                    $arrPlugins[$v->getInfoVal('name')]['label'] = $v->getTranslatedName();
                }
            }
            ksort($arrPlugins, SORT_STRING);
            $currentPlugin = core::getInstance()->getPluginToCall();
            foreach ($arrPlugins as $label) {
                $data .= '<li';
                if ($currentPlugin === $label['name']) {
                    $data .= ' class="activePlugin"';
                }
                $data .= '><a href="' . core::getInstance()->getConfigVal('siteUrl') . '/admin/'. $label['name'] . '"' ;
                if ($currentPlugin === $label['name']) {
                    $data .= ' aria-current="page"';
                }
                $data .= '>';
                $icon = $label['icon'];
                if ($icon == false) {
                    $icon = "fa-regular fa-font-awesome";
                }
                $data .= '<i title="'. $label['label'] .'" class="' . $icon . '"></i>';
                $data .= '<span>' . $label['label'] . '</span></a></li>';
            }
            echo $data;
        }
    }

    ## Affiche le theme courant (theme)

    public static function theme($format = '<a target="_blank" href="[authorWebsite]">[name]</a>') {
        if (function_exists('theme'))
            call_user_func('theme', $format);
        else {
            $core = core::getInstance();
            $data = $format;
            $data = str_replace('[authorWebsite]', $core->getThemeInfo('authorWebsite'), $data);
            $data = str_replace('[name]', $core->getThemeInfo('name'), $data);
            $data = str_replace('[id]', $core->getConfigVal('theme'), $data);
            echo $data;
        }
    }

    ## Affiche l'identifiant du plugin courant (theme)

    public static function pluginId() {
        if (function_exists('pluginId'))
            call_user_func('pluginId');
        else {
            $core = core::getInstance();
            global $runPlugin;
            if (!$runPlugin)
                echo '';
            else
                echo $runPlugin->getName();
        }
    }

    ## Affiche l'URL courante (theme)

    public static function currentUrl() {
        if (function_exists('currentUrl'))
            call_user_func('currentUrl');
        else {
            $core = core::getInstance();
            echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }
    }

    ## Affiche l'URL de l'icon du thème

    public static function themeIcon() {
        if (function_exists('themeIcon'))
            call_user_func('themeIcon');
        $core = core::getInstance();
        $icon = 'theme/' . $core->getConfigVal('theme') . '/icon.png';
        if (file_exists($icon))
            echo util::urlBuild($icon);
    }
    
    /**
     * Add a module in the Public Sidebar
     * 
     * @param string Title
     * @param string Content
     */
    public static function addSidebarPublicModule(string $title, string $content) {
        self::$sidebarPublicModules[] = [
            'title' => $title,
            'content' => $content
        ];
    }

    /**
     * Display the Public Sidebar
     */
    public static function displayPublicSidebar() {
        if (function_exists('displayPublicSidebar')) {
            call_user_func('displayPublicSidebar');
            return;
        }
        if (empty(self::$sidebarPublicModules)) {
            return;
        }
        echo '<aside id="modulesSidebar">';
        foreach (self::$sidebarPublicModules as $module) {
            echo '<div class="sidebarModule card">';
            echo '<header class="sidebarModuleTitle">' . $module['title'] . '</header>';
            echo '<div class="sidebarModuleContent">';
            echo $module['content'];
            echo '</div></div>';
        }
        echo '</aside>';
    }

}