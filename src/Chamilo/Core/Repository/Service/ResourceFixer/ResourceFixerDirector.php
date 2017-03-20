<?php

namespace Chamilo\Core\Repository\Service\ResourceFixer;

use Chamilo\Libraries\Utilities\Timer;
use Monolog\Logger;

/**
 * Directs the resource fixers
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResourceFixerDirector
{
    /**
     * @var ResourceFixer[]
     */
    protected $resourceFixers = array();

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * ResourceFixerDirector constructor.
     *
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Adds a resource fixer to the list of resource fixers
     *
     * @param ResourceFixer $resourceFixer
     */
    public function addResourceFixer(ResourceFixer $resourceFixer)
    {
        $this->resourceFixers[] = $resourceFixer;
    }

    /**
     * Fixes the resources
     */
    public function fixResources()
    {
        $timer = new Timer();

        $this->logger->addInfo('Starting fixing resources');
        $timer->start();

        foreach($this->resourceFixers as $resourceFixer)
        {
            $resourceFixer->fixResources();
        }

        $timer->stop();
        $this->logger->addInfo(sprintf('Finished fixing resources in %s', $timer->get_time_in_hours()));
    }
}