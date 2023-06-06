<?php
namespace Chamilo\Core\Home\Service;

use Chamilo\Configuration\Configuration;

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
     * @var AngularConnectorInterface[]
     */
    protected $angularConnectors;

    /**
     * Constructor
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration = null)
    {
        if (!$configuration)
        {
            $configuration = Configuration::getInstance();
        }

        $this->angularConnectors = [];

        $homeIntegrationPackages = $configuration->getIntegrationRegistrations('Chamilo\Core\Home');
        foreach ($homeIntegrationPackages as $homeIntegrationPackage)
        {
            $packageContext = $homeIntegrationPackage['context'];
            $class = $packageContext . '\\AngularConnector';

            if (class_exists($class))
            {
                $this->angularConnectors[] = new $class();
            }
        }
    }

    /**
     * Returns the list of angular modules
     *
     * @return string[]
     */
    public function getAngularModules()
    {
        $angularModules = [];

        foreach ($this->angularConnectors as $angularConnector)
        {
            $angularModules = array_merge($angularModules, $angularConnector->getAngularModules());
        }

        return $angularModules;
    }

    /**
     * Loads the angular modules javascript as html
     *
     * @return string[]
     */
    public function loadAngularModules()
    {
        $html = [];

        foreach ($this->angularConnectors as $angularConnector)
        {
            $html[] = $angularConnector->loadAngularModules();
        }

        return implode(PHP_EOL, $html);
    }
}