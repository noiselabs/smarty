<?php 
/**
* Smarty Internal Plugin Compile Foreach Else
*
* Compiles the {foreachelse} tag 
* @package Smarty
* @subpackage Compiler
* @author Uwe Tews
*/
/**
* Smarty Internal Plugin Compile Foreachelse Class
*/ 
class Smarty_Internal_Compile_Foreachelse extends Smarty_Internal_CompileBase {
    /**
    * Compiles code for the {foreachelse} tag
    * 
    * @param array $args array with attributes from parser
    * @param object $compiler compiler object
    * @return string compiled code
    */
    public function compile($args, $compiler)
    {
        $this->compiler = $compiler; 
        // check and get attributes
        $_attr = $this->_get_attributes($args);

        list($_open_tag, $this->compiler->nocache) = $this->_close_tag(array('foreach'));
        $this->_open_tag('foreachelse',array('foreachelse', $this->compiler->nocache));

        return "<?php }} else { ?>";
    } 
} 

?>
