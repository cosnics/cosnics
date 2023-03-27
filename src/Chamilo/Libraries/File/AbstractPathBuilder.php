<?php
namespace Chamilo\Libraries\File;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\File
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractPathBuilder
{
    public const ARCHIVE = 8;
    public const BASE = 2;
    public const CACHE = 6;
    public const CONFIGURATION = 12;
    public const CSS = 19;
    public const FULL = 1;
    public const I18N = 15;
    public const IMAGES = 20;
    public const JAVASCRIPT = 14;
    public const LOG = 7;
    public const PLUGIN = 11;
    public const PROFILE_PICTURE = 10;
    public const PUBLIC_STORAGE = 17;
    public const RELATIVE = 3;
    public const REPOSITORY = 9;
    public const RESOURCE = 13;
    public const STORAGE = 4;
    public const TEMPLATES = 18;
    public const TEMPORARY = 5;
    public const VENDOR = 16;

    /**
     * @var string[]
     */
    protected array $cache = [];

    protected ClassnameUtilities $classnameUtilities;

    public function __construct(ClassnameUtilities $classnameUtilities)
    {
        $this->classnameUtilities = $classnameUtilities;
    }

    abstract public function getBasePath(): string;

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    public function getConfigurationPath(string $namespace = 'Chamilo\Configuration'): string
    {
        return $this->cache[self::CONFIGURATION][$namespace] =
            $this->getResourcesPath($namespace) . 'Configuration' . $this->getDirectorySeparator();
    }

    public function getCssPath(string $namespace = 'Chamilo\Configuration'): string
    {
        return $this->cache[self::CSS][$namespace] =
            $this->getResourcesPath($namespace) . 'Css' . $this->getDirectorySeparator();
    }

    abstract public function getDirectorySeparator(): string;

    public function getI18nPath(string $namespace = 'Chamilo\Configuration'): string
    {
        return $this->cache[self::I18N][$namespace] =
            $this->getResourcesPath($namespace) . 'I18n' . $this->getDirectorySeparator();
    }

    public function getImagesPath(string $namespace = StringUtilities::LIBRARIES): string
    {
        return $this->cache[self::IMAGES][$namespace] =
            $this->getResourcesPath($namespace) . 'Images' . $this->getDirectorySeparator();
    }

    public function getJavascriptPath(string $namespace = 'Chamilo\Configuration'): string
    {
        return $this->cache[self::JAVASCRIPT][$namespace] =
            $this->getResourcesPath($namespace) . 'Javascript' . $this->getDirectorySeparator();
    }

    public function getPluginPath(string $namespace = 'Chamilo\Configuration'): string
    {
        return $this->cache[self::PLUGIN][$namespace] =
            $this->getResourcesPath($namespace) . 'Plugin' . $this->getDirectorySeparator();
    }

    abstract protected function getPublicStorageBasePath(): string;

    public function getPublicStoragePath(string $namespace = null): string
    {
        return $this->cache[self::PUBLIC_STORAGE][(string) $namespace] =
            $this->getPublicStorageBasePath() . $this->getDirectorySeparator() .
            ($namespace ? $this->getClassnameUtilities()->namespaceToPath($namespace) . $this->getDirectorySeparator() :
                '');
    }

    public function getResourcesPath(string $namespace = 'Chamilo\Configuration'): string
    {
        return $this->cache[self::RESOURCE][$namespace] =
            $this->namespaceToFullPath($namespace) . 'Resources' . $this->getDirectorySeparator();
    }

    public function getStoragePath(?string $namespace = null): string
    {
        $basePath = realpath($this->getBasePath() . '../files/');

        return $this->cache[self::STORAGE][(string) $namespace] = $basePath . DIRECTORY_SEPARATOR .
            ($namespace ? $this->getClassnameUtilities()->namespaceToPath($namespace) . DIRECTORY_SEPARATOR : '');
    }

    public function getTemplatesPath(string $namespace = 'Chamilo\Configuration'): string
    {
        return $this->cache[self::TEMPLATES][$namespace] =
            $this->getResourcesPath($namespace) . 'Templates' . $this->getDirectorySeparator();
    }

    public function namespaceToFullPath(?string $namespace = null): string
    {
        return $this->cache[self::FULL][(string) $namespace] = $this->getBasePath() .
            ($namespace ? $this->getClassnameUtilities()->namespaceToPath($namespace) . $this->getDirectorySeparator() :
                '');
    }
}
