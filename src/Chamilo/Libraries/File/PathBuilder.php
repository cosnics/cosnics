<?php
namespace Chamilo\Libraries\File;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Exception;

/**
 *
 * @package Chamilo\Libraries\File
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PathBuilder
{
    const ARCHIVE = 8;
    const BASE = 2;
    const CACHE = 6;
    const CONFIGURATION = 12;
    const CSS = 19;
    const FULL = 1;
    const I18N = 15;
    const IMAGES = 20;
    const JAVASCRIPT = 14;
    const LOG = 7;
    const PLUGIN = 11;
    const PROFILE_PICTURE = 10;
    const PUBLIC_STORAGE = 17;
    const RELATIVE = 3;
    const REPOSITORY = 9;
    const RESOURCE = 13;
    const STORAGE = 4;
    const TEMPLATES = 18;
    const TEMPORARY = 5;
    const VENDOR = 16;

    /**
     *
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    protected static $instance = null;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    private $classnameUtilities;

    /**
     *
     * @var string[]
     */
    private $cache = array();

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function __construct(ClassnameUtilities $classnameUtilities)
    {
        $this->classnameUtilities = $classnameUtilities;
    }

    /**
     *
     * @param boolean $web
     *
     * @return string
     */
    public function getBasePath($web = false)
    {
        if ($web)
        {
            $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] . 'x'));

            if ($dir !== '/')
            {
                $dir .= '/';
            }

            $protocol = (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://';
            $path = $protocol . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $dir;
        }
        else
        {
            $path = realpath(__DIR__ . '/../../../') . DIRECTORY_SEPARATOR;
        }

        return $this->cache[self::BASE][(string) $web] = $path;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    public function getClassnameUtilities()
    {
        return $this->classnameUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function setClassnameUtilities(ClassnameUtilities $classnameUtilities)
    {
        $this->classnameUtilities = $classnameUtilities;
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     *
     * @return string
     */
    public function getConfigurationPath($namespace = 'Chamilo\Configuration', $web = false)
    {
        return $this->cache[self::CONFIGURATION][(string) $namespace][(string) $web] =
            $this->getResourcesPath($namespace, $web) . 'Configuration' . $this->getDirectorySeparator($web);
    }

    /**
     * @param string $namespace
     * @param false $web
     *
     * @return string
     */
    public function getCssPath($namespace = 'Chamilo\Configuration', $web = false)
    {
        return $this->cache[self::CSS][(string) $namespace][(string) $web] =
            $this->getResourcesPath($namespace, $web) . 'Css' . $this->getDirectorySeparator($web);
    }

    /**
     * @param boolean $web
     *
     * @return string
     */
    public function getDirectorySeparator(bool $web = true)
    {
        return ($web ? '/' : DIRECTORY_SEPARATOR);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     *
     * @return string
     */
    public function getI18nPath($namespace = 'Chamilo\Configuration', $web = false)
    {
        return $this->cache[self::I18N][(string) $namespace][(string) $web] =
            $this->getResourcesPath($namespace, $web) . 'I18n' . $this->getDirectorySeparator($web);
    }

    /**
     * @param string $namespace
     * @param false $web
     *
     * @return string
     */
    public function getImagesPath($namespace = 'Chamilo\Configuration', $web = false)
    {
        return $this->cache[self::IMAGES][(string) $namespace][(string) $web] =
            $this->getResourcesPath($namespace, $web) . 'Images' . $this->getDirectorySeparator($web);
    }

    /**
     * Return 'this' as singleton
     *
     * @return \Chamilo\Libraries\File\PathBuilder
     */
    static public function getInstance()
    {
        if (is_null(static::$instance))
        {
            self::$instance = new static(ClassnameUtilities::getInstance());
        }

        return static::$instance;
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     *
     * @return string
     */
    public function getJavascriptPath($namespace = 'Chamilo\Configuration', $web = false)
    {
        return $this->cache[self::JAVASCRIPT][(string) $namespace][(string) $web] =
            $this->getResourcesPath($namespace, $web) . 'Javascript' . $this->getDirectorySeparator($web);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     *
     * @return string
     */
    public function getPluginPath($namespace = 'Chamilo\Configuration', $web = false)
    {
        return $this->cache[self::PLUGIN][(string) $namespace][(string) $web] =
            $this->getResourcesPath($namespace, $web) . 'Plugin' . $this->getDirectorySeparator($web);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     *
     * @return string
     */
    public function getPublicStoragePath($namespace = null, $web = false)
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

    /**
     *
     * @param string $namespace
     * @param boolean $web
     *
     * @return string
     */
    public function getResourcesPath($namespace = 'Chamilo\Configuration', $web = false)
    {
        return $this->cache[self::RESOURCE][(string) $namespace][(string) $web] =
            $this->namespaceToFullPath($namespace, $web) . 'Resources' . $this->getDirectorySeparator($web);
    }

    /**
     *
     * @param string $namespace
     *
     * @return string
     */
    public function getStoragePath($namespace = null)
    {
        $basePath = realpath($this->getBasePath() . '../files/');

        return $this->cache[self::STORAGE][(string) $namespace] = $basePath . DIRECTORY_SEPARATOR .
            ($namespace ? $this->getClassnameUtilities()->namespaceToPath($namespace) . DIRECTORY_SEPARATOR : '');
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     *
     * @return string
     */
    public function getTemplatesPath($namespace = 'Chamilo\Configuration', $web = false)
    {
        return $this->cache[self::TEMPLATES][(string) $namespace][(string) $web] =
            $this->getResourcesPath($namespace, $web) . 'Templates' . $this->getDirectorySeparator($web);
    }

    /**
     *
     * @param boolean $web
     *
     * @return string
     * @throws \Exception
     */
    public function getVendorPath($web = false)
    {
        if ($web)
        {
            throw new Exception('Storage is not directly accessible');
        }

        return $this->cache[self::VENDOR][(string) $web] =
            realpath($this->getBasePath($web) . '../vendor/') . $this->getDirectorySeparator($web);
    }

    /**
     * Checks if string is HTTP or FTP uri
     *
     * @param string $uri
     *
     * @return boolean
     */
    public function isWebUri($uri)
    {
        return ((stripos($uri, 'http://') === 0) || (stripos($uri, 'https://') === 0) ||
            (stripos($uri, 'ftp://') === 0));
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     *
     * @return string
     */
    public function namespaceToFullPath($namespace = null, $web = false)
    {
        return $this->cache[self::FULL][(string) $namespace][(string) $web] = $this->getBasePath($web) . ($namespace ?
                $this->getClassnameUtilities()->namespaceToPath($namespace, $web) . $this->getDirectorySeparator($web) :
                '');
    }
}
