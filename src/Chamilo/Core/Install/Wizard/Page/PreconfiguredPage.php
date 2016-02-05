<?php
namespace Chamilo\Core\Install\Wizard\Page;

use Chamilo\Core\Install\Wizard\InstallWizardPage;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: language_install_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 *
 * @package install.lib.installmanager.component.inc.wizard
 */
/**
 * This form can be used to let the user select the action.
 */
class PreconfiguredPage extends InstallWizardPage
{

    public function get_breadcrumb()
    {
        return Translation :: get('SelectConfigurationFile');
    }

    public function get_title()
    {
        return Translation :: get('SelectConfigurationFile');
    }

    public function get_info()
    {
        return Translation :: get('SelectConfigurationFileDescription');
    }

    public function buildForm()
    {
        $this->_formBuilt = true;

        $this->addElement('category', Translation :: get('ConfigurationFile'));
        $this->addElement('file', 'config_file', Translation :: get('ConfigurationFile'));
        $this->addElement('category');

        $buttons = array();
        $buttons[] = $this->createElement(
            'style_button',
            $this->getButtonName('back'),
            Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES),
            null,
            null,
            'chevron-left');
        $buttons[] = $this->createElement(
            'style_button',
            $this->getButtonName('next'),
            Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES),
            null,
            null,
            'chevron-right');
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('next'));
    }
}
