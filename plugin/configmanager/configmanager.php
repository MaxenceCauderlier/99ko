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

require_once PLUGINS . 'configmanager/lib/UpdaterManager.php';

## Fonction d'installation

function configmanagerInstall() {
    
}

## Hooks
## Code relatif au plugin

function configManagerDisplayInstallFile() {
    if (file_exists(ROOT . 'install.php')) {
        echo "<div class='msg warning'>
                <p>". lang::get("configmanager-delete-install-msg") ."</p>
                <div style='text-align:center'><a class='button' href='" . 
                router::getInstance()->generate('configmanager-delete-install', ['token' => UsersManager::getCurrentUser()->token]) . 
                "'>". lang::get("configmanager-delete-install") ."</a></div>"
        . "<a href='javascript:' class='msg-button-close'><i class='fa-solid fa-xmark'></i></a></div>";
    }
}

function configManagerCheckNewVersion() {
    $cachedInfos = util::readJsonFile(DATA_PLUGIN . 'configmanager/cache.json');
    if ($cachedInfos !== false) {
        // Cached infos
        $lastVersion = $cachedInfos['lastVersion'];
        if ($lastVersion === VERSION) {
            // No local update, check if cache is fresh
            $lastCheckUpdate = (int) $cachedInfos['lastCheckUpdate'];
            if ($lastCheckUpdate + 86400 < time()) {
                // Expired cache, try to retrieve new version
                $nextVersion = configmanagerGetNewVersion();
            } else {
                // Cache ok, actual version is the lastest
                $nextVersion = false;
            }
        } else {
            // Newer version exist in cache
            if ($lastVersion > VERSION) {
                $nextVersion = $lastVersion;
            } else {
                // Actual version (testing) is higher than official release
                $nextVersion = false;
            }
            
        }
    } else {
        // No cache
        $nextVersion = configmanagerGetNewVersion();
    }
    if ($nextVersion) {
        configmanagerDisplayNewVersion($nextVersion);
    }
}

function configmanagerDisplayNewVersion($nextVersion) {
    show::msg("<p>". lang::get('configmanager-update-msg', $nextVersion) ."</p>
        <div style='text-align:center'><a class='button alert' href='" .
        router::getInstance()->generate('configmanager-update', ['token' => UsersManager::getCurrentUser()->token]) .
        "'>". lang::get('configmanager-update') ."</a></div>");
}

function configmanagerGetNewVersion() {
    $updaterManager = new UpdaterManager();
    if ($updaterManager) {
        $nextVersion = $updaterManager->getNextVersion();
    } else {
        $nextVersion = false;
    }
    $file = DATA_PLUGIN . 'configmanager/cache.json';
    $cachedInfos = util::readJsonFile($file);
    if ($cachedInfos === false) {
        $cachedInfos = [];
    }
    $cachedInfos['lastVersion'] = $updaterManager->lastVersion;
    $cachedInfos['lastCheckUpdate'] = time();
    util::writeJsonFile($file, $cachedInfos);
    if ($nextVersion) {
        logg('Nouvelle version trouvée : ' . $nextVersion, 'INFO');
    }
    return $nextVersion;
}
