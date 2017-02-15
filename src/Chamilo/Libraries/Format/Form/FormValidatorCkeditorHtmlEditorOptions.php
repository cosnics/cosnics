<?php
namespace Chamilo\Libraries\Format\Form;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * The combination of options available for the FormValidatorCkeditorHtmlEditor
 * 
 * @author Scaramanga
 */
class FormValidatorCkeditorHtmlEditorOptions extends FormValidatorHtmlEditorOptions
{

    public function get_mapping()
    {
        $mapping = parent::get_mapping();
        
        $mapping[self::OPTION_COLLAPSE_TOOLBAR] = 'toolbarStartupExpanded';
        $mapping[self::OPTION_CONFIGURATION] = 'customConfig';
        $mapping[self::OPTION_FULL_PAGE] = 'fullPage';
        $mapping[self::OPTION_TEMPLATES] = 'templates_files';
        
        return $mapping;
    }

    public function process_collapse_toolbar($value)
    {
        if ($value === true)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function set_defaults()
    {
        parent::set_defaults();
        
        $application = Request::get('application');
        $app_sys_path = Path::getInstance()->getJavascriptPath($application) . 'HtmlEditor/Ckeditor.js';
        if (file_exists($app_sys_path))
        {
            $path = Path::getInstance()->getJavascriptPath($application, true) . 'HtmlEditor/Ckeditor.js';
        }
        else
        {
            $path = Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'HtmlEditor/Ckeditor.js';
        }
        
        $this->set_option(self::OPTION_CONFIGURATION, $path);
        $this->set_option(self::OPTION_SKIN, 'bootstrapck');
    }
}
