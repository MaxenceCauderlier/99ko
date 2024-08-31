<?php
/**
 * @copyright (C) 2024, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 *
 * @package 299Ko https://github.com/299Ko/299ko
 */

ini_set('display_errors', 1);

const ROOT = './';

include_once ROOT . 'common/common.php';

if (file_exists(DATA . 'config.json')) {
    die('A config file is already exist');
}

$core = core::getInstance();
$pluginsManager = pluginsManager::getInstance();
$url = core::getInstance()->makeSiteUrl() . '/install.php';

// ----------------- Begin tests
// Test PHP Version
$minPHPVersion = 7.4;
$errorPHP = !((float) substr(phpversion(), 0, 3) >= $minPHPVersion);

// Test mod_rewrite
if (function_exists('apache_get_modules')) {
    // PHP is installed as an Apache module
    $errorRewrite = !in_array('mod_rewrite', apache_get_modules()) ? true : false;
} else {
    // PHP is installed as a CGI
    $errorRewrite = 'CGI';
}

// Test writable
if (!is_dir(DATA)) {
    @mkdir(DATA);
}
$errorDataWrite = !is_writable(DATA);

$availablesLocales = lang::$availablesLocales;

if (count($_POST) > 0) {
	if ($core->install()) {
		$plugins = $pluginsManager->getPlugins();
		if ($plugins != false) {
			foreach ($plugins as $plugin) {
				if ($plugin->getLibFile()) {
					include_once($plugin->getLibFile());
					$plugin->loadLangFile();
					if (!$plugin->isInstalled())
						$pluginsManager->installPlugin($plugin->getName(), true);
					$plugin->setConfigVal('activate', '1');
					$pluginsManager->savePluginConfig($plugin);
				}
			}
		}
	}
	include(DATA . 'key.php');
    $adminPwd = UsersManager::encrypt($_POST['adminPwd']);
    $adminEmail = $_POST['adminEmail'];
    $config = array(
        'siteName' => "SiteName",
        'siteDesc' => "Description",
        'siteUrl' => $core->makeSiteUrl(),
        'theme' => 'default',
        'hideTitles' => '0',
        'defaultPlugin' => 'page',
        'debug' => '0',
        'defaultAdminPlugin' => 'page',
        'siteLang' => $_POST['lang-select'],
    );
    if (!file_put_contents(DATA . 'config.json', json_encode($config)) || !chmod(DATA . 'config.json', 0600)) {
        logg('Error while writing config file', 'ERROR');
        show::msg(lang::get('install-problem-during-install'), 'error');
        header('location:' . $core->makeSiteUrl() );
        die();
    } else {
        $_SESSION['installOk'] = true;
        logg('Plugins installation done', 'SUCCESS');
        $user = new User();
        $user->email = $adminEmail;
        $user->pwd = $adminPwd;
        $user->save();
        logg('Admin user created, end of install', 'SUCCESS');
        show::msg(lang::get('install-successfull'), 'success');
        header('location:' . $core->makeSiteUrl() );
        die();
    }
}

?>
<!doctype html>
<html lang="<?= lang::getLocale() ?>">
    <head>
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>299ko - <?= lang::get('install-installation') ?></title>
        <link rel="stylesheet" href="admin/styles.css" media="all">
        <link rel="stylesheet" href="<?= FONTICON ?>" />
    </head>

    <body class="login">
        <div id="alert-msg">
            <?php show::displayMsg(); ?>
        </div>
        <section id="install">
            <header>
                <h1 class="text-center"><?= lang::get('install-installation'); ?></h1>
            </header>
<?php
	if ($errorPHP) {
?>
			<div class="msg error">
                <?= lang::get('install-php-version-error', (float) substr(phpversion(), 0, 3), $minPHPVersion) ?>
            </div>
<?php
	} else {
?>
            <div class="msg success">
                <?= lang::get('install-php-version-ok', $minPHPVersion) ?>
            </div>
<?php
    }

	if ($errorRewrite === 'CGI') {
?>
			<div class="msg warning">
                <?= lang::get('install-php-rewrite-cgi') ?>
			</div>
<?php
    } elseif ($errorRewrite) {
?>
            <div class="msg error">
                <?= lang::get('install-php-rewrite-error') ?>
            </div>
<?php
    } else {
?>
			<div class="msg success">';
				<?= lang::get('install-php-rewrite-ok') ?>
            </div>
<?php
    }

	if ($errorDataWrite) {
?>
			<div class="msg error">
                <?= lang::get('install-php-data-write-error') ?>
            </div>
<?php
	} else {
?>
			<div class="msg success">
                <?= lang::get('install-php-data-write-ok') ?>
            </div>
<?php
	}

	if ($errorDataWrite || $errorPHP || $errorRewrite === true) {
		echo lang::get('install-please-check-errors');
	} else {
?>
                <form method="post" action="">
                    <h3><?= lang::get('install-please-fill-fields') ?></h3>
                    <p><label for="lang-select"><?= lang::get('install-lang-choice'); ?></label>
                    <select name="lang-select" id="lang-select" onchange="langChange()">
<?php
		$locale = lang::getLocale();
		foreach (lang::$availablesLocales as $k => $v) {
			$selected = ($locale === $k) ? 'selected' : '';
?>
						<option value="<?= $k ?>" <?= $selected ?>><?= $v  ?></option>
<?php
		}
?>
                    </select>
                    </p>
                    <p>
                        <label for="adminEmail"><?= lang::get('email'); ?></label><br>
                        <input type="email" name="adminEmail" required="required">
                    </p>
                    <p>
                        <label for="adminPwd"><?= lang::get('password'); ?></label><br>
                        <input type="password" name="adminPwd" id="adminPwd" required="required">
                    </p>
                    <p>
                        <a id="showPassword" href="javascript:showPassword()" class="button success"><?= lang::get('install-show-password'); ?></a>
                        <button type="submit" class="button success"><?= lang::get('submit'); ?></button>
                    </p>
				</form>
				<footer>
					<a target="github" href="https://github.com/299ko/"><?= lang::get('site-just-using', VERSION); ?></a>
				</footer>
<?php
	}
?>
        </section>
        <script type="text/javascript">
            function showPassword() {
                document.getElementById('adminPwd').setAttribute('type', 'text');
                document.getElementById('showPassword').style.display = 'none';
            }
            function langChange() {
                window.location.href = '<?= $url; ?>?lang=' + document.getElementById("lang-select").value;
            }
        </script>
    </body>
</html>
