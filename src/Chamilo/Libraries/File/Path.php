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
class Path
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

    /**
     *
     * @var \Chamilo\Libraries\File\Path
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
     * @return \Chamilo\Libraries\File\Path
     */
    static public function getInstance()
    {
        if (is_null(static :: $instance))
        {
            self :: $instance = new static(ClassnameUtilities :: getInstance());
        }

        return static :: $instance;
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

        return $this->cache[self :: BASE][(string) $web] = $path;
    }

    public function getRelativePath()
    {
        $url_append = \Chamilo\Configuration\Configuration :: get('Chamilo\Configuration', 'general', 'url_append');
        return $this->cache[self :: RELATIVE] = (substr($url_append, - 1) === '/' ? $url_append : $url_append . '/');
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function getStoragePath($namespace = null, $web = false)
    {
        return $this->cache[self :: STORAGE][(string) $namespace][(string) $web] = realpath(
            $this->getBasePath($web) . '../files/') . ($web ? '/' : DIRECTORY_SEPARATOR) .
             ($namespace ? $this->classnameUtilities->namespaceToPath($namespace) . ($web ? '/' : DIRECTORY_SEPARATOR) : '');
    }

    /**
     *
     * @param boolean $web
     * @return string
     */
    public function getVendorPath($web = false)
    {
        return $this->cache[self :: VENDOR][(string) $web] = realpath($this->getBasePath($web) . '../vendor/') .
             ($web ? '/' : DIRECTORY_SEPARATOR);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function getTemporaryPath($namespace = null, $web = false)
    {
        $namespace = ($namespace ? 'temp\\' . $namespace : 'temp');
        return $this->cache[self :: TEMPORARY][(string) $namespace][(string) $web] = $this->getStoragePath(
            $namespace,
            $web);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function getCachePath($namespace = null, $web = false)
    {
        $namespace = ($namespace ? 'cache\\' . $namespace : 'cache');
        return $this->cache[self :: CACHE][(string) $namespace][(string) $web] = $this->getStoragePath($namespace, $web);
    }

    /**
     *
     * @param boolean $web
     * @return string
     */
    public function getLogPath($web = false)
    {
        return $this->cache[self :: LOG][(string) $web] = $this->getStoragePath('log', $web);
    }

    /**
     *
     * @param boolean $web
     * @return string
     */
    public function getArchivePath($web = false)
    {
        return $this->cache[self :: ARCHIVE][(string) $web] = $this->getStoragePath('archive', $web);
    }

    /**
     *
     * @param boolean $web
     * @return string
     */
    public function getRepositoryPath($web = false)
    {
        return $this->cache[self :: REPOSITORY][(string) $web] = $this->getStoragePath('Repository', $web);
    }

    /**
     *
     * @param boolean $web
     * @return string
     */
    public function getProfilePicturePath($web = false)
    {
        return $this->cache[self :: PROFILE_PICTURE][(string) $web] = $this->getStoragePath('userpictures', $web);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function getPluginPath($namespace = 'Chamilo\Configuration', $web = false)
    {
        return $this->cache[self :: PLUGIN][(string) $namespace][(string) $web] = $this->namespaceToFullPath(
            $namespace,
            $web) . 'plugin' . ($web ? '/' : DIRECTORY_SEPARATOR);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function getResourcesPath($namespace = 'Chamilo\Configuration', $web = false)
    {
        return $this->cache[self :: RESOURCE][(string) $namespace][(string) $web] = $this->namespaceToFullPath(
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
        return $this->cache[self :: JAVASCRIPT][(string) $namespace][(string) $web] = $this->getResourcesPath(
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
        return $this->cache[self :: I18N][(string) $namespace][(string) $web] = $this->getResourcesPath(
            $namespace,
            $web) . 'I18n' . ($web ? '/' : DIRECTORY_SEPARATOR);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function getConfigurationPath($web = false)
    {
        return $this->cache[self :: CONFIGURATION][(string) $web] = $this->namespaceToFullPath(
            'Chamilo\Configuration',
            $web);
    }

    /**
     *
     * @param string $namespace
     * @param boolean $web
     * @return string
     */
    public function namespaceToFullPath($namespace = null, $web = false)
    {
        return $this->cache[self :: FULL][(string) $namespace][(string) $web] = $this->getBasePath($web) . ($namespace ? $this->classnameUtilities->namespaceToPath(
            $namespace,
            $web) . ($web ? '/' : DIRECTORY_SEPARATOR) : '');
    }
}
