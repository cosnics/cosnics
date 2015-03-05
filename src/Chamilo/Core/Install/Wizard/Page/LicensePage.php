<?php
namespace Chamilo\Core\Install\Wizard\Page;

use Chamilo\Core\Install\Wizard\InstallWizardPage;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: license_install_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * 
 * @package install.lib.installmanager.component.inc.wizard
 */
/**
 * Class for license page Displays the GNU GPL license that has to be accepted to install Chamilo.
 */
class LicensePage extends InstallWizardPage
{

    public function buildForm()
    {
        $this->set_lang($this->controller->exportValue('page_language', 'install_language'));
        $this->_formBuilt = true;
        $this->addElement('category', Translation :: get('License'));
        $this->addElement(
            'textarea', 
            'license', 
            null, 
            array('cols' => 80, 'rows' => 30, 'style' => 'background-color: white;'));
        $this->addElement('checkbox', 'license_accept', '', Translation :: get('IAccept'));
        $this->addRule(
            'license_accept', 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        $this->addElement('category');
        
        $buttons = array();
        $buttons[] = $this->createElement(
            'style_submit_button', 
            $this->getButtonName('back'), 
            Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'normal previous'));
        $buttons[] = $this->createElement(
            'style_submit_button', 
            $this->getButtonName('next'), 
            Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'normal next'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('next'));
        $this->set_form_defaults();
    }

    public function set_form_defaults()
    {
        $defaults = array();
        $defaults['license'] = implode("", file(realpath(__DIR__ . '/../../../../../../documentation/license.txt')));
        $this->setDefaults($defaults);
    }
}
