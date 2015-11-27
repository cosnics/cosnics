<?php
namespace Chamilo\Core\Install\Wizard\Page;

use Chamilo\Core\Install\Wizard\InstallWizardPage;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: settings_install_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 *
 * @package install.lib.installmanager.component.inc.wizard
 */
/**
 * Page in the install wizard in which some config settings are asked to the user.
 */
class OverviewPage extends InstallWizardPage
{

    public function buildForm()
    {
        $this->set_lang($this->controller->exportValue('page_language', 'install_language'));
        $this->_formBuilt = true;

        $this->addElement('category', Translation :: get('Database'));
        $this->addElement('static', 'database_driver', Translation :: get('DatabaseDriver'));
        $this->addElement('static', 'database_host', Translation :: get('DatabaseHost'));
        $this->addElement('static', 'database_name', Translation :: get('DatabaseName'));
        $this->addElement('static', 'database_username', Translation :: get('DatabaseLogin'));
        $this->addElement('static', 'database_password', Translation :: get('DatabasePassword'));
        $this->addElement('Static', 'database_exists', Translation :: get('DatabaseExists'));
        $this->addElement('category');

        $this->addElement('category', Translation :: get('Packages'));
        $this->addElement('static', 'selected_packages', Translation :: get('SelectedPackages'));
        $this->addElement('category');

        $this->addElement('category', Translation :: get('GeneralProperties'));
        $this->addElement('static', 'platform_language', Translation :: get('MainLang'));
        $this->addElement('static', 'platform_url', Translation :: get('ChamiloURL'));
        $this->addElement('static', 'server_type', Translation :: get('ServerType'));
        $this->addElement('category');

        $this->addElement('category', Translation :: get('Administrator'));
        $this->addElement('static', 'admin_email', Translation :: get('AdminEmail'));
        $this->addElement('static', 'admin_surname', Translation :: get('AdminLastName'));
        $this->addElement('static', 'admin_firstname', Translation :: get('AdminFirstName'));
        $this->addElement('static', 'admin_phone', Translation :: get('AdminPhone'));
        $this->addElement('static', 'admin_username', Translation :: get('AdminLogin'));
        $this->addElement('static', 'admin_password', Translation :: get('AdminPass'));
        $this->addElement('category');

        $this->addElement('category', Translation :: get('Platform'));
        $this->addElement('static', 'platform_name', Translation :: get('CampusName'));
        $this->addElement('static', 'organization_name', Translation :: get('InstituteShortName'));
        $this->addElement('static', 'organization_url', Translation :: get('InstituteURL'));
        $this->addElement('category');

        $this->addElement('category', Translation :: get('Security'));
        $this->addElement('static', 'self_reg', Translation :: get('AllowSelfReg'));
        $this->addElement('static', 'hashing_algorithm', Translation :: get('HashingAlgorithm'));
        $this->addElement('category');

        $this->addElement('category', Translation :: get('Storage'));
        $this->addElement('static', 'archive_path', Translation :: get('ArchivePath'));
        $this->addElement('static', 'cache_path', Translation :: get('CachePath'));
        $this->addElement('static', 'garbage_path', Translation :: get('GarbagePath'));
        $this->addElement('static', 'hotpotatoes_path', Translation :: get('HotpotatoesPath'));
        $this->addElement('static', 'logs_path', Translation :: get('LogsPath'));
        $this->addElement('static', 'repository_path', Translation :: get('RepositoryPath'));
        $this->addElement('static', 'scorm_path', Translation :: get('ScormPath'));
        $this->addElement('static', 'temp_path', Translation :: get('TempPath'));
        $this->addElement('static', 'userpictures_path', Translation :: get('UserpicturesPath'));
        $this->addElement('category');

        $buttons = array();
        $buttons[] = $this->createElement(
            'style_submit_button',
            $this->getButtonName('back'),
            Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal previous'));
        $buttons[] = $this->createElement(
            'style_submit_button',
            $this->getButtonName('submit'),
            Translation :: get('Finish'),
            array('class' => 'positive'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('submit'));
        $this->set_form_defaults();
    }

    public function set_form_defaults()
    {
        $defaults = array();

        // Database settings
        $defaults['database_driver'] = $this->controller->exportValue('page_database', 'database_driver');
        $defaults['database_host'] = $this->controller->exportValue('page_database', 'database_host');
        $defaults['database_name'] = $this->controller->exportValue('page_database', 'database_name');
        $defaults['database_username'] = $this->controller->exportValue('page_database', 'database_username');
        $defaults['database_password'] = $this->controller->exportValue('page_database', 'database_password');
        $defaults['database_exists'] = $this->controller->exportValue('page_database', 'database_exists') ? Translation :: get(
            'ConfirmYes',
            null,
            Utilities :: COMMON_LIBRARIES) : Translation :: get('ConfirmNo', null, Utilities :: COMMON_LIBRARIES);

        // Application selections
        $selected_applications = array();
        $wizard_packages = (array) $this->controller->exportValue('page_package', 'install');

        foreach ($wizard_packages as $context => $value)
        {
            if (isset($value) && $value == '1')
            {
                $selected_packages[] = Translation :: get('TypeName', null, $context);
            }
        }

        $defaults['selected_packages'] = implode(', ', $selected_packages);

        // Storage settings
        $defaults['archive_path'] = $this->controller->exportValue('page_settings', 'archive_path');
        $defaults['cache_path'] = $this->controller->exportValue('page_settings', 'cache_path');
        $defaults['garbage_path'] = $this->controller->exportValue('page_settings', 'garbage_path');
        $defaults['hotpotatoes_path'] = $this->controller->exportValue('page_settings', 'hotpotatoes_path');
        $defaults['logs_path'] = $this->controller->exportValue('page_settings', 'logs_path');
        $defaults['repository_path'] = $this->controller->exportValue('page_settings', 'repository_path');
        $defaults['scorm_path'] = $this->controller->exportValue('page_settings', 'scorm_path');
        $defaults['temp_path'] = $this->controller->exportValue('page_settings', 'temp_path');
        $defaults['userpictures_path'] = $this->controller->exportValue('page_settings', 'userpictures_path');

        // Platform settings
        $defaults['platform_language'] = $this->controller->exportValue('page_language', 'install_language');
        $defaults['platform_url'] = $this->controller->exportValue('page_settings', 'platform_url');
        $defaults['admin_email'] = $this->controller->exportValue('page_settings', 'admin_email');
        $defaults['admin_surname'] = $this->controller->exportValue('page_settings', 'admin_surname');
        $defaults['admin_firstname'] = $this->controller->exportValue('page_settings', 'admin_firstname');
        $defaults['admin_username'] = $this->controller->exportValue('page_settings', 'admin_username');
        $defaults['admin_password'] = $this->controller->exportValue('page_settings', 'admin_password');
        $defaults['platform_name'] = $this->controller->exportValue('page_settings', 'platform_name');
        $defaults['organization_name'] = $this->controller->exportValue('page_settings', 'organization_name');
        $defaults['organization_url'] = $this->controller->exportValue('page_settings', 'organization_url');
        $defaults['server_type'] = $this->controller->exportValue('page_settings', 'server_type');
        $defaults['self_reg'] = Translation :: get(
            ($this->controller->exportValue('page_settings', 'self_reg') == 1 ? 'Yes' : 'No'));
        $defaults['hashing_algorithm'] = $this->controller->exportValue('page_settings', 'hashing_algorithm');
        $this->setDefaults($defaults);
    }
}
