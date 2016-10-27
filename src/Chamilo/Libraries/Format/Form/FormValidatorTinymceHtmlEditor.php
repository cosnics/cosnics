<?php
namespace Chamilo\Libraries\Format\Form;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

class FormValidatorTinymceHtmlEditor extends FormValidatorHtmlEditor
{

    public function create()
    {
        $form = $this->get_form();

        $scripts = $this->get_includes();

        foreach ($scripts as $script)
        {
            if (! empty($script))
            {
                $form->addElement('html', $script);
            }
        }

        $form->addElement('html', implode(PHP_EOL, $this->get_javascript()));

        return parent :: create();
    }

    public function render()
    {
        $html = array();
        $html[] = parent :: render();
        $html[] = implode(PHP_EOL, $this->get_javascript());

        return implode(PHP_EOL, $html);
    }

    public function get_includes()
    {
        $scripts = array();
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'HtmlEditor/Tinymce/tiny_mce.js');
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'HtmlEditor/Tinymce/jquery.tinymce.js');
        $scripts[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'HtmlEditor/Tinymce.js');

        return $scripts;
    }

    public function get_javascript()
    {
        $javascript = array();
        $javascript[] = '<script type="text/javascript">';
        $javascript[] = '$(function ()';
        $javascript[] = '{';
        $javascript[] = '	$(document).ready(function ()';
        $javascript[] = '	{';
        $javascript[] = '		$("textarea.html_editor[name=\'' . $this->get_name() . '\']").tinymce({';
        $javascript[] = $this->get_options()->render_options();
        $javascript[] = '		});';
        $javascript[] = '	});';
        $javascript[] = '});';
        $javascript[] = '</script>';

        return $javascript;
    }
}
