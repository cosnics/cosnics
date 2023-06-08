<?php
namespace Chamilo\Core\Home\Service;

use Chamilo\Core\Home\Architecture\Interfaces\AngularConnectorInterface;

/**
 * Services that connects to the several home integration packages and connects their angular functionality
 * to the home page
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AngularConnectorService
{

    /**
     * List of available angular connectors
     *
     * @var \Chamilo\Core\Home\Architecture\Interfaces\AngularConnectorInterface[]
     */
    protected array $angularConnectors;

    public function addAngularConnector(AngularConnectorInterface $angularConnector): void
    {
        $this->angularConnectors[get_class($angularConnector)] = $angularConnector;
    }

    public function getAngularConnectors(): array
    {
        return $this->angularConnectors;
    }

    /**
     * @return string[]
     */
    public function getAngularModules(): array
    {
        $angularModules = [];

        foreach ($this->getAngularConnectors() as $angularConnector)
        {
            $angularModules = array_merge($angularModules, $angularConnector->getAngularModules());
        }

        return $angularModules;
    }

    public function loadAngularModules(): string
    {
        $html = [];

        foreach ($this->getAngularConnectors() as $angularConnector)
        {
            $html[] = $angularConnector->loadAngularModules();
        }

        return implode(PHP_EOL, $html);
    }
}