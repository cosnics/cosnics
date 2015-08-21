<?php
namespace Chamilo\Core\Install\Wizard\Page;

use Chamilo\Core\Install\ValidateDatabaseConnection;
use Chamilo\Core\Install\Wizard\InstallWizardPage;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: database_install_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * 
 * @package install.lib.installmanager.component.inc.wizard
 */
/**
 * Class for database settings page Displays a form where the user can enter the installation settings regarding the
 * databases - login and password, names, prefixes, single or multiple databases, tracking or not...
 */
class DatabasePage extends InstallWizardPage
{

    public function buildForm()
    {
        $this->set_lang($this->controller->exportValue('page_language', 'install_language'));
        $this->_formBuilt = true;
        
        $this->get_database_drivers();
        
        $this->addElement('category', Translation :: get('Database'));
        $this->addElement(
            'select', 
            'database_driver', 
            Translation :: get('DatabaseDriver'), 
            $this->get_database_drivers());
        $this->addElement('text', 'database_host', Translation :: get('DatabaseHost'), array('size' => '40'));
        $this->addElement('text', 'database_name', Translation :: get('DatabaseName'), array('size' => '40'));
        $this->addElement('checkbox', 'database_overwrite', Translation :: get('DatabaseOverwrite'));
        $this->addElement('category');
        
        $this->addElement('category', Translation :: get('Credentials'));
        $this->addElement('text', 'database_username', Translation :: get('DatabaseLogin'), array('size' => '40'));
        $this->addElement(
            'password', 
            'database_password', 
            Translation :: get('DatabasePassword'), 
            array('size' => '40'));
        $this->addElement('category');
        
        $this->addRule('database_host', 'ThisFieldIsRequired', 'required');
        $this->addRule('database_driver', 'ThisFieldIsRequired', 'required');
        $this->addRule('database_name', 'ThisFieldIsRequired', 'required');
        
        $pattern = '/(^[a-zA-Z$_][0-9a-zA-Z$_]*$)|(^[0-9][0-9a-zA-Z$_]*[a-zA-Z$_][0-9a-zA-Z$_]*$)/';
        $this->addRule('database_name', 'OnlyCharactersNumbersUnderscoresAndDollarSigns', 'regex', $pattern);
        $this->addRule(
            array('database_driver', 'database_host', 'database_username', 'database_password', 'database_name'), 
            Translation :: get('CouldNotConnectToDatabase'), 
            new ValidateDatabaseConnection());
        
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
        $defaults['database_driver'] = 'mysqli';
        $defaults['database_host'] = 'localhost';
        $defaults['database_name'] = 'chamilo';
        $this->setDefaults($defaults);
    }

    public function get_database_drivers()
    {
        $drivers = array();
        $drivers['mysqli'] = 'MySQL >= 4.1.3';
        return $drivers;
    }
}
