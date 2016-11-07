<?php
namespace Chamilo\Configuration;

use Chamilo\Configuration\Storage\DataClass\Language;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Libraries\Storage\DataManager\DataSourceName;
use Doctrine\DBAL\DriverManager;

/**
 * This class represents the current configuration
 *
 * @package Chamilo\Configuration
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @deprecated Use a ConfigurationConsulter service now
 */
class Configuration
{
    use \Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;

    // Constants
    const REGISTRATION_CONTEXT = 1;
    const REGISTRATION_TYPE = 2;
    const REGISTRATION_INTEGRATION = 3;

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
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getConfigurationConsulter()
    {
        return $this->getService('chamilo.configuration.service.configuration_consulter');
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\RegistrationConsulter
     */
    public function getRegistrationConsulter()
    {
        return $this->getService('chamilo.configuration.service.registration_consulter');
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\LanguageConsulter
     */
    public function getLanguageConsulter()
    {
        return $this->getService('chamilo.configuration.service.language_consulter');
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\FileConfigurationLocator
     */
    public function getFileConfigurationLocator()
    {
        return $this->getService('chamilo.configuration.service.file_configuration_locator');
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    public function getDataClassRepositoryCache()
    {
        return $this->getService('chamilo.libraries.storage.cache.data_class_repository_cache');
    }

    /**
     * Returns the instance of this class.
     *
     * @return \Chamilo\Configuration\Configuration The instance.
     */
    public static function get_instance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     *
     * @param string[] $keys
     * @return string
     */
    public function get_setting($keys)
    {
        return $this->getConfigurationConsulter()->getSetting($keys);
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
     * @return string
     */
    public static function get()
    {
        return self::get_instance()->get_setting(func_get_args());
    }

    /**
     *
     * @return boolean
     */
    public function is_available()
    {
        return $this->getFileConfigurationLocator()->isAvailable();
    }

    public function is_connectable()
    {
        $configuration = new \Doctrine\DBAL\Configuration();

        $data_source_name = DataSourceName::factory(
            'Doctrine',
            array(
                'driver' => \Chamilo\Configuration\Configuration::get('Chamilo\Configuration', 'database', 'driver'),
                'username' => \Chamilo\Configuration\Configuration::get('Chamilo\Configuration', 'database', 'username'),
                'host' => \Chamilo\Configuration\Configuration::get('Chamilo\Configuration', 'database', 'host'),
                'name' => \Chamilo\Configuration\Configuration::get('Chamilo\Configuration', 'database', 'name'),
                'password' => \Chamilo\Configuration\Configuration::get('Chamilo\Configuration', 'database', 'password')));

        $connection_parameters = array(
            'user' => $data_source_name->get_username(),
            'password' => $data_source_name->get_password(),
            'host' => $data_source_name->get_host(),
            'driverClass' => $data_source_name->get_driver(true));

        try
        {
            DriverManager::getConnection($connection_parameters, $configuration)->connect();
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
        return self::get_instance()->is_available();
    }

    /**
     *
     * @param string $context
     * @return string[]
     */
    public function get_registration($context)
    {
        return $this->getRegistrationConsulter()->getRegistrationForContext($context);
    }

    /**
     *
     * @param string $context
     * @return string[]
     */
    public static function registration($context)
    {
        return self::get_instance()->get_registration($context);
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
     * @return string[]
     */
    public function get_registration_contexts()
    {
        return $this->getRegistrationConsulter()->getRegistrationContexts();
    }

    /**
     *
     * @param string $type
     * @return string[]
     */
    public function get_registrations_by_type($type)
    {
        return $this->getRegistrationConsulter()->getRegistrationsByType($type);
    }

    /**
     *
     * @param string $type
     * @return string[]
     */
    public static function registrations_by_type($type)
    {
        return self::get_instance()->get_registrations_by_type($type);
    }

    /**
     *
     * @return string[]
     */
    public static function registrations()
    {
        return self::get_instance()->get_registrations();
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public static function is_registered($context)
    {
        return self::get_instance()->isRegistered($context);
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public function isRegistered($context)
    {
        return $this->getRegistrationConsulter()->isContextRegistered($context);
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public function isRegisteredAndActive($context)
    {
        return $this->getRegistrationConsulter()->isContextRegisteredAndActive($context);
    }

    /**
     *
     * @param string $integration
     * @param string $root
     * @return string[]
     */
    public function getIntegrationRegistrations($integration, $root = null)
    {
        return $this->getRegistrationConsulter()->getIntegrationRegistrations($integration, $root);
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
     * @param string $isocode
     * @return string
     */
    public function getLanguageNameFromIsocode($isocode)
    {
        return $this->getLanguageConsulter()->getLanguageNameFromIsocode($isocode);
    }

    /**
     * Trigger a reset of the entire configuration to force a reload from storage
     * TODO: Reimplement this in the new services
     */
    public static function reset()
    {
        $this->getDataClassRepositoryCache()->truncates(
            array(Registration::class_name(), Setting::class_name(), Language::class_name()));
        self::get_instance()->getConfigurationCacheService()->clear();
        self::get_instance()->loadFromStorage();
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public function has_settings($context)
    {
        return $this->getConfigurationConsulter()->hasSettingsForContext($context);
    }
}
