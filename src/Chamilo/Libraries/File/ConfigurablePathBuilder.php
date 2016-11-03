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
     * @param string $configuredArchivePath
     * @param string $configuredCachePath
     * @param string $configuredGarbagePath
     * @param string $configuredHotpotatoesPath
     * @param string $configuredLogsPath
     * @param string $configuredRepositoryPath
     * @param string $configuredScormPath
     * @param string $configuredTempPath
     * @param string $configuredUserPicturesPath
     */
    public function __construct($configuredArchivePath, $configuredCachePath, $configuredGarbagePath,
        $configuredHotpotatoesPath, $configuredLogsPath, $configuredRepositoryPath, $configuredScormPath,
        $configuredTempPath, $configuredUserPicturesPath)
    {
        $this->cache = array();

        $this->configuredArchivePath = $configuredArchivePath;
        $this->configuredCachePath = $configuredCachePath;
        $this->configuredGarbagePath = $configuredGarbagePath;
        $this->configuredHotpotatoesPath = $configuredHotpotatoesPath;
        $this->configuredLogsPath = $configuredLogsPath;
        $this->configuredRepositoryPath = $configuredRepositoryPath;
        $this->configuredScormPath = $configuredScormPath;
        $this->configuredTempPath = $configuredTempPath;
        $this->configuredUserPicturesPath = $configuredUserPicturesPath;
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
     * @return string
     */
    public function getConfiguredArchivePath()
    {
        return $this->configuredArchivePath;
    }

    /**
     *
     * @param string $configuredArchivePath
     */
    public function setConfiguredArchivePath($configuredArchivePath)
    {
        $this->configuredArchivePath = $configuredArchivePath;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredCachePath()
    {
        return $this->configuredCachePath;
    }

    /**
     *
     * @param string $configuredCachePath
     */
    public function setConfiguredCachePath($configuredCachePath)
    {
        $this->configuredCachePath = $configuredCachePath;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredGarbagePath()
    {
        return $this->configuredGarbagePath;
    }

    /**
     *
     * @param string $configuredGarbagePath
     */
    public function setConfiguredGarbagePath($configuredGarbagePath)
    {
        $this->configuredGarbagePath = $configuredGarbagePath;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredHotpotatoesPath()
    {
        return $this->configuredHotpotatoesPath;
    }

    /**
     *
     * @param string $configuredHotpotatoesPath
     */
    public function setConfiguredHotpotatoesPath($configuredHotpotatoesPath)
    {
        $this->configuredHotpotatoesPath = $configuredHotpotatoesPath;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredLogsPath()
    {
        return $this->configuredLogsPath;
    }

    /**
     *
     * @param string $configuredLogsPath
     */
    public function setConfiguredLogsPath($configuredLogsPath)
    {
        $this->configuredLogsPath = $configuredLogsPath;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredRepositoryPath()
    {
        return $this->configuredRepositoryPath;
    }

    /**
     *
     * @param string $configuredRepositoryPath
     */
    public function setConfiguredRepositoryPath($configuredRepositoryPath)
    {
        $this->configuredRepositoryPath = $configuredRepositoryPath;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredScormPath()
    {
        return $this->configuredScormPath;
    }

    /**
     *
     * @param string $configuredScormPath
     */
    public function setConfiguredScormPath($configuredScormPath)
    {
        $this->configuredScormPath = $configuredScormPath;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredTempPath()
    {
        return $this->configuredTempPath;
    }

    /**
     *
     * @param string $configuredTempPath
     */
    public function setConfiguredTempPath($configuredTempPath)
    {
        $this->configuredTempPath = $configuredTempPath;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredUserPicturesPath()
    {
        return $this->configuredUserPicturesPath;
    }

    /**
     *
     * @param string $configuredUserPicturesPath
     */
    public function setConfiguredUserPicturesPath($configuredUserPicturesPath)
    {
        $this->configuredUserPicturesPath = $configuredUserPicturesPath;
    }

    /**
     *
     * @param string $namespace
     * @return string
     */
    public function getTemporaryPath($namespace = null)
    {
        $completeNamespace = ($namespace ? 'temp\\' . $namespace : 'temp');
        return $this->cache[self::TEMPORARY][(string) $completeNamespace] = $this->getConfiguredTempPath() .
             md5($namespace) . DIRECTORY_SEPARATOR;
    }

    /**
     *
     * @param string $namespace
     * @return string
     */
    public function getCachePath($namespace = null)
    {
        $completeNamespace = ($namespace ? 'cache\\' . $namespace : 'cache');
        return $this->cache[self::CACHE][(string) $completeNamespace] = $this->getConfiguredCachePath() . md5(
            $namespace) . DIRECTORY_SEPARATOR;
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
    public function getArchivePath()
    {
        return $this->cache[self::ARCHIVE] = $this->getConfiguredArchivePath();
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
     * @return string
     */
    public function getProfilePicturePath()
    {
        return $this->cache[self::PROFILE_PICTURE] = $this->getConfiguredUserPicturesPath();
    }
}
