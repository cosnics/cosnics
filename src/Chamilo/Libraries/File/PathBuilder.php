<?php
namespace Chamilo\Libraries\File;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * @package Chamilo\Libraries\File
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PathBuilder
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

    protected static ?PathBuilder $instance = null;

    /**
     * @var string[]
     */
    private array $cache = [];

    private ClassnameUtilities $classnameUtilities;

    private ChamiloRequest $request;

    public function __construct(ClassnameUtilities $classnameUtilities, ChamiloRequest $request)
    {
        $this->classnameUtilities = $classnameUtilities;
        $this->request = $request;
    }

    public function getBasePath(bool $web = false): string
    {
        if (!isset($this->cache[self::BASE][(string) $web]))
        {
            if ($web)
            {
                $request = $this->getRequest();
                $this->cache[self::BASE][(string) $web] =
                    $request->getSchemeAndHttpHost() . $request->getBasePath() . $request->getPathInfo();
            }
            else
            {
                $this->cache[self::BASE][(string) $web] = realpath(__DIR__ . '/../../../') . DIRECTORY_SEPARATOR;
            }
        }

        return $this->cache[self::BASE][(string) $web];
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    public function getConfigurationPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $this->cache[self::CONFIGURATION][$namespace][(string) $web] =
            $this->getResourcesPath($namespace, $web) . 'Configuration' . $this->getDirectorySeparator($web);
    }

    public function getCssPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $this->cache[self::CSS][$namespace][(string) $web] =
            $this->getResourcesPath($namespace, $web) . 'Css' . $this->getDirectorySeparator($web);
    }

    public function getDirectorySeparator(bool $web = true): string
    {
        return ($web ? '/' : DIRECTORY_SEPARATOR);
    }

    public function getI18nPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $this->cache[self::I18N][$namespace][(string) $web] =
            $this->getResourcesPath($namespace, $web) . 'I18n' . $this->getDirectorySeparator($web);
    }

    public function getImagesPath(string $namespace = StringUtilities::LIBRARIES, bool $web = false): string
    {
        return $this->cache[self::IMAGES][$namespace][(string) $web] =
            $this->getResourcesPath($namespace, $web) . 'Images' . $this->getDirectorySeparator($web);
    }

    public static function getInstance(): ?PathBuilder
    {
        if (is_null(static::$instance))
        {
            self::$instance = new static(ClassnameUtilities::getInstance(), ChamiloRequest::createFromGlobals());
        }

        return static::$instance;
    }

    public function getJavascriptPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $this->cache[self::JAVASCRIPT][$namespace][(string) $web] =
            $this->getResourcesPath($namespace, $web) . 'Javascript' . $this->getDirectorySeparator($web);
    }

    public function getPluginPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $this->cache[self::PLUGIN][$namespace][(string) $web] =
            $this->getResourcesPath($namespace, $web) . 'Plugin' . $this->getDirectorySeparator($web);
    }

    public function getPublicStoragePath(string $namespace = null, bool $web = false): string
    {
        if ($web)
        {
            $basePath = $this->getBasePath($web) . 'Files';
        }
        else
        {
            $basePath = realpath(
                $this->getBasePath($web) . '..' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'Files'
            );
        }

        return $this->cache[self::PUBLIC_STORAGE][(string) $namespace][(string) $web] =
            $basePath . $this->getDirectorySeparator($web) . ($namespace ?
                $this->getClassnameUtilities()->namespaceToPath($namespace, $web) . $this->getDirectorySeparator($web) :
                '');
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getResourcesPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $this->cache[self::RESOURCE][$namespace][(string) $web] =
            $this->namespaceToFullPath($namespace, $web) . 'Resources' . $this->getDirectorySeparator($web);
    }

    public function getStoragePath(?string $namespace = null): string
    {
        $basePath = realpath($this->getBasePath() . '../files/');

        return $this->cache[self::STORAGE][(string) $namespace] = $basePath . DIRECTORY_SEPARATOR .
            ($namespace ? $this->getClassnameUtilities()->namespaceToPath($namespace) . DIRECTORY_SEPARATOR : '');
    }

    public function getTemplatesPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $this->cache[self::TEMPLATES][$namespace][(string) $web] =
            $this->getResourcesPath($namespace, $web) . 'Templates' . $this->getDirectorySeparator($web);
    }

    /**
     * @throws \Exception
     */
    public function getVendorPath(bool $web = false): string
    {
        if ($web)
        {
            throw new Exception('Storage is not directly accessible');
        }

        return $this->cache[self::VENDOR][(string) $web] =
            realpath($this->getBasePath($web) . '../vendor/') . $this->getDirectorySeparator($web);
    }

    public function isWebUri(string $uri): bool
    {
        return ((stripos($uri, 'http://') === 0) || (stripos($uri, 'https://') === 0) ||
            (stripos($uri, 'ftp://') === 0));
    }

    public function namespaceToFullPath(?string $namespace = null, bool $web = false): string
    {
        return $this->cache[self::FULL][(string) $namespace][(string) $web] = $this->getBasePath($web) . ($namespace ?
                $this->getClassnameUtilities()->namespaceToPath($namespace, $web) . $this->getDirectorySeparator($web) :
                '');
    }

    public function setClassnameUtilities(ClassnameUtilities $classnameUtilities)
    {
        $this->classnameUtilities = $classnameUtilities;
    }
}
