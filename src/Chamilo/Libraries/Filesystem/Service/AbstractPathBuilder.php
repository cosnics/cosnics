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
    public const int ARCHIVE_PATH = 8;
    public const int BASE_PATH = 2;
    public const int CACHE_PATH = 6;
    public const int CONFIGURATION_PATH = 12;
    public const int CONFIGURATION_STORAGE_PATH = 99;
    public const int CSS_PATH = 19;
    public const int FULL_PATH = 1;
    public const int IMAGES_PATH = 20;
    public const int JAVASCRIPT_PATH = 14;
    public const int LOG_PATH = 7;
    public const int PLUGIN_PATH = 11;
    public const int PROFILE_PICTURE_PATH = 10;
    public const int PUBLIC_STORAGE_PATH = 17;
    public const int RELATIVE_PATH = 3;
    public const int RESOURCE_PATH = 13;
    public const int ROOT_PATH = 21;
    public const int STORAGE_PATH = 4;
    public const int TEMPLATES_PATH = 18;
    public const int TEMPORARY_PATH = 5;
    public const int TRANSLATION_PATH = 15;
    public const int VENDOR_PATH = 16;

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
        return $this->cache[self::CONFIGURATION_PATH][$namespace] =
            $this->getResourcesPath($namespace) . 'Configuration' . $this->getDirectorySeparator();
    }

    public function getCssPath(string $namespace = 'Chamilo\Libraries'): string
    {
        return $this->cache[self::CSS_PATH][$namespace] =
            $this->getResourcesPath($namespace) . 'Css' . $this->getDirectorySeparator();
    }

    abstract public function getDirectorySeparator(): string;

    public function getImagesPath(string $namespace = StringUtilities::LIBRARIES): string
    {
        return $this->cache[self::IMAGES_PATH][$namespace] =
            $this->getResourcesPath($namespace) . 'Images' . $this->getDirectorySeparator();
    }

    public function getJavascriptPath(string $namespace = 'Chamilo\Libraries'): string
    {
        return $this->cache[self::JAVASCRIPT_PATH][$namespace] =
            $this->getResourcesPath($namespace) . 'Javascript' . $this->getDirectorySeparator();
    }

    public function getPluginPath(string $namespace = 'Chamilo\Libraries'): string
    {
        return $this->cache[self::PLUGIN_PATH][$namespace] =
            $this->getResourcesPath($namespace) . 'Plugin' . $this->getDirectorySeparator();
    }

    abstract protected function getPublicStorageBasePath(): string;

    public function getPublicStoragePath(string $namespace = null): string
    {
        return $this->cache[self::PUBLIC_STORAGE_PATH][(string) $namespace] = $this->getPublicStorageBasePath() .
            ($namespace ? $this->namespaceToPath($namespace) . $this->getDirectorySeparator() : '');
    }

    public function getResourcesPath(string $namespace = 'Chamilo\Libraries'): string
    {
        return $this->cache[self::RESOURCE_PATH][$namespace] =
            $this->namespaceToFullPath($namespace) . 'Resources' . $this->getDirectorySeparator();
    }

    public function getTemplatesPath(string $namespace = 'Chamilo\Libraries'): string
    {
        return $this->cache[self::TEMPLATES_PATH][$namespace] =
            $this->getResourcesPath($namespace) . 'Template' . $this->getDirectorySeparator();
    }

    public function getTranslationPath(string $namespace = 'Chamilo\Libraries'): string
    {
        return $this->cache[self::TRANSLATION_PATH][$namespace] =
            $this->getResourcesPath($namespace) . 'Translation' . $this->getDirectorySeparator();
    }

    public function namespaceToFullPath(?string $namespace = null): string
    {
        return $this->cache[self::FULL_PATH][(string) $namespace] = $this->getBasePath() .
            ($namespace ? $this->namespaceToPath($namespace) . $this->getDirectorySeparator() : '');
    }

    public function namespaceToPath(string $namespace): string
    {
        return $this->namespacePathMap[$namespace][$this->getDirectorySeparator()] = strtr(
            $namespace, '\\', $this->getDirectorySeparator()
        );
    }
}
