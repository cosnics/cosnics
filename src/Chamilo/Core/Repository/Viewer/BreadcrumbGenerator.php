<?php
namespace Chamilo\Core\Repository\Viewer;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 * @package Chamilo\Core\Repository\Viewer
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BreadcrumbGenerator extends \Chamilo\Libraries\Format\Breadcrumb\BreadcrumbGenerator
{

    /**
     * @throws \ReflectionException
     */
    protected function generateComponentBreadcrumb(Application $application): void
    {
        if ($application->areBreadcrumbsDisabled())
        {
            return;
        }

        parent::generateComponentBreadcrumb($application);
    }
}