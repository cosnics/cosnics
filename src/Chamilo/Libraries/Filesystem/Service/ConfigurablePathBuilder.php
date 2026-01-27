<?php
namespace Chamilo\Libraries\Filesystem\Service;

/**
 * @package Chamilo\Libraries\File
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class ConfigurablePathBuilder
{
    public const ARCHIVE = 4;
    public const CACHE = 2;
    public const GARBAGE = 7;
    public const LOG = 3;
    public const PROFILE_PICTURE = 6;
    public const TEMPORARY = 1;
    public const USER_PICTURES = 8;

    /**
     * @var string[]
     */
    protected array $cache;

    private string $configuredArchivePath;

    private string $configuredCachePath;

    private string $configuredGarbagePath;

    private string $configuredLogsPath;

    /**
     * @var string[]
     */
    private array $configuredPaths;

    private string $configuredTempPath;

    private string $configuredUserPicturesPath;

    /**
     * @param string[] $configuredPaths
     */
    public function __construct(array $configuredPaths)
    {
        $this->cache = [];
        $this->configuredPaths = $configuredPaths;
    }

    public function getArchivePath(): string
    {
        return $this->cache[self::ARCHIVE] = $this->getConfiguredArchivePath();
    }

    /**
     * @return string[]
     */
    public function getCache(): array
    {
        return $this->cache;
    }

    /**
     * @param string[] $cache
     */
    public function setCache(array $cache): static
    {
        $this->cache = $cache;

        return $this;
    }

    public function getCachePath(?string $namespace = null): string
    {
        $completeNamespace = ($namespace ? 'cache\\' . $namespace : 'cache');

        return $this->cache[self::CACHE][$completeNamespace] = $this->getConfiguredCachePath() . md5(
                $namespace
            ) . DIRECTORY_SEPARATOR;
    }

    public function getConfiguredArchivePath(): string
    {
        if (!isset($this->configuredArchivePath))
        {
            $configuredPaths = $this->getConfiguredPaths();
            $this->configuredArchivePath = $configuredPaths['archive_path'];
        }

        return $this->configuredArchivePath;
    }

    public function getConfiguredCachePath(): string
    {
        if (!isset($this->configuredCachePath))
        {
            $configuredPaths = $this->getConfiguredPaths();
            $this->configuredCachePath = $configuredPaths['cache_path'];
        }

        return $this->configuredCachePath;
    }

    public function getConfiguredGarbagePath(): string
    {
        if (!isset($this->configuredGarbagePath))
        {
            $configuredPaths = $this->getConfiguredPaths();
            $this->configuredGarbagePath = $configuredPaths['garbage_path'];
        }

        return $this->configuredGarbagePath;
    }

    public function getConfiguredLogsPath(): string
    {
        if (!isset($this->configuredLogsPath))
        {
            $configuredPaths = $this->getConfiguredPaths();
            $this->configuredLogsPath = $configuredPaths['logs_path'];
        }

        return $this->configuredLogsPath;
    }

    /**
     * @return string[]
     */
    public function getConfiguredPaths(): array
    {
        return $this->configuredPaths;
    }

    public function getConfiguredTempPath(): string
    {
        if (!isset($this->configuredTempPath))
        {
            $configuredPaths = $this->getConfiguredPaths();
            $this->configuredTempPath = $configuredPaths['temp_path'];
        }

        return $this->configuredTempPath;
    }

    public function getConfiguredUserPicturesPath(): string
    {
        if (!isset($this->configuredUserPicturesPath))
        {
            $configuredPaths = $this->getConfiguredPaths();
            $this->configuredUserPicturesPath = $configuredPaths['userpictures_path'];
        }

        return $this->configuredUserPicturesPath;
    }

    public function getGarbagePath(): string
    {
        return $this->cache[self::GARBAGE] = $this->getConfiguredGarbagePath();
    }

    public function getLogPath(): string
    {
        return $this->cache[self::LOG] = $this->getConfiguredLogsPath();
    }

    public function getProfilePicturePath(): string
    {
        return $this->cache[self::PROFILE_PICTURE] = $this->getConfiguredUserPicturesPath();
    }

    public function getTemporaryPath(?string $namespace = null): string
    {
        $completeNamespace = ($namespace ? 'temp\\' . $namespace : 'temp');

        return $this->cache[self::TEMPORARY][$completeNamespace] =
            $this->getConfiguredTempPath() . md5($namespace) . DIRECTORY_SEPARATOR;
    }

    public function getUserPicturesPath(): string
    {
        return $this->cache[self::USER_PICTURES] = $this->getConfiguredUserPicturesPath();
    }
}
