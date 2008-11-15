<?php
/**
* Smarty Internal Plugin Compile Print Expression
* 
* Compiles any tag which will output an expression or variable
* 
* @package Smarty
* @subpackage Compiler
* @author Uwe Tews 
*/
/**
* Smarty Internal Plugin Compile Print Expression Class
*/
class Smarty_Internal_Compile_Print_Expression extends Smarty_Internal_CompileBase {
    /**
    * Compiles code for gererting output from any expression
    * 
    * @param array $args array with attributes from parser
    * @return string compiled code
    */
    public function compile($args)
    {
        $this->required_attributes = array('value');
        $this->optional_attributes = array('assign'); 
        // check and get attributes
        $_attr = $this->_get_attributes($args);

        if (isset($_attr['assign'])) {
            // assign output to variable
            $output = '<?php $_smarty_tpl->assign(' . $_attr['assign'] . ',' . $_attr['value'] . ');?>';
        } else {
            // display value
            $this->compiler->has_output = true;
            $output = '<?php echo ' . $_attr['value'] . ';?>';
        } 
        return $output;
    } 
} 

?>
