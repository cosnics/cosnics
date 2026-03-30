<?php
namespace Chamilo\Libraries\Filesystem\Service;

use Chamilo\Libraries\Service\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Filesystem\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractPathBuilder
{
    public const int ARCHIVE = 8;
    public const int BASE = 2;
    public const int CACHE = 6;
    public const int CONFIGURATION = 12;
    public const int CONFIGURATION_STORAGE = 99;
    public const int CSS = 19;
    public const int FULL = 1;
    public const int IMAGES = 20;
    public const int JAVASCRIPT = 14;
    public const int LOG = 7;
    public const int PLUGIN = 11;
    public const int PROFILE_PICTURE = 10;
    public const int PUBLIC_STORAGE = 17;
    public const int RELATIVE = 3;
    public const int RESOURCE = 13;
    public const int ROOT = 21;
    public const int STORAGE = 4;
    public const int TEMPLATES = 18;
    public const int TEMPORARY = 5;
    public const int TRANSLATION = 15;
    public const int VENDOR = 16;

    /**
     * @var string[]
     */
    protected array $cache = [];

    /**
     * @var string[]
     */
    protected array $namespacePathMap = [];

    abstract public function getBasePath(): string;

    public function getConfigurationPath(string $namespace = 'Chamilo\Libraries'): string
    {
        return $this->cache[self::CONFIGURATION][$namespace] =
            $this->getResourcesPath($namespace) . 'Configuration' . $this->getDirectorySeparator();
    }

    public function getCssPath(string $namespace = 'Chamilo\Libraries'): string
    {
        return $this->cache[self::CSS][$namespace] =
            $this->getResourcesPath($namespace) . 'Css' . $this->getDirectorySeparator();
    }

    abstract public function getDirectorySeparator(): string;

    public function getImagesPath(string $namespace = StringUtilities::LIBRARIES): string
    {
        return $this->cache[self::IMAGES][$namespace] =
            $this->getResourcesPath($namespace) . 'Images' . $this->getDirectorySeparator();
    }

    public function getJavascriptPath(string $namespace = 'Chamilo\Libraries'): string
    {
        return $this->cache[self::JAVASCRIPT][$namespace] =
            $this->getResourcesPath($namespace) . 'Javascript' . $this->getDirectorySeparator();
    }

    public function getPluginPath(string $namespace = 'Chamilo\Libraries'): string
    {
        return $this->cache[self::PLUGIN][$namespace] =
            $this->getResourcesPath($namespace) . 'Plugin' . $this->getDirectorySeparator();
    }

    abstract protected function getPublicStorageBasePath(): string;

    public function getPublicStoragePath(string $namespace = null): string
    {
        return $this->cache[self::PUBLIC_STORAGE][(string) $namespace] = $this->getPublicStorageBasePath() .
            ($namespace ? $this->namespaceToPath($namespace) . $this->getDirectorySeparator() : '');
    }

    public function getResourcesPath(string $namespace = 'Chamilo\Libraries'): string
    {
        return $this->cache[self::RESOURCE][$namespace] =
            $this->namespaceToFullPath($namespace) . 'Resources' . $this->getDirectorySeparator();
    }

    public function getTemplatesPath(string $namespace = 'Chamilo\Libraries'): string
    {
        return $this->cache[self::TEMPLATES][$namespace] =
            $this->getResourcesPath($namespace) . 'Template' . $this->getDirectorySeparator();
    }

    public function getTranslationPath(string $namespace = 'Chamilo\Libraries'): string
    {
        return $this->cache[self::TRANSLATION][$namespace] =
            $this->getResourcesPath($namespace) . 'Translation' . $this->getDirectorySeparator();
    }

    public function namespaceToFullPath(?string $namespace = null): string
    {
        return $this->cache[self::FULL][(string) $namespace] = $this->getBasePath() .
            ($namespace ? $this->namespaceToPath($namespace) . $this->getDirectorySeparator() : '');
    }

    public function namespaceToPath(string $namespace): string
    {
        return $this->namespacePathMap[$namespace][$this->getDirectorySeparator()] = strtr(
            $namespace, '\\', $this->getDirectorySeparator()
        );
    }
}
