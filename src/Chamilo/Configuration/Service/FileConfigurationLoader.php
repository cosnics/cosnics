<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Libraries\File\Path;
use Chamilo\Configuration\Interfaces\DataLoaderInterface;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class FileConfigurationLoader implements DataLoaderInterface
{

    /**
     *
     * @var \Chamilo\Libraries\File\Path
     */
    private $pathUtilities;

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
        $file = $this->getConfigurationFilePath();

        if (is_file($file) && is_readable($file))
        {
            return true;
        }
        else
        {
            return false;
        }
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

    protected function getDefaultSettings()
    {
        $settings = array();

        $settings['Chamilo\Core\Admin']['show_administrator_data'] = false;
        $settings['Chamilo\Core\Admin']['whoisonlineaccess'] = 2;
        $settings['Chamilo\Core\Admin']['site_name'] = 'Chamilo';
        $settings['Chamilo\Core\Admin']['institution'] = 'Chamilo';
        $settings['Chamilo\Core\Admin']['institution_url'] = 'http://www.chamilo.org';
        $settings['Chamilo\Core\Admin']['platform_timezone'] = 'Europe/Brussels';
        $settings['Chamilo\Core\Admin']['theme'] = 'Aqua';
        $settings['Chamilo\Core\Admin']['html_editor'] = 'Ckeditor';
        $settings['Chamilo\Core\Admin']['allow_portal_functionality'] = false;
        $settings['Chamilo\Core\Admin']['enable_external_authentication'] = false;
        $settings['Chamilo\Core\Admin']['server_type'] = 'production';
        $settings['Chamilo\Core\Admin']['installation_blocked'] = false;
        $settings['Chamilo\Core\Admin']['platform_language'] = 'en';
        $settings['Chamilo\Core\Admin']['show_variable_in_translation'] = false;
        $settings['Chamilo\Core\Admin']['write_new_variables_to_translation_file'] = false;
        $settings['Chamilo\Core\Admin']['show_version_data'] = false;
        $settings['Chamilo\Core\Admin']['hide_dcda_markup'] = true;
        $settings['Chamilo\Core\Admin']['version'] = '5.0';
        $settings['Chamilo\Core\Admin']['maintenance_mode'] = false;

        $settings['Chamilo\Core\Admin']['administrator_email'] = '';
        $settings['Chamilo\Core\Admin']['administrator_website'] = '';
        $settings['Chamilo\Core\Admin']['administrator_surname'] = '';
        $settings['Chamilo\Core\Admin']['administrator_firstname'] = '';

        $settings['Chamilo\Core\Menu']['show_sitemap'] = false;
        $settings['Chamilo\Core\Menu']['menu_renderer'] = 'Bar';
        $settings['Chamilo\Core\Menu']['brand_image'] = '';

        $settings['Chamilo\Core\Help']['hide_empty_pages'] = true;

        $settings['Chamilo\Core\User']['allow_user_change_platform_language'] = false;
        $settings['Chamilo\Core\User']['allow_user_quick_change_platform_language'] = false;

        $url_append = str_replace('/src/Core/Install/index.php', '', $_SERVER['PHP_SELF']);

        $settings['Chamilo\Configuration']['general']['root_web'] = 'http://' . $_SERVER['HTTP_HOST'] . $url_append . '/';
        $settings['Chamilo\Configuration']['general']['url_append'] = $url_append;

        $settings['Chamilo\Configuration']['general']['hashing_algorithm'] = 'sha1';
        $settings['Chamilo\Configuration']['debug']['show_errors'] = false;
        $settings['Chamilo\Configuration']['debug']['enable_query_cache'] = true;
        $settings['Chamilo\Configuration']['debug']['enable_query_file_cache'] = false;
        $settings['Chamilo\Configuration']['session']['session_handler'] = 'chamilo';

        $pathUtilities = $this->getPathUtilities();

        $settings['Chamilo\Configuration']['storage']['archive'] = $pathUtilities->getStoragePath('archive');
        $settings['Chamilo\Configuration']['storage']['cache_path'] = $pathUtilities->getStoragePath('cache');
        $settings['Chamilo\Configuration']['storage']['garbage'] = $pathUtilities->getStoragePath('garbage_path');
        $settings['Chamilo\Configuration']['storage']['hotpotatoes_path'] = $pathUtilities->getStoragePath(
            'hotpotatoes');
        $settings['Chamilo\Configuration']['storage']['logs_path'] = $pathUtilities->getStoragePath('logs');
        $settings['Chamilo\Configuration']['storage']['repository_path'] = $pathUtilities->getStoragePath('repository');
        $settings['Chamilo\Configuration']['storage']['scorm_path'] = $pathUtilities->getStoragePath('scorm');
        $settings['Chamilo\Configuration']['storage']['temp_path'] = $pathUtilities->getStoragePath('temp');
        $settings['Chamilo\Configuration']['storage']['userpictures'] = $pathUtilities->getStoragePath(
            'userpictures_path');

        return $settings;
    }

    /**
     *
     * @return string[]
     */
    public function getData()
    {
        if ($this->isAvailable())
        {
            $settings = $this->getSettingsFromFile();
        }
        else
        {
            $settings = $this->getDefaultSettings();
        }

        return $settings;
    }
}
