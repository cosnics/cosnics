<?php
namespace Chamilo\Libraries\Format\Form;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

/**
 * Specific setting / additions for the CKEditor HTML editor All CKEditor settings:
 * http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html
 * 
 * @author Scaramanga
 */
class FormValidatorCkeditorHtmlEditor extends FormValidatorHtmlEditor
{

    public function create()
    {
        $form = $this->get_form();
        
        $form->addElement('html', implode(PHP_EOL, $this->add_pre_javascript_config()));
        
        $scripts = $this->get_includes();
        
        foreach ($scripts as $script)
        {
            if (! empty($script))
            {
                $form->addElement('html', $script);
            }
        }
        
        $form->addElement('html', implode(PHP_EOL, $this->get_javascript()));
        
        return parent::create();
    }

    public function render()
    {
        $html = array();
        $html[] = parent::render();
        // $html[] = implode(PHP_EOL, $this->get_includes());
        $html[] = implode(PHP_EOL, $this->get_javascript());
        
        return implode(PHP_EOL, $html);
    }

    public function add_pre_javascript_config()
    {
        $javascript = array();
        
        $javascript[] = '<script type="text/javascript">';
        $javascript[] = 'window.CKEDITOR_BASEPATH = "' .
             Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) .
             '" + "HtmlEditor/Ckeditor/"';
        $javascript[] = '</script>';
        
        return $javascript;
    }

    public function get_includes()
    {
        $scripts = array();
        $scripts[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) .
                 'HtmlEditor/Ckeditor/ckeditor.js');
        $scripts[] = '<script type="text/javascript">';
        $scripts[] = 'CKEDITOR.timestamp = "v7"';
        $scripts[] = '</script>';
        $scripts[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) .
                 'HtmlEditor/Ckeditor/adapters/jquery.js');
        
        return $scripts;
    }

    public function get_javascript()
    {
        $javascript = array();
        $javascript[] = '<script type="text/javascript">';
        $javascript[] = 'var web_path = \'' . Path::getInstance()->getBasePath(true) . '\'';
        $javascript[] = '$(function ()';
        $javascript[] = '{';
        $javascript[] = '	$(document).ready(function ()';
        $javascript[] = '	{';
        $javascript[] = '         if(typeof $el == \'undefined\'){';
        $javascript[] = '           $el = new Array()';
        $javascript[] = '         }';
        $javascript[] = '	  $el.push($("textarea.html_editor[name=\'' . $this->get_name() . '\']").ckeditor({';
        $javascript[] = $this->get_options()->render_options();
        $javascript[] = '		}, function(){ $(document).trigger(\'ckeditor_loaded\'); }));';
        $javascript[] = '	}); ';
        $javascript[] = '});';
        $javascript[] = '</script>';
        
        return $javascript;
    }
}
