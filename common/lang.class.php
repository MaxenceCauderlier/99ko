<?php

/**
 * @copyright (C) 2023, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

class lang {
    

    /**
     * Translation name, like 'en' or 'fr'
     * @static
     * @var string
     */
    protected static $locale;

    /**
     * Language sentences
     * @static
     * @var array
     */
    protected static $data = [];
    
    public static $availablesLocales = [
        'fr' => 'Français',
        'en' => 'English',
        'ru' => 'Russian'
    ];
    
    /**
     * Set the language locale
     * @param string $locale
     */
    public static function setLocale(string $locale) {
        self::$locale = $locale;
        setlocale(LC_ALL, $locale);
    }
    
    /**
     * Get the current language locale
     * @return string
     */
    public static function getLocale():string {
        return self::$locale;
    }

    /**
     * Get the available locales and their names
     *
     * @return array An array of available locales as keys and locale names as values
     */
    public static function getAvailablesLocales(): array
    {
        return self::$availablesLocales;
    }
    
    /**
     * Load a language .ini file
     * If locale is set to 'en', will load $folderPath . 'en.ini'
     * 
     * @param string Folder Path where file can be found
     * @return bool File loaded or not
     */
    public static function loadLanguageFile(string $folderPath): bool {
        $file = $folderPath . self::$locale . '.ini';
        if (file_exists($file)) {
            $datas = parse_ini_file($file);
            self::$data = array_merge(self::$data, $datas);
            return true;
        }
        return false;
    }
    
    /**
     * Get a string from a locale file
     * 
     * @param string Name of var
     * @param string Parameters
     * Eg: Lang::get('test', 'one', 'two')
     * @return string
     */
    public static function get($name) {        
        if (!isset(self::$data[$name])) {
            return $name;
        }
        $nbArgs = func_num_args();
        if ($nbArgs === 1) {
            // No param
            return self::$data[$name];
        }
        $args = func_get_args();
        unset($args[0]);

        if (is_array($args[1])) {
            return vsprintf(self::$data[$name], $args[1]);
        }
        return vsprintf(self::$data[$name], $args);

    }

}