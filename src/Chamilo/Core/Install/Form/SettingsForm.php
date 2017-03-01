<?php
namespace Chamilo\Core\Install\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Core\Install\ValidateDatabaseConnection;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Hashing\Hashing;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Tabs\DynamicFormTabsRenderer;
use Chamilo\Core\Install\Manager;
use Chamilo\Libraries\Platform\Session\Session;

/**
 *
 * @package Chamilo\Core\Install\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SettingsForm extends FormValidator
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var DynamicFormTabsRenderer
     */
    private $tabsGenerator;

    /**
     *
     * @param Application $application
     * @param string $action
     */
    public function __construct(Application $application, $action)
    {
        parent::__construct('install_settings', $method = 'post', $action);
        $this->application = $application;

        $this->buildForm();
        $this->setDefaults();
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    protected function buildForm()
    {
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab('database', Translation::get('DatabaseComponentTitle'), null, 'addDatabaseSettings'));
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab('general', Translation::get('SettingsComponentTitle'), null, 'addGeneralSettings'));
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab('package', Translation::get('PackageComponentTitle'), null, 'addPackageSettings'));

        $this->getTabsGenerator()->render();

        $buttons = array();

        $buttons[] = $this->createElement(
            'static',
            null,
            null,
            '<a href="' . $this->getApplication()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_LICENSE)) .
                 '" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span>' .
                 Translation::get('Previous', null, Utilities::COMMON_LIBRARIES) . '</a>');

        $buttons[] = $this->createElement(
            'style_button',
            'next',
            Translation::get('Next', null, Utilities::COMMON_LIBRARIES),
            array('class' => 'btn-primary'),
            null,
            'chevron-right');
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Tabs\DynamicFormTabsRenderer
     */
    public function getTabsGenerator()
    {
        if (! isset($this->tabsGenerator))
        {
            $this->tabsGenerator = new DynamicFormTabsRenderer('settings', $this);
        }
        return $this->tabsGenerator;
    }

    public function addDatabaseSettings()
    {
        $this->addElement(
            'select',
            'database_driver',
            Translation::get('DatabaseDriver'),
            $this->get_database_drivers());
        $this->addElement('text', 'database_host', Translation::get('DatabaseHost'), array('size' => '40'));
        $this->addElement('text', 'database_name', Translation::get('DatabaseName'), array('size' => '40'));

        $this->addElement('text', 'database_username', Translation::get('DatabaseLogin'), array('size' => '40'));
        $this->addElement('password', 'database_password', Translation::get('DatabasePassword'), array('size' => '40'));

        $this->addElement('checkbox', 'database_overwrite', Translation::get('DatabaseOverwrite'));

        $this->addRule('database_host', 'ThisFieldIsRequired', 'required');
        $this->addRule('database_driver', 'ThisFieldIsRequired', 'required');
        $this->addRule('database_name', 'ThisFieldIsRequired', 'required');

        $pattern = '/(^[a-zA-Z$_][0-9a-zA-Z$_]*$)|(^[0-9][0-9a-zA-Z$_]*[a-zA-Z$_][0-9a-zA-Z$_]*$)/';
        $this->addRule('database_name', 'OnlyCharactersNumbersUnderscoresAndDollarSigns', 'regex', $pattern);
        $this->addRule(
            array('database_driver', 'database_host', 'database_username', 'database_password', 'database_name'),
            Translation::get('CouldNotConnectToDatabase'),
            new ValidateDatabaseConnection());
    }

    public function addGeneralSettings()
    {
        $this->addElement('category', Translation::get('GeneralProperties'));
        $this->addElement(
            'select',
            'platform_language',
            Translation::get("MainLang"),
            $this->getApplication()->getLanguages());
        $this->addElement('category');

        $this->addElement('category', Translation::get('Administrator'));
        $this->addElement('text', 'admin_email', Translation::get("AdminEmail"), array('size' => '40'));
        $this->addRule(
            'admin_email',
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required');
        $this->addRule('admin_email', Translation::get('WrongEmail'), 'email');
        $this->addElement('text', 'admin_surname', Translation::get("AdminLastName"), array('size' => '40'));
        $this->addRule(
            'admin_surname',
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required');
        $this->addElement('text', 'admin_firstname', Translation::get("AdminFirstName"), array('size' => '40'));
        $this->addRule(
            'admin_firstname',
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required');
        $this->addElement('text', 'admin_phone', Translation::get("AdminPhone"), array('size' => '40'));
        $this->addElement('text', 'admin_username', Translation::get("AdminLogin"), array('size' => '40'));
        $this->addRule(
            'admin_username',
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required');
        $this->addElement('text', 'admin_password', Translation::get("AdminPass"), array('size' => '40'));
        $this->addRule(
            'admin_password',
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required');
        $this->addElement('category');

        $this->addElement('category', Translation::get('Platform'));
        $this->addElement('text', 'platform_name', Translation::get("CampusName"), array('size' => '40'));
        $this->addRule(
            'platform_name',
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required');
        $this->addElement('text', 'organization_name', Translation::get("InstituteShortName"), array('size' => '40'));
        $this->addRule(
            'organization_name',
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required');
        $this->addElement('text', 'organization_url', Translation::get("InstituteURL"), array('size' => '40'));
        $this->addRule(
            'organization_url',
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required');
        $this->addElement('category');

        $this->addElement('category', Translation::get('Security'));

        $selfRegistration = array();
        $selfRegistration[] = $this->createElement(
            'radio',
            'self_reg',
            null,
            Translation::get('ConfirmYes', null, Utilities::COMMON_LIBRARIES),
            1);
        $selfRegistration[] = $this->createElement('radio', 'self_reg', null, Translation::get('AfterApproval'), 2);
        $selfRegistration[] = $this->createElement(
            'radio',
            'self_reg',
            null,
            Translation::get('ConfirmNo', null, Utilities::COMMON_LIBRARIES),
            0);
        $this->addGroup($selfRegistration, 'self_reg', Translation::get("AllowSelfReg"), '&nbsp;', false);

        $this->addElement(
            'select',
            'hashing_algorithm',
            Translation::get('HashingAlgorithm'),
            Hashing::get_available_types());
        $this->addElement('category');

        $this->addElement('category', Translation::get('Storage'));
        $this->addElement('text', 'archive_path', Translation::get("ArchivePath"), array('size' => '40'));
        $this->addElement('text', 'cache_path', Translation::get("CachePath"), array('size' => '40'));
        $this->addElement('text', 'garbage_path', Translation::get("GarbagePath"), array('size' => '40'));
        $this->addElement('text', 'hotpotatoes_path', Translation::get("HotpotatoesPath"), array('size' => '40'));
        $this->addElement('text', 'logs_path', Translation::get("LogsPath"), array('size' => '40'));
        $this->addElement('text', 'repository_path', Translation::get("RepositoryPath"), array('size' => '40'));
        $this->addElement('text', 'scorm_path', Translation::get("ScormPath"), array('size' => '40'));
        $this->addElement('text', 'temp_path', Translation::get("TempPath"), array('size' => '40'));
        $this->addElement('text', 'userpictures_path', Translation::get("UserPicturesPath"), array('size' => '40'));
        $this->addElement('category');
    }

    protected function get_database_drivers()
    {
        $drivers = array();
        $drivers['mysqli'] = 'MySQL >= 4.1.3';
        return $drivers;
    }

    protected function addPackageSelectionToggle()
    {
        $html = array();

        $html[] = '&nbsp;';
        $html[] = '<small>';
        $html[] = '<a class="label label-success package-list-select-all">';
        $html[] = Translation::get('SelectAll', null, Utilities::COMMON_LIBRARIES);
        $html[] = '</a>';
        $html[] = '&nbsp;';
        $html[] = '<a class="label label-default package-list-select-none">';
        $html[] = Translation::get('UnselectAll', null, Utilities::COMMON_LIBRARIES);
        $html[] = '</a>';
        $html[] = '</small>';

        return implode(PHP_EOL, $html);
    }

    public function addPackageSettings()
    {
        $html = array();

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

        $html = array();

        $html[] = '<script type="text/javascript" src="' .
             Path::getInstance()->getJavascriptPath('Chamilo\Core\Install', true) . 'Install.js"></script>';
        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));
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
                $extra = $package->get_extra();

                if ($extra['core-install'])
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

    /**
     *
     * @param \Chamilo\Configuration\Package\PackageList $packageList
     */
    public function renderPackages(PackageList $packageList)
    {
        $html = array();

        $renderer = $this->defaultRenderer();
        $packages = $this->determinePackages($packageList);

        if (count($packages) > 0)
        {
            $firstPackage = current($packages);
            $packageType = Translation::get('TypeCategory', null, $firstPackage->get_context());

            $html = array();

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
                $extraPackageInfo = $package->get_extra();

                $title = Translation::get('TypeName', null, $package->get_context());
                $packageClasses = $this->getPackageClasses($package);

                if ($extraPackageInfo['core-install'])
                {
                    $iconSource = Theme::getInstance()->getImagePath($package->get_context(), 'Logo/22Na');
                    $disabled = ' disabled="disabled"';
                }
                else
                {
                    $iconSource = Theme::getInstance()->getImagePath($package->get_context(), 'Logo/22');
                    $disabled = '';
                }

                $html = array();
                $html[] = '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">';
                $html[] = '<a class="' . $packageClasses . '"' . $disabled . '><img src="' . $iconSource . '"> ';
                $this->addElement('html', implode(PHP_EOL, $html));

                $checkbox_name = 'install_' . ClassnameUtilities::getInstance()->getNamespaceId($package->get_context());
                $this->addElement('checkbox', 'install[' . $package->get_context() . ']');
                $renderer->setElementTemplate('{element}', 'install[' . $package->get_context() . ']');

                $html = array();
                $html[] = $title;
                $html[] = '</a>';
                $html[] = '</div>';
                $this->addElement('html', implode(PHP_EOL, $html));

                $extra = $package->get_extra();

                if ($extra['core-install'] || $extra['default-install'])
                {
                    $defaults['install'][$package->get_context()] = 1;
                }
            }

            $this->setDefaults($defaults);

            $html = array();
            $html[] = '<div class="clear"></div>';
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
     *
     * @param \Chamilo\Configuration\Package\PackageList $packageList
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package[]
     */
    public function determinePackages(PackageList $packageList)
    {
        $packages = array();

        foreach ($packageList->get_packages() as $namespace => $package)
        {
            if (strpos($namespace, '\Integration\Chamilo\\') === false)
            {
                $packages[] = $package;
            }
        }

        usort($packages, array($this, "orderPackages"));

        return $packages;
    }

    public function orderPackages($packageLeft, $packageRight)
    {
        $packageNameLeft = Translation::get('TypeName', null, $packageLeft->get_context());
        $packageNameRight = Translation::get('TypeName', null, $packageRight->get_context());

        return strcmp($packageNameLeft, $packageNameRight);
    }

    /**
     *
     * @param \Chamilo\Configuration\Package\Storage\DataClass\Package $package
     * @return string
     */
    private function getPackageClasses(Package $package)
    {
        $classes = array('btn');

        $extra = $package->get_extra();

        if ($extra['core-install'])
        {
            $classes[] = 'btn-default';
        }
        else
        {
            if ($extra['default-install'])
            {
                $sessionSettings = $this->getSessionSettings();

                if (! empty($sessionSettings))
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
     *
     * @return string[]
     */
    protected function getSessionSettings()
    {
        if (! isset($this->sessionSettings))
        {
            $sessionSettings = Session::retrieve(Manager::PARAM_SETTINGS);

            if (is_null($sessionSettings))
            {
                $sessionSettings = array();
            }
            else
            {
                $sessionSettings = unserialize($sessionSettings);
            }

            $this->sessionSettings = $sessionSettings;
        }

        return $this->sessionSettings;
    }

    public function setDefaults($defaults = array())
    {
        $sessionSettings = $this->getSessionSettings();

        if (! empty($sessionSettings))
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
            $defaults['platform_name'] = Translation::get('MyChamilo');
            $defaults['organization_name'] = Translation::get('Chamilo');
            $defaults['organization_url'] = 'http://www.cosnics.org';
            $defaults['self_reg'] = 0;
            $defaults['encrypt_password'] = 1;
            $defaults['hashing_algorithm'] = 'Sha1';

            // Storage paths
            $defaults['archive_path'] = Path::getInstance()->getStoragePath('archive');
            $defaults['cache_path'] = Path::getInstance()->getStoragePath('cache');
            $defaults['garbage_path'] = Path::getInstance()->getStoragePath('garbage');
            $defaults['hotpotatoes_path'] = Path::getInstance()->getStoragePath('hotpotatoes');
            $defaults['logs_path'] = Path::getInstance()->getStoragePath('logs');
            $defaults['repository_path'] = Path::getInstance()->getStoragePath('repository');
            $defaults['scorm_path'] = Path::getInstance()->getStoragePath('scorm');
            $defaults['temp_path'] = Path::getInstance()->getStoragePath('temp');
            $defaults['userpictures_path'] = Path::getInstance()->getStoragePath('userpictures');
        }

        parent::setDefaults($defaults);
    }
}