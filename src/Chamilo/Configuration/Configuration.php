<?php
namespace Chamilo\Configuration;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Configuration\Service\LanguageConsulter;
use Chamilo\Configuration\Service\RegistrationConsulter;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataManager\DataSourceName;
use Doctrine\DBAL\DriverManager;
use Exception;

/**
 * This class represents the current configuration
 *
 * @package Chamilo\Configuration
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @deprecated Use a ConfigurationConsulter service now
 */
class Configuration
{
    use DependencyInjectionContainerTrait;

    // Constants
    public const REGISTRATION_CONTEXT = 1;

    public const REGISTRATION_INTEGRATION = 3;

    public const REGISTRATION_TYPE = 2;

    /**
     * Instance of this class for the singleton pattern.
     *
     * @var \Chamilo\Configuration\Configuration
     */
    private static $instance;

    public function __construct()
    {
        $this->initializeContainer();
    }

    /**
     *
     * @return bool
     */
    public static function available()
    {
        return self::getInstance()->is_available();
    }

    /**
     *
     * @return string
     */
    public static function get()
    {
        return self::getInstance()->get_setting(func_get_args());
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getConfigurationConsulter()
    {
        return $this->getService(ConfigurationConsulter::class);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    public function getDataClassRepositoryCache()
    {
        return $this->getService(DataClassRepositoryCache::class);
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\FileConfigurationLocator
     */
    public function getFileConfigurationLocator()
    {
        return $this->getService(FileConfigurationLocator::class);
    }

    /**
     * Returns the instance of this class.
     *
     * @return \Chamilo\Configuration\Configuration The instance.
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     *
     * @param string $integration
     * @param string $root
     *
     * @return string[][]
     */
    public function getIntegrationRegistrations($integration, $root = null)
    {
        return $this->getRegistrationConsulter()->getIntegrationRegistrations($integration, $root);
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\LanguageConsulter
     */
    public function getLanguageConsulter()
    {
        return $this->getService(LanguageConsulter::class);
    }

    /**
     *
     * @param string $isocode
     *
     * @return string
     */
    public function getLanguageNameFromIsocode($isocode)
    {
        return $this->getLanguageConsulter()->getLanguageNameFromIsocode($isocode);
    }

    /**
     *
     * @return string[]
     */
    public function getLanguages()
    {
        return $this->getLanguageConsulter()->getLanguages();
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\RegistrationConsulter
     */
    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->getService(RegistrationConsulter::class);
    }

    /**
     *
     * @param string $context
     *
     * @return string[]
     */
    public function get_registration($context)
    {
        return $this->getRegistrationConsulter()->getRegistrationForContext($context);
    }

    /**
     *
     * @return string[]
     */
    public function get_registration_contexts()
    {
        return $this->getRegistrationConsulter()->getRegistrationContexts();
    }

    /**
     *
     * @return string[]
     */
    public function get_registrations()
    {
        return $this->getRegistrationConsulter()->getRegistrations();
    }

    /**
     *
     * @param string $type
     *
     * @return string[][]
     */
    public function get_registrations_by_type($type)
    {
        return $this->getRegistrationConsulter()->getRegistrationsByType($type);
    }

    /**
     *
     * @param string[] $keys
     *
     * @return string
     */
    public function get_setting($keys)
    {
        return $this->getConfigurationConsulter()->getSetting($keys);
    }

    /**
     *
     * @param string $context
     *
     * @return bool
     */
    public function has_settings($context)
    {
        return $this->getConfigurationConsulter()->hasSettingsForContext($context);
    }

    /**
     *
     * @param string $context
     *
     * @return bool
     */
    public function isRegistered($context)
    {
        return $this->getRegistrationConsulter()->isContextRegistered($context);
    }

    /**
     *
     * @param string $context
     *
     * @return bool
     */
    public function isRegisteredAndActive($context)
    {
        return $this->getRegistrationConsulter()->isContextRegisteredAndActive($context);
    }

    /**
     *
     * @return bool
     */
    public function is_available()
    {
        return $this->getFileConfigurationLocator()->isAvailable();
    }

    public function is_connectable()
    {
        $configuration = new \Doctrine\DBAL\Configuration();

        $data_source_name = DataSourceName::factory(
            'Doctrine', array(
                'driver' => Configuration::get('Chamilo\Configuration', 'database', 'driver'),
                'username' => Configuration::get('Chamilo\Configuration', 'database', 'username'),
                'host' => Configuration::get('Chamilo\Configuration', 'database', 'host'),
                'name' => Configuration::get('Chamilo\Configuration', 'database', 'name'),
                'password' => Configuration::get('Chamilo\Configuration', 'database', 'password')
            )
        );

        $connection_parameters = array(
            'user' => $data_source_name->getUsername(),
            'password' => $data_source_name->getPassword(),
            'host' => $data_source_name->getHost(),
            'driverClass' => $data_source_name->getDriver(true)
        );

        try
        {
            DriverManager::getConnection($connection_parameters, $configuration)->connect();

            return true;
        }
        catch (Exception $exception)
        {
            return false;
        }
    }

    /**
     *
     * @param string $context
     *
     * @return bool
     */
    public static function is_registered($context)
    {
        return self::getInstance()->isRegistered($context);
    }

    /**
     *
     * @param string $context
     *
     * @return string[]
     */
    public static function registration($context)
    {
        return self::getInstance()->get_registration($context);
    }

    /**
     *
     * @return string[]
     */
    public static function registrations()
    {
        return self::getInstance()->get_registrations();
    }

    /**
     *
     * @param string $type
     *
     * @return string[][]
     */
    public static function registrations_by_type($type)
    {
        return self::getInstance()->get_registrations_by_type($type);
    }

    /**
     * Trigger a reset of the entire configuration to force a reload from storage
     * TODO: Reimplement this in the new services
     */
    public static function reset()
    {
        self::getInstance()->getDataClassRepositoryCache()->truncates(array(Registration::class, Setting::class));
        self::getInstance()->getConfigurationConsulter()->clearData();
        self::getInstance()->getRegistrationConsulter()->clearData();
        self::getInstance()->getLanguageConsulter()->clearData();
    }

    /**
     *
     * @param string[] $keys
     * @param mixed $value TODO: Reimplement this in the new services ?
     */
    public function set($keys, $value)
    {
        $variables = $keys;
        $values = &$this->settings;

        while (count($variables) > 0)
        {
            $key = array_shift($variables);

            if (!isset($values[$key]))
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
}
