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
     *
     * @param bool $forceUpdate
     */
    public function fixResources($forceUpdate = false)
    {
        $timer = new Timer();

        $this->logger->addInfo('Starting fixing resources');

        if ($forceUpdate)
        {
            $this->logger->addInfo('[WARNING] THIS FIXER IS RUNNING FOR REAL NOW, NO GOING BACK');
        }
        else
        {
            $this->logger->addInfo(
                'This fixer is running in debug mode, everything is logged but the content is not updated. ' .
                'Use the option --force / -f to force the update of the content'
            );
        }

        $timer->start();

        foreach ($this->resourceFixers as $resourceFixer)
        {
            $resourceFixer->fixResources($forceUpdate);
        }

        $timer->stop();
        $this->logger->addInfo(sprintf('Finished fixing resources in %s', $timer->get_time_in_hours()));
    }
}