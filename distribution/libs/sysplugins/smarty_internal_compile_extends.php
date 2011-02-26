<?php

/**
 * Smarty Internal Plugin Compile extend
 * 
 * Compiles the {extends} tag
 * 
 * @package Smarty
 * @subpackage compiler
 * @author Uwe Tews 
 */

/**
 * Smarty Internal Plugin Compile extend Class
 */
class Smarty_Internal_Compile_Extends extends Smarty_Internal_CompileBase {
	// attribute definitions
    public $required_attributes = array('file');
    public $shorttag_order = array('file');

    /**
     * Compiles code for the {extends} tag
     * 
     * @param array $args array with attributes from parser
     * @param object $compiler $compiler object
     * @return string compiled code
     */
    public function compile($args, $compiler)
    {
        $this->_rdl = preg_quote($compiler->smarty->right_delimiter);
        $this->_ldl = preg_quote($compiler->smarty->left_delimiter);
        $filepath = $compiler->template->source->filepath;
        // check and get attributes
        $_attr = $this->_get_attributes($compiler, $args);
        if ($_attr['nocache'] === true) {
        	$compiler->trigger_template_error('nocache option not allowed', $compiler->lex->taglineno);
        }

        $_smarty_tpl = $compiler->template; 
        $include_file = null;
        if (strpos($_attr['file'],'$_tmp') !== false) {
        	$compiler->trigger_template_error('illegal value for file attribute', $compiler->lex->taglineno);
        }
        eval('$include_file = ' . $_attr['file'] . ';'); 
        // create template object
        $_template = new $compiler->smarty->template_class($include_file, $compiler->smarty, $compiler->template); 
        // save file dependency
        if (in_array($_template->source->type,array('eval','string'))) {
        	$template_sha1 = sha1($include_file);
    	} else {
        	$template_sha1 = sha1($_template->source->filepath);
    	}
        if (isset($compiler->template->properties['file_dependency'][$template_sha1])) {
            $compiler->trigger_template_error("illegal recursive call of \"{$include_file}\"",$compiler->lex->line-1);
        } 
        $compiler->template->properties['file_dependency'][$template_sha1] = array($_template->source->filepath, $_template->source->timestamp,$_template->source->type);
        $_content = substr($compiler->template->source->content,$compiler->lex->counter-1);
        if (preg_match_all("!({$this->_ldl}block\s(.+?){$this->_rdl})!", $_content, $s) !=
                preg_match_all("!({$this->_ldl}/block{$this->_rdl})!", $_content, $c)) {
            $compiler->trigger_template_error('unmatched {block} {/block} pairs');
        } 
        preg_match_all("!{$this->_ldl}block\s(.+?){$this->_rdl}|{$this->_ldl}/block{$this->_rdl}!", $_content, $_result, PREG_OFFSET_CAPTURE);
        $_result_count = count($_result[0]);
        $_start = 0;
        while ($_start < $_result_count) {
            $_end = 0;
            $_level = 1;
            while ($_level != 0) {
                $_end++;
                if (!strpos($_result[0][$_start + $_end][0], '/')) {
                    $_level++;
                } else {
                    $_level--;
                } 
            } 
            $_block_content = str_replace($compiler->smarty->left_delimiter . '$smarty.block.parent' . $compiler->smarty->right_delimiter, '%%%%SMARTY_PARENT%%%%',
                substr($_content, $_result[0][$_start][1] + strlen($_result[0][$_start][0]), $_result[0][$_start + $_end][1] - $_result[0][$_start][1] - + strlen($_result[0][$_start][0])));
            Smarty_Internal_Compile_Block::saveBlockData($_block_content, $_result[0][$_start][0], $compiler->template, $filepath);
            $_start = $_start + $_end + 1;
        } 
        // TODO: (utews) can this be optimized?
        $compiler->template->source->content = $_template->source->content;
        $compiler->template->source->filepath = $_template->source->filepath;
        $compiler->abort_and_recompile = true;
        return '';
    } 

} 
?>