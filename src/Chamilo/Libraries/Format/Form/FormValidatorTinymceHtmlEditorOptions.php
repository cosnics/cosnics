<?php
namespace Chamilo\Libraries\Format\Form;

use Chamilo\Libraries\File\Path;

/**
 * The combination of options available for the FormValidatorCkeditorHtmlEditor
 *
 * @author Scaramanga
 */
class FormValidatorTinymceHtmlEditorOptions extends FormValidatorHtmlEditorOptions
{
    const OPTION_SCRIPT_URL = 'script_url';
    const OPTION_TOOLBAR_LOCATION = 'theme_advanced_toolbar_location';
    const OPTION_TOOLBAR_ALIGN = 'theme_advanced_toolbar_align';
    const OPTION_TOOLBAR_LINE_1 = 'theme_advanced_buttons1';
    const OPTION_TOOLBAR_LINE_2 = 'theme_advanced_buttons2';
    const OPTION_TOOLBAR_LINE_3 = 'theme_advanced_buttons3';
    const OPTION_BROWSER = 'file_browser_callback';
    const OPTION_PLUGIN = 'plugins';

    private $toolbars = array(
        'Basic' => array(
            self :: OPTION_TOOLBAR_LINE_1 => 'bold,italic,underline,separator,numlist,bullist,separator,link,unlink,separator,forecolor,backcolor,separator,hr,separator,image,template',
            self :: OPTION_TOOLBAR_LINE_2 => '',
            self :: OPTION_TOOLBAR_LINE_3 => ''));

    public function get_option_names()
    {
        $options = parent :: get_option_names();
        $options[] = self :: OPTION_SCRIPT_URL;
        $options[] = self :: OPTION_TOOLBAR_LOCATION;
        $options[] = self :: OPTION_TOOLBAR_ALIGN;
        $options[] = self :: OPTION_TOOLBAR_LINE_1;
        $options[] = self :: OPTION_TOOLBAR_LINE_2;
        $options[] = self :: OPTION_TOOLBAR_LINE_3;
        $options[] = self :: OPTION_BROWSER;
        $options[] = self :: OPTION_PLUGIN;
        return $options;
    }

    public function get_mapping()
    {
        $mapping = parent :: get_mapping();

        $mapping[self :: OPTION_THEME] = 'theme';
        // $mapping[self :: OPTION_COLLAPSE_TOOLBAR] = 'toolbarStartupExpanded';
        // $mapping[self :: OPTION_CONFIGURATION] = 'script_url';
        // $mapping[self :: OPTION_FULL_PAGE] = 'fullPage';
        $mapping[self :: OPTION_TEMPLATES] = 'template_external_list_url';

        return $mapping;
    }

    public function get_toolbar_format($toolbar_name)
    {
        $toolbars = $this->toolbars;

        if (key_exists($toolbar_name, $toolbars))
        {
            return $toolbars[$toolbar_name];
        }
        else
        {
            return $toolbars['Basic'];
        }
    }

    public function set_defaults()
    {
        parent :: set_defaults();

        $this->set_option(
            self :: OPTION_SCRIPT_URL,
            Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'HtmlEditor/Tinymce/tinymce.min.js');
        $this->set_option(self :: OPTION_THEME, 'advanced');
        $this->set_option(self :: OPTION_TOOLBAR_LOCATION, 'top');
        $this->set_option(self :: OPTION_TOOLBAR_ALIGN, 'left');
        $this->set_option(self :: OPTION_BROWSER, 'myFileBrowser');
        $this->set_option(self :: OPTION_PLUGIN, 'template,media');

        $formats = $this->get_toolbar_format($this->get_option(self :: OPTION_TOOLBAR));
        foreach ($formats as $key => $format)
        {
            $this->set_option($key, $format);
        }

        $this->set_option(self :: OPTION_COLLAPSE_TOOLBAR, null);
        $this->set_option(self :: OPTION_TOOLBAR, null);
        $this->set_option(self :: OPTION_FULL_PAGE, null);
    }
}
