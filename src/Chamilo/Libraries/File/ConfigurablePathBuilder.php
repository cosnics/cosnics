<?php
namespace Chamilo\Libraries\File;

/**
 *
 * @package Chamilo\Libraries\File
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ConfigurablePathBuilder
{
    const ARCHIVE = 4;
    const CACHE = 2;
    const LOG = 3;
    const PROFILE_PICTURE = 6;
    const REPOSITORY = 5;
    const TEMPORARY = 1;

    /**
     *
     * @var string[]
     */
    protected $cache;

    /**
     *
     * @var string[]
     */
    private $configuredPaths;

    /**
     *
     * @var string
     */
    private $configuredArchivePath;

    /**
     *
     * @var string
     */
    private $configuredCachePath;

    /**
     *
     * @var string
     */
    private $configuredGarbagePath;

    /**
     *
     * @var string
     */
    private $configuredHotpotatoesPath;

    /**
     *
     * @var string
     */
    private $configuredLogsPath;

    /**
     *
     * @var string
     */
    private $configuredRepositoryPath;

    /**
     *
     * @var string
     */
    private $configuredScormPath;

    /**
     *
     * @var string
     */
    private $configuredTempPath;

    /**
     *
     * @var string
     */
    private $configuredUserPicturesPath;

    /**
     *
     * @param string[] $configuredPaths
     */
    public function __construct($configuredPaths)
    {
        $this->cache = [];
        $this->configuredPaths = $configuredPaths;
    }

    /**
     *
     * @return string
     */
    public function getArchivePath()
    {
        return $this->cache[self::ARCHIVE] = $this->getConfiguredArchivePath();
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
     * @param string $namespace
     *
     * @return string
     */
    public function getCachePath($namespace = null)
    {
        $completeNamespace = ($namespace ? 'cache\\' . $namespace : 'cache');

        return $this->cache[self::CACHE][(string) $completeNamespace] = $this->getConfiguredCachePath() . md5(
                $namespace
            ) . DIRECTORY_SEPARATOR;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredArchivePath()
    {
        if (!isset($this->configuredArchivePath))
        {
            $configuredPaths = $this->getConfiguredPaths();
            $this->configuredArchivePath = $configuredPaths['archive_path'];
        }

        return $this->configuredArchivePath;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredCachePath()
    {
        if (!isset($this->configuredCachePath))
        {
            $configuredPaths = $this->getConfiguredPaths();
            $this->configuredCachePath = $configuredPaths['cache_path'];
        }

        return $this->configuredCachePath;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredGarbagePath()
    {
        if (!isset($this->configuredGarbagePath))
        {
            $configuredPaths = $this->getConfiguredPaths();
            $this->configuredGarbagePath = $configuredPaths['garbage_path'];
        }

        return $this->configuredGarbagePath;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredHotpotatoesPath()
    {
        if (!isset($this->configuredHotpotatoesPath))
        {
            $configuredPaths = $this->getConfiguredPaths();
            $this->configuredHotpotatoesPath = $configuredPaths['hotpotatoes_path'];
        }

        return $this->configuredHotpotatoesPath;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredLogsPath()
    {
        if (!isset($this->configuredLogsPath))
        {
            $configuredPaths = $this->getConfiguredPaths();
            $this->configuredLogsPath = $configuredPaths['logs_path'];
        }

        return $this->configuredLogsPath;
    }

    /**
     *
     * @return string[]
     */
    public function getConfiguredPaths()
    {
        return $this->configuredPaths;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredRepositoryPath()
    {
        if (!isset($this->configuredRepositoryPath))
        {
            $configuredPaths = $this->getConfiguredPaths();
            $this->configuredRepositoryPath = $configuredPaths['repository_path'];
        }

        return $this->configuredRepositoryPath;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredScormPath()
    {
        if (!isset($this->configuredScormPath))
        {
            $configuredPaths = $this->getConfiguredPaths();
            $this->configuredScormPath = $configuredPaths['scorm_path'];
        }

        return $this->configuredScormPath;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredTempPath()
    {
        if (!isset($this->configuredTempPath))
        {
            $configuredPaths = $this->getConfiguredPaths();
            $this->configuredTempPath = $configuredPaths['temp_path'];
        }

        return $this->configuredTempPath;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredUserPicturesPath()
    {
        if (!isset($this->configuredUserPicturesPath))
        {
            $configuredPaths = $this->getConfiguredPaths();
            $this->configuredUserPicturesPath = $configuredPaths['userpictures_path'];
        }

        return $this->configuredUserPicturesPath;
    }

    /**
     *
     * @return string
     */
    public function getLogPath()
    {
        return $this->cache[self::LOG] = $this->getConfiguredLogsPath();
    }

    /**
     *
     * @return string
     */
    public function getProfilePicturePath()
    {
        return $this->cache[self::PROFILE_PICTURE] = $this->getConfiguredUserPicturesPath();
    }

    /**
     *
     * @return string
     */
    public function getRepositoryPath()
    {
        return $this->cache[self::REPOSITORY] = $this->getConfiguredRepositoryPath();
    }

    /**
     *
     * @param string $namespace
     *
     * @return string
     */
    public function getTemporaryPath($namespace = null)
    {
        $completeNamespace = ($namespace ? 'temp\\' . $namespace : 'temp');

        return $this->cache[self::TEMPORARY][(string) $completeNamespace] =
            $this->getConfiguredTempPath() . md5($namespace) . DIRECTORY_SEPARATOR;
    }
}
