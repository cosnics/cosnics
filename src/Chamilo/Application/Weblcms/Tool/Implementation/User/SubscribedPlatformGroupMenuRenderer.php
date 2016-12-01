<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User;

/**
 * Specific extension of the platform group menu renderer to select the default selected node
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SubscribedPlatformGroupMenuRenderer extends PlatformgroupMenuRenderer
{

    /**
     * Returns the default node id
     * 
     * @return int
     */
    protected function getDefaultNodeId()
    {
        return 0;
    }
}