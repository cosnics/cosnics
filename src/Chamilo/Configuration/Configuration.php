<?php
namespace Chamilo\Configuration;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Configuration\Storage\DataManager;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\Cache\DataClassResultSetCache;
use Chamilo\Libraries\Storage\DataManager\DataSourceName;
use Doctrine\Common\ClassLoader;
use Doctrine\DBAL\DriverManager;
use Chamilo\Libraries\File\Cache\CacheFactory;
use Chamilo\Libraries\File\Cache\Cache;
use Chamilo\Libraries\File\Cache\CacheUnavailableException;

/**
 * This class represents the current configuration
 *
 * @package libraries
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Configuration
{
    const REGISTRATION_CONTEXT = 1;
    const REGISTRATION_TYPE = 2;

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
     * @var Registration[]
     */
    private $registrations;

    /**
     *
     * @var boolean
     */
    private $isAvailable;

    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->initialize();
    }

    /**
     * Returns the instance of this class.
     *
     * @return Configuration The instance.
     */
    public static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new static();

            if (self :: $instance->is_available() && self :: $instance->is_connectable())
            {
                self :: $instance->load_from_storage();
            }
            else
            {
                self :: $instance->load_default();
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
            $this->load_file();
        }
        else
        {
            $this->load_default();
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
            $file = $this->get_configuration_path();

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
        $classLoader = new ClassLoader('Doctrine', __DIR__ . '/../../plugin/');
        $classLoader->register();

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
     *
     * @return string
     */
    private function get_configuration_path()
    {
        return \Chamilo\Libraries\File\Path :: getInstance()->getStoragePath() . 'configuration/configuration.ini';
    }

    /**
     * Load the default base configuration file
     */
    private function load_file()
    {
        $file = $this->get_configuration_path();
        $this->settings[__NAMESPACE__] = parse_ini_file($file, true);
    }

    /**
     * Load the persistently stored configuration elements
     */
    private function load_from_storage()
    {
        $this->load_settings();
        $this->load_registrations();
    }

    /**
     * Load settings from storage
     */
    private function load_settings()
    {
        $cacheFactory = new CacheFactory(Cache :: TYPE_PHP, __NAMESPACE__, 'configuration.settings');
        $cache = $cacheFactory->getCache();

        try
        {
            $this->settings = $cache->get();
        }
        catch (CacheUnavailableException $exception)
        {
            $settings = DataManager :: retrieves(Setting :: class_name());

            while ($setting = $settings->next_result())
            {
                $this->settings[$setting->get_application()][$setting->get_variable()] = $setting->get_value();
            }

            $cache->set($this->settings);
        }
    }

    /**
     * Load registrations from storage
     */
    private function load_registrations()
    {
        $cacheFactory = new CacheFactory(Cache :: TYPE_SERIALIZE, __NAMESPACE__, 'configuration.registrations');
        $cache = $cacheFactory->getCache();

        try
        {
            $this->registrations = $cache->get();
        }
        catch (CacheUnavailableException $exception)
        {
            $registrations = DataManager :: retrieves(Registration :: class_name());

            while ($registration = $registrations->next_result())
            {
                $this->registrations[self :: REGISTRATION_TYPE][$registration->get_type()][$registration->get_context()] = $registration;
                $this->registrations[self :: REGISTRATION_CONTEXT][$registration->get_context()] = $registration;
            }

            $cache->set($this->registrations);
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

    public static function is_registered($context)
    {
        $registration = self :: registration($context);
        return ($registration instanceof Registration);
    }

    /**
     * Load a default configuration which should enable the installer to function properly without a completely
     * configured environment
     */
    private function load_default()
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

        $this->set(array('Chamilo\Core\Menu', 'show_sitemap'), false);

        $this->set(array('Chamilo\Core\User', 'allow_user_change_platform_language'), false);
        $this->set(array('Chamilo\Core\User', 'allow_user_quick_change_platform_language'), false);

        $url_append = str_replace('/src/Core/Install/index.php', '', $_SERVER['PHP_SELF']);

        $this->set(
            array('Chamilo\Configuration', 'general', 'root_web'),
            'http://' . $_SERVER['HTTP_HOST'] . $url_append . '/');
        $this->set(array('Chamilo\Configuration', 'general', 'url_append'), $url_append);

        $this->set(array('Chamilo\Configuration', 'general', 'hashing_algorithm'), 'sha1');
        $this->set(array('Chamilo\Configuration', 'debug', 'show_errors'), false);
    }

    /**
     * Trigger a reset of the entire configuration to force a reload from storage
     */
    public static function reset()
    {
        DataClassResultSetCache :: truncates(array(Registration :: class_name(), Setting :: class_name()));

        $cacheFactory = new CacheFactory(Cache :: TYPE_SERIALIZE, __NAMESPACE__, 'configuration.registrations');
        $cacheFactory->getCache()->truncate();

        $cacheFactory = new CacheFactory(Cache :: TYPE_PHP, __NAMESPACE__, 'configuration.settings');
        $cacheFactory->getCache()->truncate();

        self :: get_instance()->load_from_storage();
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
