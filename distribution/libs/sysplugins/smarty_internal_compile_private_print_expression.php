<?php

/**
 * Smarty Internal Plugin Compile Print Expression
 *
 * Compiles any tag which will output an expression or variable
 *
 *
 * @package Compiler
 * @author Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Print Expression Class
 *
 *
 * @package Compiler
 */
class Smarty_Internal_Compile_Private_Print_Expression extends Smarty_Internal_CompileBase
{

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array('assign');

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $option_flags = array('nocache', 'nofilter');

    /**
     * Compiles code for gererting output from any expression
     *
     * @param array $args      array with attributes from parser
     * @param object $compiler  compiler object
     * @param array $parameter array with compilation parameter
     * @throws SmartyException
     * @return string compiled code
     */
    public function compile($args, $compiler, $parameter)
    {
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);
        // nocache option
        if ($_attr['nocache'] === true) {
            $compiler->tag_nocache = true;
        }
        // filter handling
        if ($_attr['nofilter'] === true) {
            $_filter = 'false';
        } else {
            $_filter = 'true';
        }
        $this->iniTagCode($compiler);
        if (isset($_attr['assign'])) {
            // assign output to variable
            $this->php("\$_smarty_tpl->assign({$_attr['assign']},{$parameter['value']});")->newline();
        } else {
            $this->php("echo ");
            // display value
            $output = $parameter['value'];
            // tag modifier
            if (!empty($parameter['modifierlist'])) {
                $output = $compiler->compileTag('private_modifier', array(), array('modifierlist' => $parameter['modifierlist'], 'value' => $output));
            }
            if (!$_attr['nofilter']) {
                // default modifier
                if (!empty($compiler->template->default_modifiers)) {
                    if (empty($compiler->default_modifier_list)) {
                        $modifierlist = array();
                        foreach ($compiler->template->default_modifiers as $key => $single_default_modifier) {
                            preg_match_all('/(\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'|"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|:|[^:]+)/', $single_default_modifier, $mod_array);
                            for ($i = 0, $count = count($mod_array[0]); $i < $count; $i++) {
                                if ($mod_array[0][$i] != ':') {
                                    $modifierlist[$key][] = $mod_array[0][$i];
                                }
                            }
                        }
                        $compiler->default_modifier_list = $modifierlist;
                    }
                    $output = $compiler->compileTag('private_modifier', array(), array('modifierlist' => $compiler->default_modifier_list, 'value' => $output));
                }
                // autoescape html
                if ($compiler->template->escape_html) {
                    $output = "htmlspecialchars({$output}, ENT_QUOTES, '" . addslashes(Smarty::$_CHARSET) . "')";
                }
                // loop over registerd filters
                if (!empty($compiler->template->registered_filters[Smarty::FILTER_VARIABLE])) {
                    foreach ($compiler->template->registered_filters[Smarty::FILTER_VARIABLE] as $key => $function) {
                        if ($function instanceof Closure) {
                            $output = "\$_smarty_tpl->registered_filters[Smarty::FILTER_VARIABLE][{$key}]({$output},\$_smarty_tpl)";
                        } else if (!is_array($function)) {
                            $output = "{$function}({$output},\$_smarty_tpl)";
                        } else if (is_object($function[0])) {
                            $output = "\$_smarty_tpl->registered_filters[Smarty::FILTER_VARIABLE]['{$key}'][0]->{$function[1]}({$output},\$_smarty_tpl)";
                        } else {
                            $output = "{$function[0]}::{$function[1]}({$output},\$_smarty_tpl)";
                        }
                    }
                }
                // auto loaded filters
                if (isset($compiler->template->autoload_filters[Smarty::FILTER_VARIABLE])) {
                    foreach ((array)$compiler->template->autoload_filters[Smarty::FILTER_VARIABLE] as $name) {
                        $result = $this->compile_output_filter($compiler, $name, $output);
                        if ($result !== false) {
                            $output = $result;
                        } else {
                            // not found, throw exception
                            throw new SmartyException("Unable to load filter '{$name}'");
                        }
                    }
                }
                if (isset($compiler->template->variable_filters)) {
                    foreach ($compiler->template->variable_filters as $filter) {
                        if (count($filter) == 1 && ($result = $this->compile_output_filter($compiler, $filter[0], $output)) !== false) {
                            $output = $result;
                        } else {
                            $output = $compiler->compileTag('private_modifier', array(), array('modifierlist' => array($filter), 'value' => $output));
                        }
                    }
                }
            }

            $compiler->has_output = true;
            $this->raw(" {$output};")->newline();
        }
        return $this->returnTagCode($compiler);
    }

    /**
     * @param object $compiler compiler object
     * @param string $name     name of variable filter
     * @param string $output   embedded output
     * @return string
     */
    private function compile_output_filter($compiler, $name, $output)
    {
        $plugin_name = "smarty_variablefilter_{$name}";
        $path = $compiler->template->_loadPlugin($plugin_name, false);
        if ($path) {
            if ($compiler->template->caching) {
                $compiler->required_plugins['nocache'][$name][Smarty::FILTER_VARIABLE]['file'] = $path;
                $compiler->required_plugins['nocache'][$name][Smarty::FILTER_VARIABLE]['function'] = $plugin_name;
            } else {
                $compiler->required_plugins['compiled'][$name][Smarty::FILTER_VARIABLE]['file'] = $path;
                $compiler->required_plugins['compiled'][$name][Smarty::FILTER_VARIABLE]['function'] = $plugin_name;
            }
        } else {
            // not found
            return false;
        }
        return "{$plugin_name}({$output},\$_smarty_tpl)";
    }

}
