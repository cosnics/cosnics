<?php
namespace Chamilo\Libraries\File;

use Chamilo\Libraries\Architecture\ClassnameUtilities;

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
    const FULL = 1;
    const BASE = 2;
    const RELATIVE = 3;
    const STORAGE = 4;
    const TEMPORARY = 5;
    const CACHE = 6;
    const LOG = 7;
    const ARCHIVE = 8;
    const REPOSITORY = 9;
    const PROFILE_PICTURE = 10;
    const PLUGIN = 11;
    const CONFIGURATION = 12;
    const RESOURCE = 13;
    const JAVASCRIPT = 14;
    const I18N = 15;
    const VENDOR = 16;
    const PUBLIC_STORAGE = 17;
    const TEMPLATES = 18;

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
     * Constructor
     *
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function __construct(\Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities)
    {
        $this->classnameUtilities = $classnameUtilities;
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
     * @param boolean $web
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
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function getStoragePath($namespace = null)
    {
        $basePath = realpath($this->getBasePath() . '../files/');

        return $this->cache[self::STORAGE][(string) $namespace] = $basePath . DIRECTORY_SEPARATOR .
             ($namespace ? $this->getClassnameUtilities()->namespaceToPath($namespace) . DIRECTORY_SEPARATOR : '');
    }

    public function getPublicStoragePath($namespace = null, $web = false)
    {
        if ($web)
        {
            $basePath = $this->getBasePath($web) . 'Files';
        }
        else
        {
            $basePath = realpath(
                $this->getBasePath($web) . '..' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'Files');
        }
        return $this->cache[self::PUBLIC_STORAGE][(string) $namespace][(string) $web] = $basePath .
             ($web ? '/' : DIRECTORY_SEPARATOR) . ($namespace ? $this->getClassnameUtilities()->namespaceToPath(
                $namespace,
                $web) . ($web ? '/' : DIRECTORY_SEPARATOR) : '');
    }

    /**
     *
     * @param boolean $web
     * @return string
     */
    public function getVendorPath($web = false)
    {
        if ($web)
        {
            throw new \Exception('Storage is not directly accessible');
        }

        return $this->cache[self::VENDOR][(string) $web] = realpath($this->getBasePath($web) . '../vendor/') .
             ($web ? '/' : DIRECTORY_SEPARATOR);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function getPluginPath($namespace = 'Chamilo\Configuration', $web = false)
    {
        return $this->cache[self::PLUGIN][(string) $namespace][(string) $web] = $this->namespaceToFullPath(
            $namespace,
            $web) . 'Plugin' . ($web ? '/' : DIRECTORY_SEPARATOR);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function getResourcesPath($namespace = 'Chamilo\Configuration', $web = false)
    {
        return $this->cache[self::RESOURCE][(string) $namespace][(string) $web] = $this->namespaceToFullPath(
            $namespace,
            $web) . 'Resources' . ($web ? '/' : DIRECTORY_SEPARATOR);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function getJavascriptPath($namespace = 'Chamilo\Configuration', $web = false)
    {
        return $this->cache[self::JAVASCRIPT][(string) $namespace][(string) $web] = $this->getResourcesPath(
            $namespace,
            $web) . 'Javascript' . ($web ? '/' : DIRECTORY_SEPARATOR);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function getI18nPath($namespace = 'Chamilo\Configuration', $web = false)
    {
        return $this->cache[self::I18N][(string) $namespace][(string) $web] = $this->getResourcesPath($namespace, $web) .
             'I18n' . ($web ? '/' : DIRECTORY_SEPARATOR);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function getTemplatesPath($namespace = 'Chamilo\Configuration', $web = false)
    {
        return $this->cache[self::TEMPLATES][(string) $namespace][(string) $web] = $this->getResourcesPath(
            $namespace,
            $web) . 'Templates' . ($web ? '/' : DIRECTORY_SEPARATOR);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function getConfigurationPath($namespace = 'Chamilo\Configuration', $web = false)
    {
        return $this->cache[self::CONFIGURATION][(string) $namespace][(string) $web] = $this->getResourcesPath(
            $namespace,
            $web) . 'Configuration' . ($web ? '/' : DIRECTORY_SEPARATOR);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function namespaceToFullPath($namespace = null, $web = false)
    {
        return $this->cache[self::FULL][(string) $namespace][(string) $web] = $this->getBasePath($web) . ($namespace ? $this->getClassnameUtilities()->namespaceToPath(
            $namespace,
            $web) . ($web ? '/' : DIRECTORY_SEPARATOR) : '');
    }

    /*
     * Checks if string is HTTP or FTP uri @param string $uri @return boolean
     */
    public function isWebUri($uri)
    {
        return ((stripos($uri, 'http://') === 0) || (stripos($uri, 'https://') === 0) || (stripos($uri, 'ftp://') === 0));
    }
}
