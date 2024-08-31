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

## Fonction d'installation

function seoInstall() {
    
}

## Hooks

function seoEndFrontHead() {
    $plugin = pluginsManager::getInstance()->getPlugin('seo');
    if ($plugin->getConfigVal('trackingId') === false || $plugin->getConfigVal('trackingId') === '') {
        return;
    }
    $temp = "<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '" . $plugin->getConfigVal('trackingId') . "', 'auto');
  ga('send', 'pageview');

</script>";
    $temp .= '<meta name="google-site-verification" content="' . $plugin->getConfigVal('wt') . '" />';
    echo $temp;
}

function seoEndFrontBody() {
    echo '<div id="seo_social_float"><ul>';
    echo seoGetSocialIcons('<li>', '</li>');
    echo '</ul></div>';
}

function seoMainNavigation() {
    echo seoGetSocialIcons('<li class="seo_element">', '</li>');
}

function seoFooter() {
    echo '<div id="seo_social"><ul>';
    echo seoGetSocialIcons('<li>', '</li>');
    echo '</ul></div>';
}

function seoGetSocialIcons($before = '', $after = '') {
    $social = seoGetSocialVars();
    $plugin = pluginsManager::getInstance()->getPlugin('seo');
    $str = "";

    foreach ($social as $k => $v) {
        $tConfig = $plugin->getConfigVal($v);
        if ($tConfig && $tConfig !== '') {
            $str .= $before . '<a target="_blank" title="'. lang::get('seo.follow-on', $k) . '" href="' . $tConfig . '"><i class="fa-brands fa-' . $v . '"></i></a>' . $after;
        }
    }
    return $str;
}

function seoGetSocialVars() {
    return [
        'Facebook' => 'facebook',
        'Twitter' => 'twitter',
        'YouTube' => 'youtube',
        'Instagram' => 'instagram',
        'TikTok' => 'tiktok',
        'Pinterest' => 'pinterest',
        'Linkedin' => 'linkedin',
        'Viadeo' => 'viadeo',
        'GitHub' => 'github',
        'Gitlab' => 'gitlab',
        "Mastodon" => 'mastodon',
        "Twitch" => 'twitch',
        "Discord" => 'discord',
        "Codepen" => 'codepen',
        "Tumblr" => 'tumblr',
    ];
}
