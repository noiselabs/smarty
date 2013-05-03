<?php

/**
 * Smarty Internal Plugin Resource File
 *
 *
 * @package TemplateResources
 * @author Uwe Tews
 * @author Rodney Rehm
 */

/**
 * Smarty Internal Plugin Resource File
 *
 * Implements the file system as resource for Smarty templates
 *
 *
 * @package TemplateResources
 */
class Smarty_Internal_Resource_File extends Smarty_Resource
{

    /**
     * populate Source Object with meta data from Resource
     *
     * @param Smarty_Template_Source $source    source object
     * @param Smarty $_template template object
     */
    public function populate(Smarty_Template_Source $source, Smarty $_template = null)
    {
        $source->filepath = $this->buildFilepath($source, $_template);

        if ($source->filepath !== false) {
            if (is_object($_template->security_policy)) {
                $_template->security_policy->isTrustedResourceDir($source->filepath);
            }

            $source->uid = sha1($source->filepath);
            if ($_template->compile_check && !isset($source->timestamp)) {
                $source->timestamp = @filemtime($source->filepath);
                $source->exists = !!$source->timestamp;
            }
        }
    }

    /**
     * populate Source Object with timestamp and exists from Resource
     *
     * @param Smarty_Template_Source $source source object
     */
    public function populateTimestamp(Smarty_Template_Source $source)
    {
        $source->timestamp = @filemtime($source->filepath);
        $source->exists = !!$source->timestamp;
    }

    /**
     * Load template's source from file into current template object
     *
     * @param Smarty_Template_Source $source source object
     * @return string template source
     * @throws SmartyException if source cannot be loaded
     */
    public function getContent(Smarty_Template_Source $source)
    {
        if ($source->timestamp) {
            return file_get_contents($source->filepath);
        }
        throw new SmartyException("Unable to read source file {$source->type} '{$source->name}'");
    }

    /**
     * Determine basename for compiled filename
     *
     * @param Smarty_Template_Source $source source object
     * @return string resource's basename
     */
    public function getBasename(Smarty_Template_Source $source)
    {
        $_file = $source->name;
        if (($_pos = strpos($_file, ']')) !== false) {
            $_file = substr($_file, $_pos + 1);
        }
        return basename($_file);
    }

}
