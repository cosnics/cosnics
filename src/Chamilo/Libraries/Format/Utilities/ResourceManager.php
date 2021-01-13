<?php
namespace Chamilo\Libraries\Format\Utilities;

use Chamilo\Libraries\File\Path;

/**
 * Manages resources, ensuring that they are only loaded when necessary.
 * Currently only relevant for JavaScript and CSS files.
 *
 * @author Tim De Pauw
 * @package Chamilo\Libraries\Format\Utilities
 */
class ResourceManager
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Utilities\ResourceManager
     */
    private static $instance;

    /**
     *
     * @var string[]
     */
    private $resources;

    private function __construct()
    {
        $this->resources = array();
    }

    /**
     *
     * @return string[]
     */
    public function get_resources()
    {
        return $this->resources;
    }

    /**
     *
     * @param string $path
     * @return boolean
     */
    public function resource_loaded($path)
    {
        // return false;
        return in_array($path, $this->resources);
    }

    /**
     * Use this function if you load a resource through another function / class and want to make sure that
     * the resource manager does not load it again
     *
     * @param string $path
     */
    public function addPathToLoadedResources($path)
    {
        $this->resources[] = $path;
    }

    /**
     *
     * @param string $path
     * @return string
     */
    public function get_resource_html($path)
    {
        if ($this->resource_loaded($path))
        {
            return '';
        }
        else
        {
            $this->resources[] = $path;
            return $this->_get_resource_html($path);
        }
    }

    /**
     *
     * @param string $path
     * @return string
     */
    private function _get_resource_html($path)
    {
        $pathUtil = Path::getInstance();

        $webPath = $pathUtil->getBasePath(true);
        $basePath = $pathUtil->getBasePath() . '../web/';

        $systemPath = str_replace($webPath, $basePath, $path);
        $modificationTime = filemtime($systemPath);

        $matches = array();
        preg_match('/[^.]*$/', $path, $matches);
        $extension = $matches[0];
        switch (strtolower($extension))
        {
            case 'css' :
                return '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars($path) . '?' .
                     $modificationTime . '"/>';
            case 'js' :
                return '<script type="text/javascript" src="' . htmlspecialchars($path) . '?' . $modificationTime .
                     '"></script>';
            default :
                die('Unknown resource type: ' . $path);
        }
    }

    /**
     *
     * @return ResourceManager
     */
    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new ResourceManager();
        }
        return self::$instance;
    }
}
