<?php
namespace Chamilo\Libraries\File;

use Chamilo\Configuration\Service\ConfigurationConsulter;

/**
 *
 * @package Chamilo\Libraries\File
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ConfigurablePathUtilities
{
    const TEMPORARY = 1;
    const CACHE = 2;
    const LOG = 3;
    const ARCHIVE = 4;
    const REPOSITORY = 5;
    const PROFILE_PICTURE = 6;

    /**
     *
     * @var string[]
     */
    protected $cache;

    /**
     *
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(ConfigurationConsulter $configurationConsulter)
    {
        $this->cache = array();
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     *
     * @return string[]
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     *
     * @param string[] $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getConfigurationConsulter()
    {
        return $this->configurationConsulter;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function getTemporaryPath($namespace = null)
    {
        $completeNamespace = ($namespace ? 'temp\\' . $namespace : 'temp');
        return $this->cache[self::TEMPORARY][(string) $completeNamespace] = $this->getConfigurationConsulter()->getSetting(
            array('Chamilo\Configuration', 'storage', 'temp_path')) . md5($namespace) . DIRECTORY_SEPARATOR;
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function getCachePath($namespace = null)
    {
        $completeNamespace = ($namespace ? 'cache\\' . $namespace : 'cache');
        return $this->cache[self::CACHE][(string) $completeNamespace] = $this->getConfigurationConsulter()->getSetting(
            array('Chamilo\Configuration', 'storage', 'cache_path')) . md5($namespace) . DIRECTORY_SEPARATOR;
    }

    /**
     *
     * @param boolean $web
     * @return string
     */
    public function getLogPath()
    {
        return $this->cache[self::LOG] = $this->getConfigurationConsulter()->getSetting(
            array('Chamilo\Configuration', 'storage', 'logs_path'));
    }

    /**
     *
     * @return string
     */
    public function getArchivePath()
    {
        return $this->cache[self::ARCHIVE] = $this->getConfigurationConsulter()->getSetting(
            array('Chamilo\Configuration', 'storage', 'archive_path'));
    }

    /**
     *
     * @param boolean $web
     * @return string
     */
    public function getRepositoryPath()
    {
        return $this->cache[self::REPOSITORY] = $this->getConfigurationConsulter()->getSetting(
            array('Chamilo\Configuration', 'storage', 'repository_path'));
    }

    /**
     *
     * @param boolean $web
     * @return string
     */
    public function getProfilePicturePath()
    {
        return $this->cache[self::PROFILE_PICTURE] = $this->getConfigurationConsulter()->getSetting(
            array('Chamilo\Configuration', 'storage', 'userpictures_path'));
    }
}
