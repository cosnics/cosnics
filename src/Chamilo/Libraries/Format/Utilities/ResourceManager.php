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

    private static ?ResourceManager $instance = null;

    private PathBuilder $pathBuilder;

    /**
     *
     * @var string[]
     */
    private array $resources;

    public function __construct(PathBuilder $pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
        $this->resources = [];
    }

    /**
     * Use this function if you load a resource through another function / class and want to make sure that the
     * resource manager does not load it again
     */
    public function addPathToLoadedResources(string $path)
    {
        $this->resources[] = $path;
    }

    public static function getInstance(): ResourceManager
    {
        if (!isset(self::$instance))
        {
            self::$instance = new ResourceManager(PathBuilder::getInstance());
        }

        return self::$instance;
    }

    public function getResourceHtml(string $path): string
    {
        if ($this->hasResourceAlreadyBeenLoaded($path))
        {
            return '';
        }

        $this->resources[] = $path;

        return $this->renderResourceHtml($path);
    }

    /**
     * @return string[]
     */
    public function getResources(): array
    {
        return $this->resources;
    }

    /**
     * @return string[]
     * @deprecated Use ResourceManager::getResources() now
     */
    public function get_resources(): array
    {
        return $this->getResources();
    }

    public function hasResourceAlreadyBeenLoaded(string $path): bool
    {
        return in_array($path, $this->resources);
    }

    private function renderResourceHtml(string $path): string
    {
        $webPath = $this->pathBuilder->getBasePath(true);
        $basePath = $this->pathBuilder->getBasePath() . '../web/';

        $systemPath = str_replace($webPath, $basePath, $path);
        $modificationTime = filemtime($systemPath);

        $matches = [];
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
     * @deprecated Use hasResourceAlreadyBeenLoaded() now
     */
    public function resource_loaded(string $path): bool
    {
        return $this->hasResourceAlreadyBeenLoaded($path);
    }
}
