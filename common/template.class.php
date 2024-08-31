<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

/**
 * Simple lightweight template parser.
 */
class Template {

    /** @var array  Constants */
    protected static $const = [];

    /** @var string Template path */
    protected $file;

    /** @var string Template content */
    protected $content;

    /** @var array  Assigned datas to replace */
    protected $data = [];

    /**
     * Constructor
     * Check if the template exist in the current theme, else template will be
     * taken from 'default' theme
     *
     * @param string $file name with extension
     */
    public function __construct($file) {
        $this->file = $file;
    }

    /**
     * Add a var who will be added to all templates
     *
     * @static
     * @param string Var key
     * @param mixed Value
     */
    public static function addGlobal($key, $value) {
        self::$const[$key] = $value;
    }

    /**
     * Assign datas to this template
     * Datas can be string, numeric... or array and objects
     *
     * @param string Var key
     * @param mixed Value
     */
    public function set($key, $value) {
        $this->data[$key] = $value;
    }

    /**
     * Return the parsed template content
     *
     * @return string Parsed content
     */
    public function output() {
        if (!file_exists($this->file)) {
            return "Error loading template file ($this->file).<br/>";
        }
        ob_start();
        $this->get_content();
        $this->addGlobalsToVars();
        $this->parse();
        // Uncomment the next line to see parsed template
        // file_put_contents($this->file . '.cache.php', $this->content);
        eval('?>' . $this->content);
        return ob_get_clean();
    }

    /**
     * Get template content
     */
    protected function get_content() {
        $this->content = file_get_contents($this->file);
    }

    /**
     * Parse template
     * Allowed tags :
     * {# This is multiline allowed comments #}
     * {% NOPARSE %} ... {% ENDNOPARSE %}
     * {% HOOK.hookName %}
     * {% SHOW.Method %}
     * {% URL(blog&p=ttt&yyy).admin
     * {% INCLUDE My_Page %}
     * {% IF MY_VAR %} {% IF MY_VAR !== 25 %} ... {% ELSE %} ... {% ENDIF %}
     * {% ELSEIF ( false == 0 && true == 1 ) && plop === "plo" && 5 === 5 %}
     * {{ Lang.key }}
     * {{ Lang.key2(5, "moi") }}
     * {{ MY_VAR }}
     * {{ MY_VAR ~ "25" }}
     * {% FOR MY_VAR IN MY_VARS %} ... {{MY_VAR.name}} ... {% ENDFOR %}
     * {% FOR key , val IN plop %} ... {{ key }} - {{ val.propertie }} ... {% ENDFOR %}
     * {% SET plop = ["plo", 5] %}
     * {% DUMP plop %}
     */
    protected function parse() {
        $this->content = preg_replace_callback('#\{\% *NOPARSE *\%\}(.*)\{\% *ENDNOPARSE *\%\}#isU', [$this,'_no_parse'], $this->content);
        $this->content = preg_replace('#\{\#(.*)\#\}#isU', '<?php /* $1 */ ?>', $this->content);
        $this->content = preg_replace_callback('#\{\% *IF +(.+) *\%\}#iU', [$this,'_ifReplace'], $this->content);
        $this->content = preg_replace_callback('#\{% *SET (.+) = (.+) *%\}#iU', [$this,'_setReplace'], $this->content);
        $this->content = preg_replace_callback('#\{% *DUMP (.+) *%\}#iU', [$this,'_dumpReplace'], $this->content);
        $this->content = preg_replace_callback('#\{\% *HOOK.(.+) *\%\}#iU', [$this,'_callHook'], $this->content);
        $this->content = preg_replace_callback('#\{\{ *Lang.(.+) *\}\}#U', [$this,'_getLang'], $this->content);
        $this->content = preg_replace_callback('#\{\% *INCLUDE +(.+) *\%\}#iU', [$this,'_include'], $this->content);
        $this->content = preg_replace('#\{\{ *(.+) *\}\}#iU', '<?php $this->_show_var(\'$1\'); ?>', $this->content);
        $this->content = preg_replace_callback('#\{\% *FOR +(.+) +IN +(.+) *\%\}#i', [$this,'_replace_for'], $this->content);
        $this->content = preg_replace('#\{\% *ENDFOR *\%\}#i', '<?php endforeach; ?>', $this->content);
        $this->content = preg_replace('#\{\% *ENDIF *\%\}#i', '<?php } ?>', $this->content);
        $this->content = preg_replace('#\{\% *ELSE *\%\}#i', '<?php }else{ ?>', $this->content);
        $this->content = preg_replace_callback('#\{\% *ELSEIF +(.+) *\%\}#iU', [$this,'_elseifReplace'], $this->content);
        $this->content = str_replace('#/§&µ&§;#', '{', $this->content);
    }

    protected function _no_parse($matches) {
        return str_replace('{', '#/§&µ&§;#', htmlentities($matches[1]));
    }

    protected function _show_var($name) {
        echo $this->getVar($name, $this->data);
    }

    protected function simpleReplaceVar($var) {
        if (preg_match('#^([\=|\<|\>\(\)|\!&]{1,3}$|true|false)#i', $var)) {
            return $var;
        }
        if (!is_numeric($var)) {
            return '$this->getVar(\'' . $var . '\', $this->data)';
        }
        return $var;
    }

    protected function listReplaceVars($matches) {
        $end = '';
        $varList = explode(' ',trim($matches));
        foreach($varList as $v) {
            if ($v !== '') {
                $end .= $this->simpleReplaceVar($v);
            }
        }
        return $end;
    }

    protected function _ifReplace($matches) {
        return '<?php if(' . $this->listReplaceVars($matches[1]) . '){ ?>';
    }

    protected function _setReplace($matches) {
        return '<?php $this->data[\'' . $matches[1] .'\'] = $this->getVar(\'' . $matches[2] .'\', $this->data); ?>';
    }

    protected function _dumpReplace($matches) {
        return '<pre><?php var_dump($this->getVar(\'' . $matches[1] .'\', $this->data)); ?></pre>';
    }

    protected function _elseifReplace($matches) {
        return '<?php } elseif(' . $this->listReplaceVars($matches[1]) . '){ ?>';
    }

    protected function _include($matches) {
        $var = $matches[1];
        $file = $this->getVar($var, $this->data);
        if (file_exists($file)) {
            if (util::getFileExtension($file) === 'tpl') {
                $str = '$tpl = new Template(\'' . $file . '\'); echo $tpl->output();';
                return '<?php ' . $str . ' ?>';
            }
            return '<?php $core = core::getInstance(); include \'' . $file . '\'; ?>';
        }
    }

    protected function _callHook($matches) {
        $posAcc = strpos($matches[1], '(');
        $args = false;
        if ($posAcc !== false) {
            $args = substr($matches[1], $posAcc);
            $name = substr($matches[1], 0, $posAcc);
        } else {
            $name = $matches[1];
        }
        if ($args) {
            // Filter hook
            return '<?php echo core::getInstance()->callHook(\'' . $name . '\', $this->getVar(\'' . $args . '\', $this->data) ); ?>';
        }
        // Action Hook
        return '<?php core::getInstance()->callHook(\'' . $name . '\'); ?>';
    }
    
    protected function _getLang($matches) {
        $posAcc = strpos($matches[1], '(');
        $args = '';
        $name = $matches[1];
        if ($posAcc !== false) {
            $args = substr($name, $posAcc);
            $name = substr($name, 0, $posAcc);
        }
        $args = str_replace('(', '', $args);
        $args = str_replace(')', '', $args);
        if ($args !== '') {
            $params = '$this->getVar(\'' . $args . '\', $this->data)';
            return '<?php echo lang::get(\'' . $name . '\', ' . $params . '); ?>';
        } else {
            return '<?php echo lang::get(\'' . $name . '\'); ?>';
        }
    }

    protected function _replace_for($matches) {
        $parts = explode(',', $matches[1]);
        if (count($parts) === 1) {
            return '<?php foreach ($this->getVar(\'' . $matches[2] . '\', $this->data) as $' . $matches[1] . '): $this->data[\'' . $matches[1] . '\' ] = $' . $matches[1] .
                    '; ?>';
        } else {
            $parts[0] = trim($parts[0]);
            $parts[1] = trim($parts[1]);
            return '<?php foreach ($this->getVar(\'' . $matches[2] . '\', $this->data) as $' . $parts[0] . ' => $' . $parts[1] . ' ): $this->data[\'' . $parts[0] . '\' ] = $' . $parts[0] .
                    '; $this->data[\'' . $parts[1] . '\' ] = $' . $parts[1] . '; ?>';
        }
    }

    /**
     * Recursive method to get asked var, with capacity to determine children
     * like : parent.child.var
     * or : object.method(parameter1, parameter2)
     * or : array.object.propertie
     * ....
     *
     * @param string    Name of the asked var
     * @param mixed     Parent of the var
     * @return mixed    Asked var
     */
    protected function getVar($var, $parent) {
        $var = trim($var);
        if ($var === "" || $var === '""') return "";
        $concats = explode('~', $var);
        if (count($concats) > 1) {
            $content = '';
            foreach ($concats as $concat) {
                $content .= $this->getVar($concat, $this->data);
            }
            return $content;
        }
        // Check if the string is empty, true or false
        if ($var === '' || $var === 'true' || $var === 'false')
            return $var;

        // Check if the string is a string
        if (preg_match('#^"([^"]+)"$#ix', $var, $match)) {
            if (isset($match[1])) {
                // string
                return $match[1];
            }
        }

        // Check if the string is an array
        if (preg_match('#^\[ *(.+) *\]$#iU', $var, $matches)) {
            $parts = explode(',', $matches[1]);
            $arr = [];
            // Loop through the parts and get the variables
            foreach ($parts as $part) {
                if (preg_match('#^(.+) => (.+)$#iU', trim($part), $assArray)) {
                    // Associative array
                    $arr[$this->getVar($assArray[1], $this->data)] = $this->getVar($assArray[2], $this->data);
                } else {
                    // simple array
                    $arr[] = $this->getVar($part, $this->data);
                }
            }
            return $arr;
        }

        // Get the position of the parentheses
        $posAcc = strpos($var, '(');
        $args = '';
        if ($posAcc !== false) {
            // Separate the var and the arguments
            $args = substr($var, $posAcc);
            $var = substr($var, 0, $posAcc);
        }
        // Exploding separated variables
        $vars = explode(',', $var);
        if (count($vars) === 1) {
            // One var only
        } else {
            $arr = [];
            // Loop through the variables and make an array with them
            foreach ($vars as $v) {
                $arr[] = $this->getVar($v, $this->data);
            }
            return $arr;
        }

        // Get the parts
        $parts = explode('.', $var);
        if (count($parts) === 1) {
            // No child
            return $this->getSubVar($var . $args, $parent);
        } else {
            // At least 1 child
            $name = array_shift($parts);
            // Check if the name is a variable, callable, object, array or class
            if (!is_array($name) && !is_callable($name) && !is_object($name) && !isset($this->data[$name]) && !class_exists($name)) {
                // Unknown $name
                return false;
            }
                
            $new_parent = $this->getSubVar($name, $parent);
            // Glue resting $parts
            $var = join('.', $parts) . $args;
            // call recursive
            return $this->getVar($var, $new_parent);
        }
    }

    /**
     * Determine and return if asked var is var, attribut or method if parent
     * is array or object
     *
     * @param string    Name of the asked var
     * @param mixed     Parent of the var
     * @return mixed    Asked var
     */
    protected function getSubVar($var, $parent) {
        if (is_array($parent) && isset($parent[$var])) {
            return $parent[$var];
        }
        if (isset($parent->$var)) {
            // Attribut
            return $parent->$var;
        }
        // Test if var contain parameters
        $manyArgs = false;
        preg_match('#\((.+)\)#i', $var, $match);
        if (isset($match[1])) {
            $var = str_replace($match[0], "", $var);
            $args = true;
            $parts = $this->tExplode($match[1], ',');
            if (count($parts) > 1)
                $manyArgs = true;
        } else {
            // Delete empty ()
            $var = str_replace('(', '', $var);
            $var = str_replace(')', '', $var);
        }
        if ($manyArgs) {
            $arrArgs = [];
            foreach ($parts as $part) {
                $arrArgs[] = $this->getVar(trim($part), $this->data);
            }
            $args = $arrArgs;
        } elseif (isset($args) && $args == true) {
            $args = $this->getVar($match[1], $this->data);
        }
        if (is_object($parent) || (is_string($parent) && class_exists($parent))) {
            if (is_callable([$parent, $var])) {
                $rm = new \ReflectionMethod($parent, $var);
                if ($rm->isStatic()) {
                    if ($manyArgs)
                        return forward_static_call_array([$parent, $var], $args);
                    if (isset($args))
                        return forward_static_call_array([$parent, $var], [$args]);
                    return $parent::$var();
                }
                // Method
                if ($manyArgs) {
                    return call_user_func_array([$parent, $var], $args);
                }                    
                if (isset($args))
                    return call_user_func_array([$parent, $var], [$args]);
                return $parent->$var();
            }

            return false;
        }
        if (is_callable($var)) {
            // Function
            if ($manyArgs)
                return call_user_func_array($var, $args);
            if (isset($args))
                return call_user_func($var, $args);
            return call_user_func($var);
        }
        // Nothing
        if ($var === '') {
            // Only Args
            if ($manyArgs)
                return $args;
            return $args;
        }
        if (is_numeric($var)) {
            return $var * 1;
        }
        return $var;
    }

    /**
     * Add Globals vars to datas template
     */
    protected function addGlobalsToVars() {
        foreach (self::$const as $key => $value) {
            $this->data[$key] = $value;
        }
    }

    /**
     * Explodes a string into an array, ignoring delimiters inside parentheses () or []
     * 
     * @param string $str The string to explode. 
     * @param string $delimiter The delimiter to split on.
     * @return array The exploded array.
     */
    protected function tExplode(string $str, string $delimiter): array
    {
        $ret = array();
        $in_parenths = 0;
        $pos = 0;
        for ($i = 0; $i < strlen($str); $i++) {
            $c = $str[$i];
            if ($c == $delimiter && $in_parenths < 1) {
                $ret[] = substr($str, $pos, $i - $pos);
                $pos = $i + 1;
            } elseif ($c === '(' || $c === '[')
                $in_parenths++;
            elseif ($c === ')' || $c === ']')
                $in_parenths--;
        }
        if ($pos > 0)
            $ret[] = substr($str, $pos);
        if (empty($ret)) {
            $ret[0] = $str;
        }
        return $ret;
    }


}