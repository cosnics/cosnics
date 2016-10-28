<?php
namespace Chamilo\Configuration;

use Chamilo\Configuration\Service\ConfigurationCacheService;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\Cache\RecordResultSetCache;
use Chamilo\Libraries\Storage\DataManager\DataSourceName;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\DBAL\DriverManager;

/**
 * This class represents the current configuration
 *
 * @package Chamilo\Configuration
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Configuration
{
    const REGISTRATION_CONTEXT = 1;
    const REGISTRATION_TYPE = 2;
    const REGISTRATION_INTEGRATION = 3;

    /**
     * Instance of this class for the singleton pattern.
     *
     * @var Configuration
     */
    private static $instance;

    /**
     *
     * @var string[]
     */
    private $settings;

    /**
     *
     * @var \Chamilo\Configuration\Storage\DataClass\Registration[]
     */
    public $registrations;

    /**
     *
     * @var string[]
     */
    private $languages;

    /**
     *
     * @var boolean
     */
    private $isAvailable;

    /**
     *
     * @var \Chamilo\Configuration\Service\ConfigurationCacheService
     */
    private $configurationCacheService;

    /**
     * Constructor.
     */
    public function __construct(ConfigurationCacheService $configurationCacheService)
    {
        $this->configurationCacheService = $configurationCacheService;
        $this->initialize();
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\ConfigurationCacheService
     */
    public function getConfigurationCacheService()
    {
        return $this->configurationCacheService;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\ConfigurationCacheService $configurationCacheService
     */
    public function setConfigurationCacheService($configurationCacheService)
    {
        $this->configurationCacheService = $configurationCacheService;
    }

    /**
     * Returns the instance of this class.
     *
     * @return \Chamilo\Configuration\Configuration The instance.
     */
    public static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new static(new ConfigurationCacheService());

            if (self :: $instance->is_available() && self :: $instance->is_connectable())
            {
                self :: $instance->loadFromStorage();
            }
            else
            {
                self :: $instance->loadDefault();
            }
        }
        return self :: $instance;
    }

    /**
     * Gets a parameter from the configuration.
     *
     * @param string[] $keys
     * @throws \Exception
     * @return mixed
     */
    public function get_setting($keys)
    {
        $variables = $keys;
        $values = $this->settings;

        while (count($variables) > 0)
        {
            $key = array_shift($variables);

            if (! isset($values[$key]))
            {
                if ($this->is_available())
                {
                    return null;
                }
                else
                {
                    echo 'The requested variable is not available in an unconfigured environment (' .
                         implode(' > ', $keys) . ')';
                    exit();
                    // throw new \Exception(
                    // 'The requested variable is not available in an unconfigured environment (' .
                    // implode(' > ', $keys) . ')');
                }
            }
            else
            {
                $values = $values[$key];
            }
        }

        return $values;
    }

    /**
     *
     * @param string[] $keys
     * @param mixed $value
     */
    public function set($keys, $value)
    {
        $variables = $keys;
        $values = &$this->settings;

        while (count($variables) > 0)
        {
            $key = array_shift($variables);

            if (! isset($values[$key]))
            {
                $values[$key] = null;
                $values = &$values[$key];
            }
            else
            {
                $values = &$values[$key];
            }
        }

        $values = $value;
    }

    /**
     *
     * @return mixed
     * @deprecated Use \Chamilo\Configuration\Configuration :: get_instance()->get_setting(array($keys)) now
     */
    public static function get()
    {
        return self :: get_instance()->get_setting(func_get_args());
    }

    private function initialize()
    {
        if ($this->is_available())
        {
            $this->loadFile();
        }
        else
        {
            $this->loadDefault();
        }
    }

    /**
     *
     * @throws \Exception
     * @return boolean
     */
    public function is_available()
    {
        if (! isset($this->isAvailable))
        {
            $file = $this->getConfigurationCacheService()->getConfigurationFilePath();

            if (is_file($file) && is_readable($file))
            {
                $this->isAvailable = true;
            }
            else
            {
                $this->isAvailable = false;
            }
        }

        return $this->isAvailable;
    }

    public function is_connectable()
    {
        $configuration = new \Doctrine\DBAL\Configuration();

        $data_source_name = DataSourceName :: factory(
            'Doctrine',
            $this->get_setting(array('Chamilo\Configuration', 'database', 'driver')),
            $this->get_setting(array('Chamilo\Configuration', 'database', 'username')),
            $this->get_setting(array('Chamilo\Configuration', 'database', 'host')),
            $this->get_setting(array('Chamilo\Configuration', 'database', 'name')),
            $this->get_setting(array('Chamilo\Configuration', 'database', 'password')));

        $connection_parameters = array(
            'user' => $data_source_name->get_username(),
            'password' => $data_source_name->get_password(),
            'host' => $data_source_name->get_host(),
            'driverClass' => $data_source_name->get_driver(true));

        try
        {
            DriverManager :: getConnection($connection_parameters, $configuration)->connect();
            return true;
        }
        catch (\Exception $exception)
        {
            return false;
        }
    }

    /**
     *
     * @return boolean
     */
    public static function available()
    {
        return self :: get_instance()->is_available();
    }

    /**
     * Load the default base configuration file
     */
    private function loadFile()
    {
        $this->settings = $this->getConfigurationCacheService()->getConfigurationFileSettings();
    }

    /**
     * Load the persistently stored configuration elements
     */
    private function loadFromStorage()
    {
        $this->registrations = $this->getConfigurationCacheService()->getRegistrationsCache();
        $this->languages = $this->getConfigurationCacheService()->getLanguagesCache();

        $storedSettings = $this->getConfigurationCacheService()->getSettingsCache();

        if ($this->is_available())
        {
            $this->settings = $storedSettings;
        }
        else
        {
            foreach ($storedSettings as $context => $contextSettings)
            {
                foreach ($contextSettings as $variable => $value)
                {
                    $this->settings[$context][$variable] = $value;
                }
            }
        }
    }

    /**
     *
     * @param string $context
     * @return Registration
     */
    public function get_registration($context)
    {
        return $this->registrations[self :: REGISTRATION_CONTEXT][$context];
    }

    /**
     *
     * @param string $context
     * @return \configuration\Registration
     */
    public static function registration($context)
    {
        return self :: get_instance()->get_registration($context);
    }

    /**
     *
     * @return Registration[]
     */
    public function get_registrations()
    {
        return $this->registrations;
    }

    /**
     * Returns the registration contexts
     *
     * @return array
     */
    public function get_registration_contexts()
    {
        $registrations = $this->get_registrations();
        return array_keys($registrations[Configuration::REGISTRATION_CONTEXT]);
    }

    /**
     *
     * @param string $type
     * @return \configuration\Registration[]
     */
    public function get_registrations_by_type($type)
    {
        $registrations = $this->registrations;
        return $registrations[self :: REGISTRATION_TYPE][$type];
    }

    /**
     *
     * @param string $type
     * @return \configuration\Registration[]
     */
    public static function registrations_by_type($type)
    {
        return self :: get_instance()->get_registrations_by_type($type);
    }

    /**
     *
     * @return \configuration\Registration[]
     */
    public static function registrations()
    {
        return self :: get_instance()->get_registrations();
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public static function is_registered($context)
    {
        return self :: get_instance()->isRegistered($context);
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public function isRegistered($context)
    {
        $registration = $this->get_registration($context);
        return ! empty($registration);
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public function isRegisteredAndActive($context)
    {
        $registration = $this->get_registration($context);
        return $this->isRegistered($context) &&
             $registration[Registration :: PROPERTY_STATUS] == Registration :: STATUS_ACTIVE;
    }

    /**
     *
     * @param string $integration
     * @param string $root
     * @return \Chamilo\Configuration\Storage\DataClass\Registration[]
     */
    public function getIntegrationRegistrations($integration, $root = null)
    {
        $integrationRegistrations = $this->registrations[self :: REGISTRATION_INTEGRATION][$integration];

        if ($root)
        {
            $rootIntegrationRegistrations = array();

            foreach ($integrationRegistrations as $rootContext => $registration)
            {
                $rootContextStringUtilities = StringUtilities :: getInstance()->createString($rootContext);
                if ($rootContextStringUtilities->startsWith($root))
                {
                    $rootIntegrationRegistrations[$rootContext] = $registration;
                }
            }

            return $rootIntegrationRegistrations;
        }
        else
        {
            return $integrationRegistrations;
        }
    }

    public function getLanguages()
    {
        return $this->languages;
    }

    public function getLanguageNameFromIsocode($isocode)
    {
        $languages = $this->getLanguages();
        return $languages[$isocode];
    }

    /**
     * Load a default configuration which should enable the installer to function properly without a completely
     * configured environment
     */
    private function loadDefault()
    {
        $settings = array();

        $this->set(array('Chamilo\Core\Admin', 'show_administrator_data'), false);
        $this->set(array('Chamilo\Core\Admin', 'whoisonlineaccess'), 2);
        $this->set(array('Chamilo\Core\Admin', 'site_name'), 'Chamilo');
        $this->set(array('Chamilo\Core\Admin', 'institution'), 'Chamilo');
        $this->set(array('Chamilo\Core\Admin', 'institution_url'), 'http://www.chamilo.org');
        $this->set(array('Chamilo\Core\Admin', 'platform_timezone'), 'Europe/Brussels');
        $this->set(array('Chamilo\Core\Admin', 'theme'), 'Aqua');
        $this->set(array('Chamilo\Core\Admin', 'html_editor'), 'Ckeditor');
        $this->set(array('Chamilo\Core\Admin', 'allow_portal_functionality'), false);
        $this->set(array('Chamilo\Core\Admin', 'enable_external_authentication'), false);
        $this->set(array('Chamilo\Core\Admin', 'server_type'), 'production');
        $this->set(array('Chamilo\Core\Admin', 'installation_blocked'), false);
        $this->set(array('Chamilo\Core\Admin', 'platform_language'), 'en');
        $this->set(array('Chamilo\Core\Admin', 'show_variable_in_translation'), false);
        $this->set(array('Chamilo\Core\Admin', 'write_new_variables_to_translation_file'), false);
        $this->set(array('Chamilo\Core\Admin', 'show_version_data'), false);
        $this->set(array('Chamilo\Core\Admin', 'hide_dcda_markup'), true);
        $this->set(array('Chamilo\Core\Admin', 'version'), '5.0');
        $this->set(array('Chamilo\Core\Admin', 'maintenance_mode'), false);

        $this->set(array('Chamilo\Core\Admin', 'administrator_email'), '');
        $this->set(array('Chamilo\Core\Admin', 'administrator_website'), '');
        $this->set(array('Chamilo\Core\Admin', 'administrator_surname'), '');
        $this->set(array('Chamilo\Core\Admin', 'administrator_firstname'), '');

        $this->set(array('Chamilo\Core\Menu', 'show_sitemap'), false);
        $this->set(array('Chamilo\Core\Menu', 'menu_renderer'), 'Bar');
        $this->set(array('Chamilo\Core\Menu', 'brand_image'), '');

        $this->set(array('Chamilo\Core\Help', 'hide_empty_pages'), true);

        $this->set(array('Chamilo\Core\User', 'allow_user_change_platform_language'), false);
        $this->set(array('Chamilo\Core\User', 'allow_user_quick_change_platform_language'), false);

        $url_append = str_replace('/src/Core/Install/index.php', '', $_SERVER['PHP_SELF']);

        $this->set(
            array('Chamilo\Configuration', 'general', 'root_web'),
            'http://' . $_SERVER['HTTP_HOST'] . $url_append . '/');
        $this->set(array('Chamilo\Configuration', 'general', 'url_append'), $url_append);

        $this->set(array('Chamilo\Configuration', 'general', 'hashing_algorithm'), 'sha1');
        $this->set(array('Chamilo\Configuration', 'debug', 'show_errors'), false);
        $this->set(array('Chamilo\Configuration', 'debug', 'enable_query_cache'), true);
        $this->set(array('Chamilo\Configuration', 'session', 'session_handler'), 'chamilo');

        $this->set(
            array('Chamilo\Configuration', 'storage', 'archive'),
            Path :: getInstance()->getStoragePath('archive'));
        $this->set(
            array('Chamilo\Configuration', 'storage', 'cache_path'),
            Path :: getInstance()->getStoragePath('cache'));
        $this->set(
            array('Chamilo\Configuration', 'storage', 'garbage'),
            Path :: getInstance()->getStoragePath('garbage_path'));
        $this->set(
            array('Chamilo\Configuration', 'storage', 'hotpotatoes_path'),
            Path :: getInstance()->getStoragePath('hotpotatoes'));
        $this->set(
            array('Chamilo\Configuration', 'storage', 'logs_path'),
            Path :: getInstance()->getStoragePath('logs'));
        $this->set(
            array('Chamilo\Configuration', 'storage', 'repository_path'),
            Path :: getInstance()->getStoragePath('repository'));
        $this->set(
            array('Chamilo\Configuration', 'storage', 'scorm_path'),
            Path :: getInstance()->getStoragePath('scorm'));
        $this->set(
            array('Chamilo\Configuration', 'storage', 'temp_path'),
            Path :: getInstance()->getStoragePath('temp'));
        $this->set(
            array('Chamilo\Configuration', 'storage', 'userpictures'),
            Path :: getInstance()->getStoragePath('userpictures_path'));
    }

    /**
     * Trigger a reset of the entire configuration to force a reload from storage
     */
    public static function reset()
    {
        RecordResultSetCache :: truncates(array(Registration :: class_name(), Setting :: class_name()));
        self :: get_instance()->getConfigurationCacheService()->clear();
        self :: get_instance()->loadFromStorage();
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public function has_settings($context)
    {
        return isset($this->settings[$context]);
    }
}
