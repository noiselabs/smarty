<?php
/**
* Smarty PHPunit tests compilation of {debug} tag
* 
* @package PHPunit
* @author Uwe Tews 
*/

require_once SMARTY_DIR . 'Smarty.class.php';

/**
* class for {debug} tag tests
*/
class CompileDebugTests extends PHPUnit_Framework_TestCase {
    public function setUp()
    {
        $this->smarty = new Smarty();
        $this->smarty->error_reporting = E_ALL;
        $this->smarty->enableSecurity();
        $this->smarty->force_compile = true;
        $this->old_error_level = error_reporting();
    } 

    public function tearDown()
    {
        error_reporting($this->old_error_level);
        unset($this->smarty);
        Smarty::$template_objects = null;
    } 

    /**
    * test debug tag
    */
    public function testDebugTag()
    {
        $tpl = $this->smarty->createTemplate("string:{debug}");
        $_contents = $this->smarty->fetch($tpl);
        $this->assertContains("Smarty Debug Console", $_contents);
    } 
    /**
    * test debug property
    */
//    {
//        $this->smarty->debugging = true;
//        $tpl = $this->smarty->createTemplate("string:hello world");
//        $_contents = $this->smarty->fetch($tpl);
//        $this->assertContains("Smarty Debug Console", $_contents);
//    } 
} 

?>