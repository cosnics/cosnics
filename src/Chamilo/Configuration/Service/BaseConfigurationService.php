<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Libraries\File\Path;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class BaseConfigurationService
{

    /**
     *
     * @var \Chamilo\Libraries\File\Path
     */
    private $pathUtilities;

    /**
     *
     * @var string[]
     */
    private $settings;

    /**
     *
     * @var boolean
     */
    private $isAvailable;

    /**
     *
     * @param \Chamilo\Libraries\File\Path $pathUtilities
     */
    public function __construct(Path $pathUtilities)
    {
        $this->pathUtilities = $pathUtilities;
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
     * @throws \Exception
     * @return boolean
     */
    public function isAvailable()
    {
        if (! isset($this->isAvailable))
        {
            $file = $this->getConfigurationFilePath();

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
     * @return string[]
     */
    protected function getSettingsFromFile()
    {
        return array('Chamilo\Configuration' => parse_ini_file($this->getConfigurationFilePath(), true));
    }

    /**
     *
     * @return string
     */
    protected function getConfigurationFilePath()
    {
        return $this->getPathUtilities()->getStoragePath() . 'configuration' . DIRECTORY_SEPARATOR . 'configuration.ini';
    }

    protected function setDefaultSettings()
    {
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
     *
     * @return string[]
     */
    public function getSettings()
    {
        if (! isset($this->settings))
        {
            if ($this->isAvailable())
            {
                $this->settings = $this->getSettingsFromFile();
            }
            else
            {
                $this->setDefaultSettings();
            }
        }

        return $this->settings;
    }

    /**
     * Gets a parameter from the configuration.
     *
     * @param string[] $keys
     * @throws \Exception
     * @return string
     */
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
    protected function setSetting($keys, $value)
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
}
