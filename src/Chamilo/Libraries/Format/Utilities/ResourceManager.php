<?php
namespace Chamilo\Libraries\Format\Utilities;

use Chamilo\Libraries\File\PathBuilder;

/**
 * Manages resources, ensuring that they are only loaded when necessary.
 * Currently only relevant for JavaScript and CSS files.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
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

    /**
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    private $pathBuilder;

    /**
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     */
    public function __construct(PathBuilder $pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
        $this->resources = array();
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
     * @return \Chamilo\Libraries\Format\Utilities\ResourceManager
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new ResourceManager(PathBuilder::getInstance());
        }

        return self::$instance;
    }

    /**
     *
     * @param string $path
     *
     * @return string
     */
    public function getResourceHtml($path)
    {
        if ($this->hasResourceAlreadyBeenLoaded($path))
        {
            return '';
        }

        $this->resources[] = $path;

        return $this->renderResourceHtml($path);
    }

    /**
     *
     * @return string[]
     * @deprecated Use getResources() now
     */
    public function get_resources()
    {
        return $this->getResources();
    }

    /**
     *
     * @return string[]
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     *
     * @param string $path
     *
     * @return boolean
     */
    public function hasResourceAlreadyBeenLoaded($path)
    {
        return in_array($path, $this->resources);
    }

    /**
     *
     * @param string $path
     *
     * @return string
     */
    private function renderResourceHtml($path)
    {
        $webPath = $this->pathBuilder->getBasePath(true);
        $basePath = $this->pathBuilder->getBasePath() . '../web/';

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
                return '<script src="' . htmlspecialchars($path) . '?' . $modificationTime . '"></script>';
            default :
                die('Unknown resource type: ' . $path);
        }
    }

    /**
     *
     * @param string $path
     *
     * @return boolean
     * @deprecated Use hasResourceAlreadyBeenLoaded() now
     */
    public function resource_loaded($path)
    {
        return $this->hasResourceAlreadyBeenLoaded($path);
    }
}
