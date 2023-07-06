<?php
namespace Chamilo\Core\Install\Form;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Service\Consulter\LanguageConsulter;
use Chamilo\Configuration\Storage\DataClass\Language;
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
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Install\Form
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class SettingsForm extends FormValidator
{

    /**
     * @var string[]
     */
    protected array $sessionSettings;

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

    /**
     * @throws \QuickformException
     */
    public function addDatabaseSettings()
    {
        $translator = $this->getTranslator();

        $this->addElement(
            'select', 'database_driver', $translator->trans('DatabaseDriver', [], Manager::CONTEXT),
            $this->get_database_drivers()
        );
        $this->addElement(
            'text', 'database_host', $translator->trans('DatabaseHost', [], Manager::CONTEXT), ['size' => '40']
        );
        $this->addElement(
            'text', 'database_name', $translator->trans('DatabaseName', [], Manager::CONTEXT), ['size' => '40']
        );

        $this->addElement(
            'text', 'database_username', $translator->trans('DatabaseLogin', [], Manager::CONTEXT), ['size' => '40']
        );
        $this->addElement(
            'password', 'database_password', $translator->trans('DatabasePassword', [], Manager::CONTEXT),
            ['size' => '40']
        );

        $this->addElement(
            'checkbox', 'database_overwrite', $translator->trans('DatabaseOverwrite', [], Manager::CONTEXT)
        );

        $this->addRule('database_host', 'ThisFieldIsRequired', 'required');
        $this->addRule('database_driver', 'ThisFieldIsRequired', 'required');
        $this->addRule('database_name', 'ThisFieldIsRequired', 'required');

        $pattern = '/(^[a-zA-Z$_][0-9a-zA-Z$_]*$)|(^[0-9][0-9a-zA-Z$_]*[a-zA-Z$_][0-9a-zA-Z$_]*$)/';
        $this->addRule('database_name', 'OnlyCharactersNumbersUnderscoresAndDollarSigns', 'regex', $pattern);
        $this->addRule(
            ['database_driver', 'database_host', 'database_username', 'database_password', 'database_name'],
            $translator->trans('CouldNotConnectToDatabase', [], Manager::CONTEXT), 'validate_database_connection'
        );
    }

    /**
     * @throws \QuickformException
     */
    public function addGeneralSettings(): void
    {
        $translator = $this->getTranslator();

        $this->addElement('category', $translator->trans('GeneralProperties', [], Manager::CONTEXT));
        $this->addElement(
            'select', 'platform_language', $translator->trans('MainLang', [], Manager::CONTEXT),
            $this->getLanguageOptions()
        );

        $this->addElement('category', $translator->trans('Administrator', [], Manager::CONTEXT));
        $this->addElement(
            'text', 'admin_email', $translator->trans('AdminEmail', [], Manager::CONTEXT), ['size' => '40']
        );
        $this->addRule(
            'admin_email', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required'
        );
        $this->addRule('admin_email', $translator->trans('WrongEmail', [], Manager::CONTEXT), 'email');
        $this->addElement(
            'text', 'admin_surname', $translator->trans('AdminLastName', [], Manager::CONTEXT), ['size' => '40']
        );
        $this->addRule(
            'admin_surname', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required'
        );
        $this->addElement(
            'text', 'admin_firstname', $translator->trans('AdminFirstName', [], Manager::CONTEXT), ['size' => '40']
        );
        $this->addRule(
            'admin_firstname', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required'
        );
        $this->addElement(
            'text', 'admin_phone', $translator->trans('AdminPhone', [], Manager::CONTEXT), ['size' => '40']
        );
        $this->addElement(
            'text', 'admin_username', $translator->trans('AdminLogin', [], Manager::CONTEXT), ['size' => '40']
        );
        $this->addRule(
            'admin_username', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required'
        );
        $this->addElement(
            'text', 'admin_password', $translator->trans('AdminPass', [], Manager::CONTEXT), ['size' => '40']
        );
        $this->addRule(
            'admin_password', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required'
        );

        $this->addElement('category', $translator->trans('Platform', [], Manager::CONTEXT));
        $this->addElement('text', 'site_name', $translator->trans('CampusName', [], Manager::CONTEXT), ['size' => '40']
        );
        $this->addRule(
            'site_name', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required'
        );
        $this->addElement(
            'text', 'organization_name', $translator->trans('InstituteShortName', [], Manager::CONTEXT),
            ['size' => '40']
        );
        $this->addRule(
            'organization_name', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required'
        );
        $this->addElement(
            'text', 'organization_url', $translator->trans('InstituteURL', [], Manager::CONTEXT), ['size' => '40']
        );
        $this->addRule(
            'organization_url', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required'
        );

        $this->addElement('category', $translator->trans('Security', [], Manager::CONTEXT));

        $selfRegistration = [];
        $selfRegistration[] = $this->createElement(
            'radio', 'self_reg', null, $translator->trans('ConfirmYes', [], StringUtilities::LIBRARIES), 1
        );
        $selfRegistration[] = $this->createElement(
            'radio', 'self_reg', null, $translator->trans('AfterApproval', [], Manager::CONTEXT), 2
        );
        $selfRegistration[] = $this->createElement(
            'radio', 'self_reg', null, $translator->trans('ConfirmNo', [], StringUtilities::LIBRARIES), 0
        );
        $this->addGroup(
            $selfRegistration, 'self_reg', $translator->trans('AllowSelfReg', [], Manager::CONTEXT), '&nbsp;', false
        );

        $this->addElement(
            'select', 'hashing_algorithm', $translator->trans('HashingAlgorithm', [], Manager::CONTEXT),
            HashingUtilities::getAvailableTypes()
        );

        $this->addElement('category', $translator->trans('Storage', [], Manager::CONTEXT));
        $this->addElement(
            'text', 'archive_path', $translator->trans('ArchivePath', [], Manager::CONTEXT), ['size' => '40']
        );
        $this->addElement('text', 'cache_path', $translator->trans('CachePath', [], Manager::CONTEXT), ['size' => '40']
        );
        $this->addElement(
            'text', 'garbage_path', $translator->trans('GarbagePath', [], Manager::CONTEXT), ['size' => '40']
        );
        $this->addElement(
            'text', 'hotpotatoes_path', $translator->trans('HotpotatoesPath', [], Manager::CONTEXT), ['size' => '40']
        );
        $this->addElement('text', 'logs_path', $translator->trans('LogsPath', [], Manager::CONTEXT), ['size' => '40']);
        $this->addElement(
            'text', 'repository_path', $translator->trans('RepositoryPath', [], Manager::CONTEXT), ['size' => '40']
        );
        $this->addElement('text', 'scorm_path', $translator->trans('ScormPath', [], Manager::CONTEXT), ['size' => '40']
        );
        $this->addElement('text', 'temp_path', $translator->trans('TempPath', [], Manager::CONTEXT), ['size' => '40']);
        $this->addElement(
            'text', 'userpictures_path', $translator->trans('UserPicturesPath', [], Manager::CONTEXT), ['size' => '40']
        );
    }

    protected function addPackageSelectionToggle(): string
    {
        $translator = $this->getTranslator();

        $html = [];

        $html[] = '&nbsp;';
        $html[] = '<small>';
        $html[] = '<a class="label label-success package-list-select-all">';
        $html[] = $translator->trans('SelectAll', [], StringUtilities::LIBRARIES);
        $html[] = '</a>';
        $html[] = '&nbsp;';
        $html[] = '<a class="label label-default package-list-select-none">';
        $html[] = $translator->trans('UnselectAll', [], StringUtilities::LIBRARIES);
        $html[] = '</a>';
        $html[] = '</small>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \QuickformException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function addPackageSettings(): void
    {
        $html = [];

        $html[] = '<div class="package-selection">';
        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';
        $html[] = '<h4>';
        $html[] = $this->getTranslator()->trans('AllPackages');
        $html[] = $this->addPackageSelectionToggle();
        $html[] = '</h4>';
        $html[] = '</div>';
        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->renderPackages($this->getPackageBundlesCacheService()->getAllPackages());

        $html = [];

        $html[] = '<script src="' . $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Core\Install') .
            'Install.js"></script>';
        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));
    }

    /**
     * @throws \QuickformException
     */
    protected function buildForm(): void
    {
        $translator = $this->getTranslator();

        $tabsCollection = new TabsCollection();

        $tabsCollection->add(
            new FormTab(
                'database', $translator->trans('DatabaseComponentTitle', [], Manager::CONTEXT), null,
                'addDatabaseSettings'
            )
        );
        $tabsCollection->add(
            new FormTab(
                'general', $translator->trans('SettingsComponentTitle', [], Manager::CONTEXT), null,
                'addGeneralSettings'
            )
        );
        $tabsCollection->add(
            new FormTab(
                'package', $translator->trans('PackageComponentTitle', [], Manager::CONTEXT), null, 'addPackageSettings'
            )
        );

        $this->getFormTabsGenerator()->generate('settings', $this, $tabsCollection);

        $buttons = [];

        $glyph = new FontAwesomeGlyph('chevron-left', [], null, 'fas');

        $licenseUrl = $this->getUrlGenerator()->fromParameters(
            [Application::PARAM_CONTEXT => Manager::CONTEXT, Manager::PARAM_ACTION => Manager::ACTION_LICENSE]
        );

        $buttons[] = $this->createElement(
            'static', null, null, '<a href="' . $licenseUrl . '" class="btn btn-default">' . $glyph->render() .
            $translator->trans('Previous', [], StringUtilities::LIBRARIES) . '</a>'
        );

        $buttons[] = $this->createElement(
            'style_button', 'next', $translator->trans('Next', [], StringUtilities::LIBRARIES),
            ['class' => 'btn-primary'], null, new FontAwesomeGlyph('chevron-right')
        );
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * @param \Chamilo\Configuration\Package\PackageList $packageList
     *
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package[]
     */
    public function determinePackages(PackageList $packageList): array
    {
        $packages = [];

        foreach ($packageList->getPackages() as $namespace => $package)
        {
            if (!str_contains($namespace, '\Integration\Chamilo\\'))
            {
                $packages[] = $package;
            }
        }

        usort($packages, [$this, 'orderPackages']);

        return $packages;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function getLanguageConsulter(): LanguageConsulter
    {
        return $this->getService(LanguageConsulter::class);
    }

    protected function getLanguageOptions(): array
    {
        $languageOptions = [];

        foreach ($this->getLanguageConsulter()->getLanguagesFromFilesystem() as $isocode => $language)
        {
            $languageOptions[$isocode] = $language[Language::PROPERTY_ORIGINAL_NAME];
        }

        return $languageOptions;
    }

    public function getPackageBundlesCacheService(): PackageBundlesCacheService
    {
        return $this->getService(PackageBundlesCacheService::class);
    }

    private function getPackageClasses(Package $package): string
    {
        $classes = ['btn'];

        if ($package->getCoreInstall())
        {
            $classes[] = 'btn-default';
        }
        elseif ($package->getDefaultInstall())
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

        return implode(' ', $classes);
    }

    /**
     * @return string[]
     */
    protected function getSessionSettings(): array
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

    protected function get_database_drivers(): array
    {
        $drivers = [];
        $drivers['mysqli'] = 'MySQL >= 4.1.3';

        return $drivers;
    }

    protected function hasSelectablePackages($packages): bool
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

    public function orderPackages($packageLeft, $packageRight): int
    {
        $translator = $this->getTranslator();

        $packageNameLeft = $translator->trans('TypeName', [], $packageLeft->get_context());
        $packageNameRight = $translator->trans('TypeName', [], $packageRight->get_context());

        return strcmp($packageNameLeft, $packageNameRight);
    }

    /**
     * @param \Chamilo\Configuration\Package\PackageList $packageList
     *
     * @throws \QuickformException
     */
    public function renderPackages(PackageList $packageList): void
    {
        $translator = $this->getTranslator();

        $renderer = $this->defaultRenderer();
        $packages = $this->determinePackages($packageList);

        if (count($packages) > 0)
        {
            $firstPackage = current($packages);
            $packageType = $translator->trans('TypeCategory', [], $firstPackage->get_context());

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
                $title = $translator->trans('TypeName', [], $package->get_context());
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

        foreach ($packageList->getPackageLists() as $child)
        {
            $this->renderPackages($child);
        }
    }

    public function setApplication(Application $application): void
    {
        $this->application = $application;
    }

    public function setDefaults(array $defaultValues = [], $filter = null)
    {
        $translator = $this->getTranslator();

        $sessionSettings = $this->getSessionSettings();

        if (!empty($sessionSettings))
        {
            $defaultValues = $sessionSettings;
        }
        else
        {
            // Database
            $defaultValues['database_driver'] = 'mysqli';
            $defaultValues['database_host'] = 'localhost';
            $defaultValues['database_name'] = 'cosnics';

            // General settings

            $defaultValues['platform_language'] = $translator->getLocale();
            $defaultValues['admin_email'] = $_SERVER['SERVER_ADMIN'];
            $email_parts = explode('@', $defaultValues['admin_email']);
            if ($email_parts[1] == 'localhost')
            {
                $defaultValues['admin_email'] .= '.localdomain';
            }
            $defaultValues['admin_surname'] = 'Doe';
            $defaultValues['admin_firstname'] = mt_rand(0, 1) ? 'John' : 'Jane';
            $defaultValues['admin_username'] = 'admin';
            $defaultValues['site_name'] = $translator->trans('MyChamilo', [], Manager::CONTEXT);
            $defaultValues['organization_name'] = $translator->trans('Chamilo', [], Manager::CONTEXT);
            $defaultValues['organization_url'] = 'http://www.cosnics.org';
            $defaultValues['self_reg'] = 0;
            $defaultValues['encrypt_password'] = 1;
            $defaultValues['hashing_algorithm'] = 'Sha1';

            // Storage paths
            $defaultValues['archive_path'] = $this->getConfigurablePathBuilder()->getArchivePath();
            $defaultValues['cache_path'] = $this->getConfigurablePathBuilder()->getCachePath();
            $defaultValues['garbage_path'] = $this->getConfigurablePathBuilder()->getGarbagePath();
            $defaultValues['hotpotatoes_path'] =
                $this->getSystemPathBuilder()->getPublicStoragePath(Hotpotatoes::CONTEXT);
            $defaultValues['logs_path'] = $this->getConfigurablePathBuilder()->getLogPath();
            $defaultValues['repository_path'] = $this->getConfigurablePathBuilder()->getRepositoryPath();
            $defaultValues['scorm_path'] = $this->getConfigurablePathBuilder()->getScormPath();
            $defaultValues['temp_path'] = $this->getConfigurablePathBuilder()->getTemporaryPath();
            $defaultValues['userpictures_path'] = $this->getConfigurablePathBuilder()->getUserPicturesPath();
        }

        parent::setDefaults($defaultValues);
    }
}