<?php
namespace Chamilo\Configuration\Repository;

use Chamilo\Configuration\Service\ConfigurationCacheService;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName;
use Doctrine\DBAL\DriverManager;

/**
 *
 * @package Chamilo\Configuration\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ConfigurationRepository
{
    const REGISTRATION_CONTEXT = 1;
    const REGISTRATION_TYPE = 2;
    const REGISTRATION_INTEGRATION = 3;

    /**
     *
     * @var string[]
     */
    private $settings;

    /**
     *
     * @var \Chamilo\Configuration\Storage\DataClass\Registration[]
     */
    private $registrations;

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
     * @var \Chamilo\Libraries\File\Path
     */
    private $pathUtilities;

    /**
     *
     * @var \Chamilo\Configuration\Service\ConfigurationCacheService
     */
    private $configurationCacheService;

    /**
     *
     * @var \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    private $dataClassRepositoryCache;

    /**
     * Constructor.
     */
    public function __construct(Path $pathUtilities, ConfigurationCacheService $configurationCacheService,
        DataClassRepositoryCache $dataClassRepositoryCache)
    {
        $this->pathUtilities = $pathUtilities;
        $this->configurationCacheService = $configurationCacheService;
        $this->dataClassRepositoryCache = $dataClassRepositoryCache;
        $this->initialize();
    }

    public function initialize()
    {
        if ($this->isAvailable())
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
     * @return \Chamilo\Libraries\File\Path
     */
    public function getPathUtilities()
    {
        return $this->pathUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\File\Path $pathUtilities
     */
    public function setPathUtilities(Path $pathUtilities)
    {
        $this->pathUtilities = $pathUtilities;
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
     *
     * @return \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    public function getDataClassRepositoryCache()
    {
        return $this->dataClassRepositoryCache;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache $dataClassRepositoryCache
     */
    public function setDataClassRepositoryCache($dataClassRepositoryCache)
    {
        $this->dataClassRepositoryCache = $dataClassRepositoryCache;
    }

    /**
     *
     * @return string[]
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     *
     * @param string[] $keys
     * @return string
     */
    public function getSetting($keys)
    {
        $variables = $keys;
        $values = $this->getSettings();

        while (count($variables) > 0)
        {
            $key = array_shift($variables);

            if (! isset($values[$key]))
            {
                if ($this->isAvailable())
                {
                    return null;
                }
                else
                {
                    echo 'The requested variable is not available in an unconfigured environment (' .
                         implode(' > ', $keys) . ')';
                    exit();
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
     * @param string $value
     */
    public function setSetting($keys, $value)
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

    public function loadConfiguration()
    {
        if ($this->isAvailable() && $this->isConnectable())
        {
            $this->loadFromStorage();
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
    public function isAvailable()
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

    /**
     *
     * @return boolean
     */
    public function isConnectable()
    {
        $configuration = new \Doctrine\DBAL\Configuration();

        $dataSourceName = new DataSourceName(
            $this->getSetting(array('Chamilo\Configuration', 'database', 'driver')),
            $this->getSetting(array('Chamilo\Configuration', 'database', 'username')),
            $this->getSetting(array('Chamilo\Configuration', 'database', 'host')),
            $this->getSetting(array('Chamilo\Configuration', 'database', 'name')),
            $this->getSetting(array('Chamilo\Configuration', 'database', 'password')));

        $connectionParameters = array(
            'user' => $dataSourceName->get_username(),
            'password' => $dataSourceName->get_password(),
            'host' => $dataSourceName->get_host(),
            'driverClass' => $dataSourceName->get_driver(true));

        try
        {
            DriverManager::getConnection($connectionParameters, $configuration)->connect();
            return true;
        }
        catch (\Exception $exception)
        {
            return false;
        }
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

        if ($this->isAvailable())
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
     * @return Registration[]
     */
    public function getRegistrations()
    {
        return $this->registrations;
    }

    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Load a default configuration which should enable the installer to function properly without a completely
     * configured environment
     */
    public function loadDefault()
    {
        $settings = array();

        $this->setSetting(array('Chamilo\Core\Admin', 'show_administrator_data'), false);
        $this->setSetting(array('Chamilo\Core\Admin', 'whoisonlineaccess'), 2);
        $this->setSetting(array('Chamilo\Core\Admin', 'site_name'), 'Chamilo');
        $this->setSetting(array('Chamilo\Core\Admin', 'institution'), 'Chamilo');
        $this->setSetting(array('Chamilo\Core\Admin', 'institution_url'), 'http://www.chamilo.org');
        $this->setSetting(array('Chamilo\Core\Admin', 'platform_timezone'), 'Europe/Brussels');
        $this->setSetting(array('Chamilo\Core\Admin', 'theme'), 'Aqua');
        $this->setSetting(array('Chamilo\Core\Admin', 'html_editor'), 'Ckeditor');
        $this->setSetting(array('Chamilo\Core\Admin', 'allow_portal_functionality'), false);
        $this->setSetting(array('Chamilo\Core\Admin', 'enable_external_authentication'), false);
        $this->setSetting(array('Chamilo\Core\Admin', 'server_type'), 'production');
        $this->setSetting(array('Chamilo\Core\Admin', 'installation_blocked'), false);
        $this->setSetting(array('Chamilo\Core\Admin', 'platform_language'), 'en');
        $this->setSetting(array('Chamilo\Core\Admin', 'show_variable_in_translation'), false);
        $this->setSetting(array('Chamilo\Core\Admin', 'write_new_variables_to_translation_file'), false);
        $this->setSetting(array('Chamilo\Core\Admin', 'show_version_data'), false);
        $this->setSetting(array('Chamilo\Core\Admin', 'hide_dcda_markup'), true);
        $this->setSetting(array('Chamilo\Core\Admin', 'version'), '5.0');
        $this->setSetting(array('Chamilo\Core\Admin', 'maintenance_mode'), false);

        $this->setSetting(array('Chamilo\Core\Admin', 'administrator_email'), '');
        $this->setSetting(array('Chamilo\Core\Admin', 'administrator_website'), '');
        $this->setSetting(array('Chamilo\Core\Admin', 'administrator_surname'), '');
        $this->setSetting(array('Chamilo\Core\Admin', 'administrator_firstname'), '');

        $this->setSetting(array('Chamilo\Core\Menu', 'show_sitemap'), false);
        $this->setSetting(array('Chamilo\Core\Menu', 'menu_renderer'), 'Bar');
        $this->setSetting(array('Chamilo\Core\Menu', 'brand_image'), '');

        $this->setSetting(array('Chamilo\Core\Help', 'hide_empty_pages'), true);

        $this->setSetting(array('Chamilo\Core\User', 'allow_user_change_platform_language'), false);
        $this->setSetting(array('Chamilo\Core\User', 'allow_user_quick_change_platform_language'), false);

        $url_append = str_replace('/src/Core/Install/index.php', '', $_SERVER['PHP_SELF']);

        $this->setSetting(
            array('Chamilo\Configuration', 'general', 'root_web'),
            'http://' . $_SERVER['HTTP_HOST'] . $url_append . '/');
        $this->setSetting(array('Chamilo\Configuration', 'general', 'url_append'), $url_append);

        $this->setSetting(array('Chamilo\Configuration', 'general', 'hashing_algorithm'), 'sha1');
        $this->setSetting(array('Chamilo\Configuration', 'debug', 'show_errors'), false);
        $this->setSetting(array('Chamilo\Configuration', 'debug', 'enable_query_cache'), true);
        $this->setSetting(array('Chamilo\Configuration', 'debug', 'enable_query_file_cache'), false);
        $this->setSetting(array('Chamilo\Configuration', 'session', 'session_handler'), 'chamilo');

        $this->setSetting(
            array('Chamilo\Configuration', 'storage', 'archive'),
            $this->getPathUtilities()->getStoragePath('archive'));
        $this->setSetting(
            array('Chamilo\Configuration', 'storage', 'cache_path'),
            $this->getPathUtilities()->getStoragePath('cache'));
        $this->setSetting(
            array('Chamilo\Configuration', 'storage', 'garbage'),
            $this->getPathUtilities()->getStoragePath('garbage_path'));
        $this->setSetting(
            array('Chamilo\Configuration', 'storage', 'hotpotatoes_path'),
            $this->getPathUtilities()->getStoragePath('hotpotatoes'));
        $this->setSetting(
            array('Chamilo\Configuration', 'storage', 'logs_path'),
            $this->getPathUtilities()->getStoragePath('logs'));
        $this->setSetting(
            array('Chamilo\Configuration', 'storage', 'repository_path'),
            $this->getPathUtilities()->getStoragePath('repository'));
        $this->setSetting(
            array('Chamilo\Configuration', 'storage', 'scorm_path'),
            $this->getPathUtilities()->getStoragePath('scorm'));
        $this->setSetting(
            array('Chamilo\Configuration', 'storage', 'temp_path'),
            $this->getPathUtilities()->getStoragePath('temp'));
        $this->setSetting(
            array('Chamilo\Configuration', 'storage', 'userpictures'),
            $this->getPathUtilities()->getStoragePath('userpictures_path'));
    }

    /**
     * Trigger a reset of the entire configuration to force a reload from storage
     */
    public function reset()
    {
        $this->dataClassRepositoryCache->truncates(array(Registration::class_name(), Setting::class_name()));
        $this->getConfigurationCacheService()->clear();
        $this->loadFromStorage();
    }
}
