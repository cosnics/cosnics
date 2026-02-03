<?php
namespace Chamilo\Libraries\Service\Resource;

use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;

/**
 * Manages resources, ensuring that they are only loaded when necessary.
 *
 * @package Chamilo\Libraries\Service\Resource
 * @author  Tim De Pauw
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ResourceManager
{
    /**
     * @var string[]
     */
    private array $resources;

    private SystemPathBuilder $systemPathBuilder;

    private WebPathBuilder $webPathBuilder;

    public function __construct(SystemPathBuilder $systemPathBuilder, WebPathBuilder $webPathBuilder)
    {
        $this->systemPathBuilder = $systemPathBuilder;
        $this->webPathBuilder = $webPathBuilder;
        $this->resources = [];
    }

    /**
     * Use this function if you load a resource through another function / class and want to make sure that the
     * resource manager does not load it again
     */
    public function addPathToLoadedResources(string $path): static
    {
        $this->resources[] = $path;

        return $this;
    }

    public function getResourceHtml(string $path): string
    {
        if ($this->hasResourceAlreadyBeenLoaded($path)) {
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

    public function hasResourceAlreadyBeenLoaded(string $path): bool
    {
        return in_array($path, $this->resources);
    }

    private function renderResourceHtml(string $path): string
    {
        $webPath = $this->webPathBuilder->getBasePath();
        $basePath = $this->systemPathBuilder->getPublicPath();

        $systemPath = str_replace($webPath, $basePath, $path);
        $modificationTime = filemtime($systemPath);

        $matches = [];
        preg_match('/[^.]*$/', $path, $matches);
        $extension = $matches[0];

        switch (strtolower($extension)) {
            case 'css' :
                return '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars($path) . '?' .
                    $modificationTime . '"/>';
            case 'js' :
                return '<script src="' . htmlspecialchars($path) . '?' . $modificationTime . '"></script>';
            default :
                die('Unknown resource type: ' . $path);
        }
    }
}
