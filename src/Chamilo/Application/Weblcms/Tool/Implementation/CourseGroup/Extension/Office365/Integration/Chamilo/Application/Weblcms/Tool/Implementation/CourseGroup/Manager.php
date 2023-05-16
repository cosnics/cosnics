<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component\IntegrationLauncherComponent;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365Connector;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService;
use Chamilo\Libraries\Architecture\Application\Application;
use RuntimeException;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    public const ACTION_SYNC_COURSE_GROUP = 'SyncCourseGroup';
    public const ACTION_VISIT_GROUP = 'VisitGroup';
    public const ACTION_VISIT_PLANNER = 'VisitPlanner';
    public const ACTION_VISIT_TEAM = 'VisitTeam';

    public const CONTEXT = __NAMESPACE__;

    public const DEFAULT_ACTION = self::ACTION_VISIT_GROUP;
    public const PARAM_ACTION = 'Office365CourseGroupAction';

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365Connector
     */
    protected function getCourseGroupOffice365Connector()
    {
        return $this->getService(CourseGroupOffice365Connector::class);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService
     */
    protected function getCourseGroupOffice365ReferenceService()
    {
        return $this->getService(CourseGroupOffice365ReferenceService::class);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component\IntegrationLauncherComponent
     */
    protected function getIntegrationLauncherComponent()
    {
        $application = $this->get_application();
        if (!$application instanceof IntegrationLauncherComponent)
        {
            throw new RuntimeException(
                'The course group integration can only be launched from the integration launcher component in the course groups'
            );
        }

        return $application;
    }
}