<?php
/**
 * Smarty PHPunit test suite
 *
 * @package PHPunit
 * @author Uwe Tews
 */



define ('SMARTY_DIR', realpath('../../distribution/libs/') . '/');

require_once SMARTY_DIR . 'SmartyBC.class.php';

/**
 * class for running test suite
 */
class SmartyTests
{
    static $smarty = null;
    static $smartyBC = null;
    static $smartyBC31 = null;

    protected static function _init($smarty)
    {
        $smarty->setTemplateDir('.' . DS . 'templates' . DS);
        $smarty->setCompileDir('.' . DS . 'templates_c' . DS);
        $smarty->setPluginsDir(SMARTY_PLUGINS_DIR);
        $smarty->setCacheDir('.' . DS . 'cache' . DS);
        $smarty->setConfigDir('.' . DS . 'configs' . DS);
        foreach (Smarty::$template_objects as $tpl) {
            $tpl->cleanPointer();
            unset($tpl);
        }
        Smarty::$template_objects = array();
        $smarty->tpl_vars = new Smarty_Variable_Container($smarty);
        $smarty->template_functions = array();
        $smarty->force_compile = false;
        $smarty->force_cache = false;
        $smarty->auto_literal = true;
        $smarty->caching = false;
        $smarty->debugging = false;
        $smarty->registered_plugins = array();
        $smarty->default_plugin_handler_func = null;
        $smarty->registered_objects = array();
        $smarty->default_modifiers = array();
        $smarty->registered_filters = array();
        $smarty->autoload_filters = array();
        $smarty->escape_html = false;
        $smarty->use_sub_dirs = false;
        $smarty->config_overwrite = true;
        $smarty->config_booleanize = true;
        $smarty->config_read_hidden = true;
        $smarty->security_policy = null;
        $smarty->left_delimiter = '{';
        $smarty->right_delimiter = '}';
        $smarty->php_handling = Smarty::PHP_PASSTHRU;
        $smarty->enableSecurity();
        $smarty->error_reporting = null;
        $smarty->error_unassigned = Smarty::UNASSIGNED_NOTICE;
        $smarty->caching_type = 'file';
        $smarty->cache_locking = false;
        $smarty->cache_id = null;
        $smarty->compile_id = null;
        $smarty->default_resource_type = 'file';
        Smarty_CacheResource::$resources = array();
    }

    public static function init()
    {
        error_reporting(E_ALL | E_STRICT);
        self::_init(SmartyTests::$smarty);
        self::_init(SmartyTests::$smartyBC);
        self::_init(SmartyTests::$smartyBC31);
        Smarty_Resource::$sources = array();
        Smarty_Compiled::$compileds = array();
        Smarty::$global_tpl_vars = new Smarty_Variable_Container();
        Smarty::$_smarty_vars = array();
        Smarty_CacheResource::$resources = array();
        SmartyTests::$smartyBC->registerPlugin('block', 'php', 'smarty_php_tag');
    }
}

class  PHPUnit_Framework_TestCase
{
    public $current_function = '';
    public $error_functions = array();
    
    public function __construct() {
        $this->setUp();
    }
    public function __call($a,$b) {
        $this->error();
        echo '<br>Missing method  '.$a;
        return true;
    }
    
    public function assertContains($a,$b)
    {
        if (strpos($b,$a) === false) {
            $this->error();
            echo '<br><br>result:<br>'.$b;
            echo '<br><br>should conctain:<br>'.$a;           
        }
    }

    public function assertNotContains($a,$b)
    {
        if (strpos($b,$a) !== false) {
            $this->error();
            echo '<br><br>result:<br>'.$b;
            echo '<br><br>should not conctain:<br>'.$a;           
        }
    }

    public function assertEquals($a,$b)
    {
        if ($a !== $b) {
            $this->error();
            echo '<br><br>expected:<br>'.$a;
            echo '<br><br>is:<br>'.$b;           
        }
    }

    public function assertFalse($a)
    {
        if ($a !== false) {
            $this->error();
            echo '<br><br>result was not false';
        }
    }
   public function assertTrue($a)
    {
        if ($a !== true) {
            $this->error();
            echo '<br><br>result was not true';
        }
    }
    
    public function error(){
        echo '<br><br><br>ERROR in test:  '.$this->current_function;
        $this->error_functions[] = $this->current_function; 
    }
}

SmartyTests::$smartyBC = new SmartyBC();
SmartyTests::$smartyBC31 = new SmartyBC31();
SmartyTests::$smarty = new Smarty();