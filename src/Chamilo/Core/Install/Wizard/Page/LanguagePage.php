<?php
namespace Chamilo\Core\Install\Wizard\Page;

use Chamilo\Core\Install\Wizard\InstallWizardPage;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: language_install_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 *
 * @package install.lib.installmanager.component.inc.wizard
 */
/**
 * This form can be used to let the user select the action.
 */
class LanguagePage extends InstallWizardPage
{

    public function buildForm()
    {
        $this->_formBuilt = true;

        $this->addElement('category', Translation :: get('Language'));
        $this->addElement(
            'select',
            'install_language',
            Translation :: get('InstallationLanguage'),
            $this->get_language_folder_list());
        $this->addElement('category');

        $buttons = array();
        $buttons[] = $this->createElement(
            'style_button',
            $this->getButtonName('next'),
            Translation :: get('Start'),
            null,
            null,
            'chevron-right');

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('next'));
        $this->set_form_defaults();
    }

    public function get_language_folder_list()
    {
        $language_path = Path :: getInstance()->namespaceToFullPath('Chamilo\Configuration') . 'Resources/I18n/';
        $language_files = Filesystem :: get_directory_content($language_path, Filesystem :: LIST_FILES, false);

        $language_list = array();
        foreach ($language_files as $language_file)
        {
            $file_info = pathinfo($language_file);
            $language_info_file = $language_path . $file_info['filename'] . '.info';

            if (file_exists($language_info_file))
            {
                $dom_document = new \DOMDocument('1.0', 'UTF-8');
                $dom_document->load($language_info_file);
                $dom_xpath = new \DOMXPath($dom_document);

                $language_node = $dom_xpath->query('/packages/package')->item(0);

                $language_list[$dom_xpath->query('extra/isocode', $language_node)->item(0)->nodeValue] = $dom_xpath->query(
                    'name',
                    $language_node)->item(0)->nodeValue;
            }
        }

        return $language_list;
    }

    public function set_form_defaults()
    {
        $defaults = array();
        $defaults['install_language'] = 'en';
        $defaults['platform_language'] = 'en';
        $this->setDefaults($defaults);
    }
}
