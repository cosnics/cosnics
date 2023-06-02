<?php
namespace Chamilo\Core\Install\Form;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Core\Install\Manager;
use Chamilo\Core\Install\ValidateDatabaseConnection;
use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Tabs\Form\FormTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Install\Form
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class SettingsForm extends FormValidator
{

    private Application $application;

    /**
     * @param Application $application
     * @param string $action
     */
    public function __construct(Application $application, $action)
    {
        parent::__construct('install_settings', self::FORM_METHOD_POST, $action);
        $this->application = $application;

        $this->buildForm();
        $this->setDefaults();
    }

    public function addDatabaseSettings()
    {
        $this->addElement(
            'select', 'database_driver', Translation::get('DatabaseDriver'), $this->get_database_drivers()
        );
        $this->addElement('text', 'database_host', Translation::get('DatabaseHost'), ['size' => '40']);
        $this->addElement('text', 'database_name', Translation::get('DatabaseName'), ['size' => '40']);

        $this->addElement('text', 'database_username', Translation::get('DatabaseLogin'), ['size' => '40']);
        $this->addElement('password', 'database_password', Translation::get('DatabasePassword'), ['size' => '40']);

        $this->addElement('checkbox', 'database_overwrite', Translation::get('DatabaseOverwrite'));

        $this->addRule('database_host', 'ThisFieldIsRequired', 'required');
        $this->addRule('database_driver', 'ThisFieldIsRequired', 'required');
        $this->addRule('database_name', 'ThisFieldIsRequired', 'required');

        $pattern = '/(^[a-zA-Z$_][0-9a-zA-Z$_]*$)|(^[0-9][0-9a-zA-Z$_]*[a-zA-Z$_][0-9a-zA-Z$_]*$)/';
        $this->addRule('database_name', 'OnlyCharactersNumbersUnderscoresAndDollarSigns', 'regex', $pattern);
        $this->addRule(
            ['database_driver', 'database_host', 'database_username', 'database_password', 'database_name'],
            Translation::get('CouldNotConnectToDatabase'), new ValidateDatabaseConnection()
        );
    }

    public function addGeneralSettings()
    {
        $this->addElement('category', Translation::get('GeneralProperties'));
        $this->addElement(
            'select', 'platform_language', Translation::get('MainLang'), $this->getApplication()->getLanguages()
        );

        $this->addElement('category', Translation::get('Administrator'));
        $this->addElement('text', 'admin_email', Translation::get('AdminEmail'), ['size' => '40']);
        $this->addRule(
            'admin_email', Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required'
        );
        $this->addRule('admin_email', Translation::get('WrongEmail'), 'email');
        $this->addElement('text', 'admin_surname', Translation::get('AdminLastName'), ['size' => '40']);
        $this->addRule(
            'admin_surname', Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required'
        );
        $this->addElement('text', 'admin_firstname', Translation::get('AdminFirstName'), ['size' => '40']);
        $this->addRule(
            'admin_firstname', Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required'
        );
        $this->addElement('text', 'admin_phone', Translation::get('AdminPhone'), ['size' => '40']);
        $this->addElement('text', 'admin_username', Translation::get('AdminLogin'), ['size' => '40']);
        $this->addRule(
            'admin_username', Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required'
        );
        $this->addElement('text', 'admin_password', Translation::get('AdminPass'), ['size' => '40']);
        $this->addRule(
            'admin_password', Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required'
        );

        $this->addElement('category', Translation::get('Platform'));
        $this->addElement('text', 'site_name', Translation::get('CampusName'), ['size' => '40']);
        $this->addRule(
            'site_name', Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required'
        );
        $this->addElement('text', 'organization_name', Translation::get('InstituteShortName'), ['size' => '40']);
        $this->addRule(
            'organization_name', Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required'
        );
        $this->addElement('text', 'organization_url', Translation::get('InstituteURL'), ['size' => '40']);
        $this->addRule(
            'organization_url', Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required'
        );

        $this->addElement('category', Translation::get('Security'));

        $selfRegistration = [];
        $selfRegistration[] = $this->createElement(
            'radio', 'self_reg', null, Translation::get('ConfirmYes', null, StringUtilities::LIBRARIES), 1
        );
        $selfRegistration[] = $this->createElement('radio', 'self_reg', null, Translation::get('AfterApproval'), 2);
        $selfRegistration[] = $this->createElement(
            'radio', 'self_reg', null, Translation::get('ConfirmNo', null, StringUtilities::LIBRARIES), 0
        );
        $this->addGroup($selfRegistration, 'self_reg', Translation::get('AllowSelfReg'), '&nbsp;', false);

        $this->addElement(
            'select', 'hashing_algorithm', Translation::get('HashingAlgorithm'), HashingUtilities::getAvailableTypes()
        );

        $this->addElement('category', Translation::get('Storage'));
        $this->addElement('text', 'archive_path', Translation::get('ArchivePath'), ['size' => '40']);
        $this->addElement('text', 'cache_path', Translation::get('CachePath'), ['size' => '40']);
        $this->addElement('text', 'garbage_path', Translation::get('GarbagePath'), ['size' => '40']);
        $this->addElement('text', 'hotpotatoes_path', Translation::get('HotpotatoesPath'), ['size' => '40']);
        $this->addElement('text', 'logs_path', Translation::get('LogsPath'), ['size' => '40']);
        $this->addElement('text', 'repository_path', Translation::get('RepositoryPath'), ['size' => '40']);
        $this->addElement('text', 'scorm_path', Translation::get('ScormPath'), ['size' => '40']);
        $this->addElement('text', 'temp_path', Translation::get('TempPath'), ['size' => '40']);
        $this->addElement('text', 'userpictures_path', Translation::get('UserPicturesPath'), ['size' => '40']);
    }

    protected function addPackageSelectionToggle()
    {
        $html = [];

        $html[] = '&nbsp;';
        $html[] = '<small>';
        $html[] = '<a class="label label-success package-list-select-all">';
        $html[] = Translation::get('SelectAll', null, StringUtilities::LIBRARIES);
        $html[] = '</a>';
        $html[] = '&nbsp;';
        $html[] = '<a class="label label-default package-list-select-none">';
        $html[] = Translation::get('UnselectAll', null, StringUtilities::LIBRARIES);
        $html[] = '</a>';
        $html[] = '</small>';

        return implode(PHP_EOL, $html);
    }

    public function addPackageSettings()
    {
        $html = [];

        $html[] = '<div class="package-selection">';
        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';
        $html[] = '<h4>';
        $html[] = Translation::get('AllPackages');
        $html[] = $this->addPackageSelectionToggle();
        $html[] = '</h4>';
        $html[] = '</div>';
        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->renderPackages(PlatformPackageBundles::getInstance()->get_package_list());

        $html = [];

        $html[] = '<script src="' . $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Core\Install') .
            'Install.js"></script>';
        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));
    }

    protected function buildForm()
    {
        $tabsCollection = new TabsCollection();

        $tabsCollection->add(
            new FormTab('database', Translation::get('DatabaseComponentTitle'), null, 'addDatabaseSettings')
        );
        $tabsCollection->add(
            new FormTab('general', Translation::get('SettingsComponentTitle'), null, 'addGeneralSettings')
        );
        $tabsCollection->add(
            new FormTab('package', Translation::get('PackageComponentTitle'), null, 'addPackageSettings')
        );

        $this->getFormTabsGenerator()->generate('settings', $this, $tabsCollection);

        $buttons = [];

        $glyph = new FontAwesomeGlyph('chevron-left', [], null, 'fas');

        $buttons[] = $this->createElement(
            'static', null, null,
            '<a href="' . $this->getApplication()->get_url([Manager::PARAM_ACTION => Manager::ACTION_LICENSE]) .
            '" class="btn btn-default">' . $glyph->render() .
            Translation::get('Previous', null, StringUtilities::LIBRARIES) . '</a>'
        );

        $buttons[] = $this->createElement(
            'style_button', 'next', Translation::get('Next', null, StringUtilities::LIBRARIES),
            ['class' => 'btn-primary'], null, new FontAwesomeGlyph('chevron-right')
        );
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * @param \Chamilo\Configuration\Package\PackageList $packageList
     *
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package[]
     */
    public function determinePackages(PackageList $packageList)
    {
        $packages = [];

        foreach ($packageList->get_packages() as $namespace => $package)
        {
            if (strpos($namespace, '\Integration\Chamilo\\') === false)
            {
                $packages[] = $package;
            }
        }

        usort($packages, [$this, 'orderPackages']);

        return $packages;
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param \Chamilo\Configuration\Package\Storage\DataClass\Package $package
     *
     * @return string
     */
    private function getPackageClasses(Package $package)
    {
        $classes = ['btn'];

        if ($package->getCoreInstall())
        {
            $classes[] = 'btn-default';
        }
        else
        {
            if ($package->getDefaultInstall())
            {
                $sessionSettings = $this->getSessionSettings();

                if (!empty($sessionSettings))
                {
                    if ($sessionSettings['install'][$package->get_context()])
                    {
                        $classes[] = 'btn-success';
                    }
                    else
                    {
                        $classes[] = 'btn-default';
                    }
                }
                else
                {
                    $classes[] = 'btn-success';
                }
            }
            else
            {
                $classes[] = 'btn-default';
            }
        }

        return implode(' ', $classes);
    }

    /**
     * @return string[]
     */
    protected function getSessionSettings()
    {
        if (!isset($this->sessionSettings))
        {
            $sessionSettings = $this->getSession()->get(Manager::PARAM_SETTINGS);

            if (is_null($sessionSettings))
            {
                $sessionSettings = [];
            }
            else
            {
                $sessionSettings = unserialize($sessionSettings);
            }

            $this->sessionSettings = $sessionSettings;
        }

        return $this->sessionSettings;
    }

    protected function get_database_drivers()
    {
        $drivers = [];
        $drivers['mysqli'] = 'MySQL >= 4.1.3';

        return $drivers;
    }

    protected function hasSelectablePackages($packages)
    {
        if (count($packages) <= 1)
        {
            return false;
        }
        else
        {
            $numberOfCorePackages = 0;

            foreach ($packages as $package)
            {
                if ($package->getCoreInstall())
                {
                    $numberOfCorePackages ++;
                }
            }

            if ($numberOfCorePackages == count($packages))
            {
                return false;
            }
        }

        return true;
    }

    public function orderPackages($packageLeft, $packageRight)
    {
        $packageNameLeft = Translation::get('TypeName', null, $packageLeft->get_context());
        $packageNameRight = Translation::get('TypeName', null, $packageRight->get_context());

        return strcmp($packageNameLeft, $packageNameRight);
    }

    /**
     * @param \Chamilo\Configuration\Package\PackageList $packageList
     */
    public function renderPackages(PackageList $packageList)
    {
        $html = [];

        $renderer = $this->defaultRenderer();
        $packages = $this->determinePackages($packageList);

        if (count($packages) > 0)
        {
            $firstPackage = current($packages);
            $packageType = Translation::get('TypeCategory', null, $firstPackage->get_context());

            $html = [];

            $html[] = '<div class="package-list">';

            $html[] = '<div class="row package-list-header">';
            $html[] = '<div class="col-xs-12">';
            $html[] = '<h4>';
            $html[] = $packageType;

            if ($this->hasSelectablePackages($packages))
            {
                $html[] = $this->addPackageSelectionToggle();
            }

            $html[] = '</h4>';
            $html[] = '</div>';
            $html[] = '</div>';

            $html[] = '<div class="package-list-items row">';

            $this->addElement('html', implode(PHP_EOL, $html));

            foreach ($packages as $package)
            {
                $title = Translation::get('TypeName', null, $package->get_context());
                $packageClasses = $this->getPackageClasses($package);

                if ($package->getCoreInstall())
                {
                    $glyph = new NamespaceIdentGlyph($package->get_context(), true, false, true);
                    $disabled = ' disabled="disabled"';
                }
                else
                {
                    $glyph = new NamespaceIdentGlyph($package->get_context(), true);
                    $disabled = '';
                }

                $html = [];
                $html[] = '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">';
                $html[] = '<a class="' . $packageClasses . '"' . $disabled . '>' . $glyph->render() . ' ';
                $this->addElement('html', implode(PHP_EOL, $html));

                $checkbox_name =
                    'install_' . ClassnameUtilities::getInstance()->getNamespaceId($package->get_context());
                $this->addElement('checkbox', 'install[' . $package->get_context() . ']');
                $renderer->setElementTemplate('{element}', 'install[' . $package->get_context() . ']');

                $html = [];
                $html[] = $title;
                $html[] = '</a>';
                $html[] = '</div>';
                $this->addElement('html', implode(PHP_EOL, $html));

                $extra = $package->get_extra();

                if ($package->getCoreInstall() || $package->getDefaultInstall())
                {
                    $defaults['install'][$package->get_context()] = 1;
                }
            }

            $this->setDefaults($defaults);

            $html = [];
            $html[] = '<div class="clearfix"></div>';
            $html[] = '</div>';
            $html[] = '</div>';
            $this->addElement('html', implode(PHP_EOL, $html));
        }

        foreach ($packageList->get_children() as $child)
        {
            $this->renderPackages($child);
        }
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        $sessionSettings = $this->getSessionSettings();

        if (!empty($sessionSettings))
        {
            $defaults = $sessionSettings;
        }
        else
        {
            // Database
            $defaults['database_driver'] = 'mysqli';
            $defaults['database_host'] = 'localhost';
            $defaults['database_name'] = 'cosnics';

            // General settings

            $defaults['platform_language'] = Translation::getInstance()->getLanguageIsocode();
            $defaults['admin_email'] = $_SERVER['SERVER_ADMIN'];
            $email_parts = explode('@', $defaults['admin_email']);
            if ($email_parts[1] == 'localhost')
            {
                $defaults['admin_email'] .= '.localdomain';
            }
            $defaults['admin_surname'] = 'Doe';
            $defaults['admin_firstname'] = mt_rand(0, 1) ? 'John' : 'Jane';
            $defaults['admin_username'] = 'admin';
            $defaults['site_name'] = Translation::get('MyChamilo');
            $defaults['organization_name'] = Translation::get('Chamilo');
            $defaults['organization_url'] = 'http://www.cosnics.org';
            $defaults['self_reg'] = 0;
            $defaults['encrypt_password'] = 1;
            $defaults['hashing_algorithm'] = 'Sha1';

            // Storage paths
            $defaults['archive_path'] = $this->getConfigurablePathBuilder()->getArchivePath();
            $defaults['cache_path'] = $this->getConfigurablePathBuilder()->getCachePath();
            $defaults['garbage_path'] = $this->getConfigurablePathBuilder()->getGarbagePath();
            $defaults['hotpotatoes_path'] = $this->getSystemPathBuilder()->getPublicStoragePath(Hotpotatoes::CONTEXT);
            $defaults['logs_path'] = $this->getConfigurablePathBuilder()->getLogPath();
            $defaults['repository_path'] = $this->getConfigurablePathBuilder()->getRepositoryPath();
            $defaults['scorm_path'] = $this->getConfigurablePathBuilder()->getScormPath();
            $defaults['temp_path'] = $this->getConfigurablePathBuilder()->getTemporaryPath();
            $defaults['userpictures_path'] = $this->getConfigurablePathBuilder()->getUserPicturesPath();
        }

        parent::setDefaults($defaults);
    }
}