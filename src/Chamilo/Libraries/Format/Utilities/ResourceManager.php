<?php
namespace Chamilo\Libraries\Format\Utilities;

/**
 * $Id: resource_manager.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 *
 * @package common
 */
use Chamilo\Libraries\File\Path;

/**
 * Manages resources, ensuring that they are only loaded when necessary.
 * Currently only relevant for JavaScript and CSS
 * files.
 *
 * @author Tim De Pauw
 * @package common
 */
class ResourceManager
{

    private static $instance;

    private $resources;

    private function __construct()
    {
        $this->resources = array();
    }

    public function get_resources()
    {
        return $this->resources;
    }

    public function resource_loaded($path)
    {
        // return false;
        return in_array($path, $this->resources);
    }

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

    private function _get_resource_html($path)
    {
        $pathUtil = Path::getInstance();

        $webPath = $pathUtil->getBasePath(true);
        $basePath = $pathUtil->getBasePath();

        $systemPath = str_replace($webPath, $basePath, $path);
        $modificationTime = filemtime($systemPath);

        $matches = array();
        preg_match('/[^.]*$/', $path, $matches);
        $extension = $matches[0];
        switch (strtolower($extension))
        {
            case 'css' :
                return '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars($path) . '?' . $modificationTime . '"/>';
            case 'js' :
                return '<script type="text/javascript" src="' . htmlspecialchars($path) . '?' . $modificationTime . '"></script>';
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
        if (! isset(self :: $instance))
        {
            self :: $instance = new ResourceManager();
        }
        return self :: $instance;
    }
}
