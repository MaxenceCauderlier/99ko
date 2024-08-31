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

function antispamInstall() {
    
}

## Hooks
## Code relatif au plugin

class antispam {

    protected $captcha;

    public function __construct() {
        $pluginManager = pluginsManager::getInstance();
        $typeCaptcha = $pluginManager->getPluginConfVal('antispam', 'type');
        if ($typeCaptcha === 'useText') {
            // UseText lib
            $this->captcha = new textCaptcha();
        } elseif ($typeCaptcha === 'useRecaptcha') {
            // ReCaptcha lib
            $public = $pluginManager->getPluginConfVal('antispam', 'recaptchaPublicKey');
            $secret = $pluginManager->getPluginConfVal('antispam', 'recaptchaSecretKey');
            $this->captcha = new reCaptcha($public, $secret);
        }
    }

    public function show() {
        return $this->captcha->getText();
    }

    public function isValid() {
        return $this->captcha->isValid();
    }

}

class textCaptcha {

    protected $operation;
    protected $result;

    public function getText() {
        if (!isset($this->operation)) {
            $this->generate();
        }
        return '<p><label for="antispam">' . $this->operation . lang::get('antispam.in-numbers') . '</label><br><input required="required" type="text" name="antispam" id="antispam" value="" /></p>';
    }

    public function isValid() {
        return (isset($_SESSION['antispam_result']) && isset($_POST['antispam']) && $_SESSION['antispam_result'] === sha1($_POST['antispam']));
    }

    protected function generate() {
        $numbers = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        $letters = [];
        foreach ($numbers as $number) {
            $letters[] = lang::get('antispam.' . $number);
        }
        $first = rand(0, count($numbers) - 1);
        $second = rand(0, count($numbers) - 1);
        $sign = rand(0, 1);
        $o = lang::get('antispam.how-counts') . $letters[$first];
        if ($second <= $first && $sign == 1) {
            $r = $numbers[$first] - $numbers[$second];
            $o .= lang::get('antispam.minus-alt');
        } elseif ($second <= $first && $sign == 0) {
            $r = $numbers[$first] - $numbers[$second];
            $o .= lang::get('antispam.minus');
        } elseif ($second > $first && $sign == 1) {
            $r = $numbers[$first] + $numbers[$second];
            $o .= lang::get('antispam.plus-alt');
        } else {
            $r = $numbers[$first] + $numbers[$second];
            $o .= lang::get('antispam.plus');
        }
        $this->operation = $o . $letters[$second] . " ?";
        $this->result = $r;
        $_SESSION['antispam_result'] = sha1($this->result);
    }

}

class reCaptcha {

    protected $publicKey;
    protected $secretKey;

    public function __construct($publicKey, $secretKey) {
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;
    }

    public function getText() {
        $input = '<input type="hidden" id="recaptchaResponse" name="recaptcha-response">';
        $script = '<script src="https://www.google.com/recaptcha/api.js?render=' . $this->publicKey . '"></script>';
        $script .= '<script>grecaptcha.ready(function() {';
        $script .= "grecaptcha.execute('" . $this->publicKey . "', {action: 'homepage'}).then(function(token) {";
        $script .= "document.getElementById('recaptchaResponse').value = token;";
        $script .= "});});</script>";
        $infos = '<p>Protection par ReCaptcha. <a href="https://www.google.com/intl/fr/policies/privacy/">Confidentialité</a>'
                . ' - <a href="https://www.google.com/intl/fr/policies/terms/">Conditions</a></p>';
        return $input . $infos . $script;
    }

    public function isValid() {
        if (!isset($_POST['recaptcha-response']) || empty($_POST['recaptcha-response'])) {
            // Captcha not set or empty
            return false;
        }
        $url = "https://www.google.com/recaptcha/api/siteverify?secret="
                . $this->secretKey . "&response=" . $_POST['recaptcha-response'];
        // Verify that CURL is available
        if (function_exists('curl_version')) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
        } else {
            // Use file_get_contents
            $response = file_get_contents($url);
        }
        if (empty($response) || is_null($response)) {
            // Bad config or no response by API
            return false;
        }
        $data = json_decode($response);
        if ($data->success) {
            // Captcha is OK
            return true;
        }
        return false;
    }

}
