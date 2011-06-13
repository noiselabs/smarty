<?php
/**
 * Smarty Internal Plugin Template
 *
 * This file contains the Smarty template engine
 *
 * @package Smarty
 * @subpackage Template
 * @author Uwe Tews
 */

/**
 * Main class with template data structures and methods
 *
 * @package Smarty
 * @subpackage Template
 *
 * @property mixed $source
 * @property mixed $compiled
 * @property mixed $cached
 */
class Smarty_Internal_Template extends Smarty_Internal_TemplateBase {

    /**
     * cache_id
     * @var string
     */
    public $cache_id = null;
    /**
     * $compile_id
     * @var string
     */
    public $compile_id = null;
    /**
     * caching enabled
     * @var boolean
     */
    public $caching = null;
    /**
     * cache lifetime in seconds
     * @var integer
     */
    public $cache_lifetime = null;
    /**
     * Class name
     * @var string
     */
    public $cacher_class = null;
    /**
     * caching type
     *
     * Must be an element of $cache_resource_types.
     *
     * @var string
     */
    public $caching_type = null;
    /**
     * force cache file creation
     * @var boolean
     */
    public $forceNocache = false;
    /**
     * Template resource
     * @var type
     */
    public $template_resource = null;
    /**
     * Compiled template
     * @todo Missing documentation
     */
    public $compiled_template = null;
    /**
     * @todo Missing documentation
     */
    public $mustCompile = null;
    /**
     * @var bool
     * @todo Missing documentation
     */
    public $suppressHeader = false;
    /**
     * @var bool
     * @todo Missing documentation
     */
    public $suppressFileDependency = false;
    /**
     * @var bool
     * @todo Missing documentation
     */
    public $has_nocache_code = false;
    /**
     * @var bool
     * @todo Missing documentation
     */
    public $write_compiled_code = true;
    /**
     * storage for plugin
     * @var array
     */
    public $plugin_data = array();
    /**
     * special properties
     * @var array
     */
    public $properties = array('file_dependency' => array(),
        'nocache_hash' => '',
        'function' => array());
    /**
     * required plugins
     * @var array
     */
    public $required_plugins = array('compiled' => array(), 'nocache' => array());
    /**
     * @var mixed
     * @todo Missing documentation
     */
    public $saved_modifier = null;
    /**
     * Global smarty instance
     * @var Smarty
     */
    public $smarty = null;
    /**
     * blocks for template inheritance
     * @var array
     */
    public $block_data = array();
    /**
     * variable filters
     * @var array
     */
    public $variable_filters = array();
    /**
     * optional log of tag/attributes
     * @var array
     */
    public $used_tags = array();

    /**
     * Create template data object
     *
     * Some of the global Smarty settings copied to template scope
     * It load the required template resources and cacher plugins
     *
     * @param string                   $template_resource template resource string
     * @param Smarty                   $smarty            Smarty instance
     * @param Smarty_Internal_Template $_parent           back pointer to parent object with variables or null
     * @param mixed                    $_cache_id cache   id or null
     * @param mixed                    $_compile_id       compile id or null
     * @param bool                     $_caching          use caching?
     * @param int                      $_cache_lifetime   cache life-time in seconds
     */
    public function __construct($template_resource, $smarty, $_parent = null, $_cache_id = null, $_compile_id = null, $_caching = null, $_cache_lifetime = null)
    {
        $this->smarty = &$smarty;
        // Smarty parameter
        $this->cache_id = $_cache_id === null ? $this->smarty->cache_id : $_cache_id;
        $this->compile_id = $_compile_id === null ? $this->smarty->compile_id : $_compile_id;
        $this->caching = $_caching === null ? $this->smarty->caching : $_caching;
        if ($this->caching === true)
            $this->caching = Smarty::CACHING_LIFETIME_CURRENT;
        $this->cache_lifetime = $_cache_lifetime === null ? $this->smarty->cache_lifetime : $_cache_lifetime;
        $this->parent = $_parent;
        // Template resource
        $this->template_resource = $template_resource;
        // copy block data of template inheritance
        if ($this->parent instanceof Smarty_Internal_Template) {
            $this->block_data = $this->parent->block_data;
        }
    }

    /**
     * Returns if the current template must be compiled by the Smarty compiler
     *
     * It does compare the timestamps of template source and the compiled templates and checks the force compile configuration
     *
     * @return boolean true if the template must be compiled
     */
    public function mustCompile()
    {
        if (!$this->source->exists) {
            throw new SmartyException("Unable to load template {$this->source->type} '{$this->source->name}'");
        }
        if ($this->mustCompile === null) {
            $this->mustCompile = (!$this->source->uncompiled && ($this->smarty->force_compile || $this->source->recompiled || $this->compiled->timestamp === false ||
                    ($this->smarty->compile_check && $this->compiled->timestamp < $this->source->timestamp)));
        }
        return $this->mustCompile;
    }

    /**
     * Returns the compiled template
     *
     * It checks if the template must be compiled or just read from the template resource
     *
     * @return string the compiled template
     */
    public function getCompiledTemplate()
    {
        // see if template needs compiling.
        if ($this->mustCompile()) {
            $this->compileTemplateSource();
        }
        return!$this->source->recompiled && !$this->source->uncompiled ? $this->compiled->content : false;
    }

    /**
     * Compiles the template
     *
     * If the template is not evaluated the compiled template is saved on disk
     */
    public function compileTemplateSource()
    {
        if (!$this->source->recompiled) {
            $this->properties['file_dependency'] = array();
            if ($this->source->components) {
                // uses real resource for file dependency
                $source = end($this->source->components);
                $this->properties['file_dependency'][$this->source->uid] = array($this->source->filepath, $this->source->timestamp, $source->type);
            } else {
                $this->properties['file_dependency'][$this->source->uid] = array($this->source->filepath, $this->source->timestamp, $this->source->type);
            }
        }
        if ($this->smarty->debugging) {
            Smarty_Internal_Debug::start_compile($this);
        }
        // compile locking
        if ($this->smarty->compile_locking && !$this->source->recompiled) {
            if ($saved_timestamp = $this->compiled->timestamp) {
                touch($this->compiled->filepath);
            }
        }
        // call compiler
        try {
            $code = $this->compiler->compileTemplate($this);
        } catch (Exception $e) {
            // restore old timestamp in case of error
            if ($this->smarty->compile_locking && !$this->source->recompiled && $saved_timestamp) {
                touch($this->compiled->filepath, $saved_timestamp);
            }
            throw $e;
        }
        // compiling succeded
        if (!$this->source->recompiled && $this->write_compiled_code) {
            // write compiled template
            $_filepath = $this->compiled->filepath;
            if ($_filepath === false)
                throw new SmartyException('getCompiledFilepath() did not return a destination to save the compiled template to');
            Smarty_Internal_Write_File::writeFile($_filepath, $code, $this->smarty);
            $this->compiled->exists = true;
            $this->compiled->isCompiled = true;
        }
        if ($this->smarty->debugging) {
            Smarty_Internal_Debug::end_compile($this);
        }
        // release compiler object to free memory
        unset($this->compiler);
    }

    /**
     * Writes the cached template output
     *
     * @return bool
     */
    public function writeCachedContent($content)
    {
        if ($this->source->recompiled || !($this->caching == Smarty::CACHING_LIFETIME_CURRENT || $this->caching == Smarty::CACHING_LIFETIME_SAVED)) {
            // don't write cache file
            return false;
        }
        $this->properties['cache_lifetime'] = $this->cache_lifetime;
        $this->properties['unifunc'] = 'content_' . uniqid();
        return $this->cached->write($this, $this->createTemplatePropertyHeader($content, true));
    }

    /**
     * Get Subtemplate Content
     *
     * @param string  $template       the resource handle of the template file
     * @param mixed   $cache_id       cache id to be used with this template
     * @param mixed   $compile_id     compile id to be used with this template
     * @param integer $caching        cache mode
     * @param integer $cache_lifetime life time of cache data
     * @param array   $vars optional  variables to assign
     * @param int     $parent_scope   scope in which {include} should execute
     * @returns string template content
     */
    public function getSubTemplate($template, $cache_id, $compile_id, $caching, $cache_lifetime, $data, $parent_scope)
    {
        // already in template cache?
        $_templateId = sha1($template . $cache_id . $compile_id);
        if (isset($this->smarty->template_objects[$_templateId])) {
            // clone cached template object because of possible recursive call
            $tpl = clone $this->smarty->template_objects[$_templateId];
            $tpl->parent = $this;
            $tpl->caching = $caching;
            $tpl->cache_lifetime = $cache_lifetime;
        } else {
            $tpl = new $this->smarty->template_class($template, $this->smarty, $this, $cache_id, $compile_id, $caching, $cache_lifetime);
        }
        // get variables from calling scope
        if ($parent_scope == Smarty::SCOPE_LOCAL) {
            $tpl->tpl_vars = $this->tpl_vars;
        } elseif ($parent_scope == Smarty::SCOPE_PARENT) {
            $tpl->tpl_vars = &$this->tpl_vars;
        } elseif ($parent_scope == Smarty::SCOPE_GLOBAL) {
            $tpl->tpl_vars = &Smarty::$global_tpl_vars;
        } elseif (($scope_ptr = $this->getScopePointer($parent_scope)) == null) {
            $tpl->tpl_vars = &$this->tpl_vars;
        } else {
            $tpl->tpl_vars = &$scope_ptr->tpl_vars;
        }
        $tpl->config_vars = $this->config_vars;
        if (!empty($data)) {
            // set up variable values
            foreach ($data as $_key => $_val) {
                $tpl->tpl_vars[$_key] = new Smarty_variable($_val);
            }
        }
        return $tpl->fetch();
    }

    /**
     * Create property header
     *
     * @param string $content
     * @param bool   $cache
     * @return string
     * @todo Missing documentation
     */
    public function createTemplatePropertyHeader($content = '', $cache = false)
    {
        $plugins_string = '';
        // include code for plugins
        if (!$cache) {
            if (!empty($this->required_plugins['compiled'])) {
                $plugins_string = '<?php ';
                foreach ($this->required_plugins['compiled'] as $tmp) {
                    foreach ($tmp as $data) {
                        $plugins_string .= "if (!is_callable('{$data['function']}')) include '{$data['file']}';\n";
                    }
                }
                $plugins_string .= '?>';
            }
            if (!empty($this->required_plugins['nocache'])) {
                $this->has_nocache_code = true;
                $plugins_string .= "<?php echo '/*%%SmartyNocache:{$this->properties['nocache_hash']}%%*/<?php ";
                foreach ($this->required_plugins['nocache'] as $tmp) {
                    foreach ($tmp as $data) {
                        $plugins_string .= "if (!is_callable(\'{$data['function']}\')) include \'{$data['file']}\';\n";
                    }
                }
                $plugins_string .= "?>/*/%%SmartyNocache:{$this->properties['nocache_hash']}%%*/';?>\n";
            }
        }
        // build property code
        $this->properties['has_nocache_code'] = $this->has_nocache_code;
        $output = '';
        if (!$this->source->recompiled) {
            $output = "<?php /*%%SmartyHeaderCode:{$this->properties['nocache_hash']}%%*/";
            if ($this->smarty->direct_access_security) {
                $output .= "if(!defined('SMARTY_DIR')) exit('no direct access allowed');\n";
            }
        }
        if ($cache) {
            // remove compiled code of{function} definition
            unset($this->properties['function']);
            if (!empty($this->smarty->template_functions)) {
                // copy code of {function} tags called in nocache mode
                foreach ($this->smarty->template_functions as $name => $function_data) {
                    if (isset($function_data['called_nocache'])) {
                        unset($function_data['called_nocache'], $this->smarty->template_functions[$name]['called_nocache']);
                        $this->properties['function'][$name] = $function_data;
                    }
                }
            }
        }
        $this->properties['version'] = Smarty::SMARTY_VERSION;
        if (!isset($this->properties['unifunc'])) {
            $this->properties['unifunc'] = 'content_' . uniqid();
        }
        if (!$this->source->recompiled) {
            $output .= "\$_valid = \$_smarty_tpl->decodeProperties(" . var_export($this->properties, true) . ',' . ($cache ? 'true' : 'false') . "); /*/%%SmartyHeaderCode%%*/?>\n";
        }
        if (!$this->source->recompiled) {
            $output .= '<?php if ($_valid && !is_callable(\'' . $this->properties['unifunc'] . '\')) {function ' . $this->properties['unifunc'] . '($_smarty_tpl) {?>';
        }
        $output .= $plugins_string;
        $output .= $content;
        if (!$this->source->recompiled) {
            $output .= '<?php }} ?>';
        }
        return $output;
    }

    /**
     * Decode saved properties from compiled template and cache files
     *
     * @param array $properties
     * @param bool  $cache
     * @return bool
     * @todo Missing documentation
     */
    public function decodeProperties($properties, $cache = false)
    {
        $this->has_nocache_code = $properties['has_nocache_code'];
        $this->properties['nocache_hash'] = $properties['nocache_hash'];
        if (isset($properties['cache_lifetime'])) {
            $this->properties['cache_lifetime'] = $properties['cache_lifetime'];
        }
        if (isset($properties['file_dependency'])) {
            $this->properties['file_dependency'] = array_merge($this->properties['file_dependency'], $properties['file_dependency']);
        }
        if (!empty($properties['function'])) {
            $this->properties['function'] = array_merge($this->properties['function'], $properties['function']);
            $this->smarty->template_functions = array_merge($this->smarty->template_functions, $properties['function']);
        }
        $this->properties['version'] = $properties['version'];
        $this->properties['unifunc'] = $properties['unifunc'];
        // check file dependencies at compiled code
        $is_valid = true;
        if ($this->properties['version'] != Smarty::SMARTY_VERSION) {
            $is_valid = false;
        } else if (((!$cache && $this->smarty->compile_check) || $this->smarty->compile_check === true || $this->smarty->compile_check === Smarty::COMPILECHECK_ON) && !empty($this->properties['file_dependency'])) {
            foreach ($this->properties['file_dependency'] as $_file_to_check) {
                if ($_file_to_check[2] == 'file') {
                    // file and php types can be checked without loading the respective resource handlers
                    $mtime = filemtime($_file_to_check[0]);
                } elseif ($_file_to_check[2] == 'string') {
                    continue;
                } else {
                    $source = Smarty_Resource::source($this, $this->smarty, $_file_to_check[0]);
                    $mtime = $source->timestamp;
                }
                if ($mtime > $_file_to_check[1]) {
                    $is_valid = false;
                    break;
                }
            }
        }
        if ($cache) {
            $this->cached->valid = $is_valid;
        } else {
            $this->mustCompile = !$is_valid;
        }
        return $is_valid;
    }

    /**
     * creates a local Smarty variable for array assignments
     *
     * @param string $tpl_var
     * @param bool   $nocache
     * @param int    $scope
     * @todo Missing documentation
     */
    public function createLocalArrayVariable($tpl_var, $nocache = false, $scope = Smarty::SCOPE_LOCAL)
    {
        if (!isset($this->tpl_vars[$tpl_var])) {
            $this->tpl_vars[$tpl_var] = new Smarty_variable(array(), $nocache, $scope);
        } else {
            $this->tpl_vars[$tpl_var] = clone $this->tpl_vars[$tpl_var];
            if ($scope != Smarty::SCOPE_LOCAL) {
                $this->tpl_vars[$tpl_var]->scope = $scope;
            }
            if (!(is_array($this->tpl_vars[$tpl_var]->value) || $this->tpl_vars[$tpl_var]->value instanceof ArrayAccess)) {
                settype($this->tpl_vars[$tpl_var]->value, 'array');
            }
        }
    }

    /**
     * get template variable scope
     *
     * @param int $scope
     * @return array
     * @todo Missing documentation
     */
    public function &getScope($scope)
    {
        if ($scope == Smarty::SCOPE_PARENT && !empty($this->parent)) {
            return $this->parent->tpl_vars;
        } elseif ($scope == Smarty::SCOPE_ROOT && !empty($this->parent)) {
            $ptr = $this->parent;
            while (!empty($ptr->parent)) {
                $ptr = $this->parent;
            }
            return $ptr->tpl_vars;
        } elseif ($scope == Smarty::SCOPE_GLOBAL) {
            return Smarty::$global_tpl_vars;
        }
        $null = null;
        return $null;
    }

    /**
     * get template scope pointer
     *
     * @param int $scope
     * @return Smarty_Internal_Template
     * @todo Missing documentation
     */
    public function getScopePointer($scope)
    {
        if ($scope == Smarty::SCOPE_PARENT && !empty($this->parent)) {
            return $this->parent;
        } elseif ($scope == Smarty::SCOPE_ROOT && !empty($this->parent)) {
            $ptr = $this->parent;
            while (!empty($ptr->parent)) {
                $ptr = $this->parent;
            }
            return $ptr;
        }
        return null;
    }

    /**
     * [util function] counts an array, arrayaccess/traversable or PDOStatement object
     *
     * @param mixed $value
     * @return int the count for arrays and objects that implement countable, 1 for other objects that don't, and 0 for empty elements
     */
    public function _count($value)
    {
        if (is_array($value) === true || $value instanceof Countable) {
            return count($value);
        } elseif ($value instanceof Iterator) {
            $value->rewind();
            if ($value->valid()) {
                return iterator_count($value);
            }
        } elseif ($value instanceof PDOStatement) {
            return $value->rowCount();
        } elseif ($value instanceof Traversable) {
            return iterator_count($value);
        } elseif ($value instanceof ArrayAccess) {
            if ($value->offsetExists(0)) {
                return 1;
            }
        } elseif (is_object($value)) {
            return count($value);
        }
        return 0;
    }

    /**
     * set Smarty property in template context
     *
     * @param string $property_name property name
     * @param mixed  $value         value
     */
    public function __set($property_name, $value)
    {
        switch ($property_name) {
            case 'source':
            case 'compiled':
            case 'cached':
            case 'compiler':
                $this->$property_name = $value;
                return;

            // FIXME: routing of template -> smarty attributes
            default:
                if (property_exists($this->smarty, $property_name)) {
                    $this->smarty->$property_name = $value;
                    return;
                }
        }

        throw new SmartyException("invalid template property '$property_name'.");
    }

    /**
     * get Smarty property in template context
     *
     * @param string $property_name property name
     */
    public function __get($property_name)
    {
        switch ($property_name) {
            case 'source':
                if (empty($this->template_resource)) {
                    throw new SmartyException("Unable to parse resource name \"{$this->template_resource}\"");
                }
                $this->source = Smarty_Resource::source($this);
                // cache template object under a unique ID
                // do not cache eval resources
                if ($this->source->type != 'eval') {
                    $this->smarty->template_objects[sha1($this->template_resource . $this->cache_id . $this->compile_id)] = $this;
                }
                return $this->source;

            case 'compiled':
                $this->compiled = $this->source->getCompiled($this);
                return $this->compiled;

            case 'cached':
                if (!class_exists('Smarty_Template_Cached')) {
                    include SMARTY_SYSPLUGINS_DIR . 'smarty_cacheresource.php';
                }
                $this->cached = new Smarty_Template_Cached($this);
                return $this->cached;

            case 'compiler':
                $this->smarty->loadPlugin($this->source->compiler_class);
                $this->compiler = new $this->source->compiler_class($this->source->template_lexer_class, $this->source->template_parser_class, $this->smarty);
                return $this->compiler;

            // FIXME: routing of template -> smarty attributes
            default:
                if (property_exists($this->smarty, $property_name)) {
                    return $this->smarty->$property_name;
                }
        }

        throw new SmartyException("template property '$property_name' does not exist.");
    }

}

?>