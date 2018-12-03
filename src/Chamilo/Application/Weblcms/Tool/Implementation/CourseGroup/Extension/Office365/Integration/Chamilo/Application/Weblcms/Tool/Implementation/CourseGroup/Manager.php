<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component\IntegrationLauncherComponent;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    const PARAM_ACTION = 'Office365CourseGroupAction';

    const ACTION_VISIT_PLANNER = 'VisitPlanner';
    const ACTION_VISIT_GROUP = 'VisitGroup';
    const ACTION_VISIT_TEAM = 'VisitTeam';
    const ACTION_SYNC_COURSE_GROUP = 'SyncCourseGroup';

    const DEFAULT_ACTION = self::ACTION_VISIT_GROUP;

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component\IntegrationLauncherComponent
     */
    protected function getIntegrationLauncherComponent()
    {
        $application = $this->get_application();
        if (!$application instanceof IntegrationLauncherComponent)
        {
            throw new \RuntimeException(
                'The course group integration can only be launched from the integration launcher component in the course groups'
            );
        }

        return $application;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService
     */
    protected function getCourseGroupOffice365ReferenceService()
    {
        return $this->getService(
            'chamilo.application.weblcms.tool.implementation.course_group.extension.office365.integration.chamilo.application.weblcms.tool.implementation.course_group.service.course_group_office365_reference_service'
        );
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365Connector
     */
    protected function getCourseGroupOffice365Connector()
    {
        return $this->getService(
            'chamilo.application.weblcms.tool.implementation.course_group.extension.office365.integration.chamilo.application.weblcms.tool.implementation.course_group.service.course_group_office365_connector'
        );
    }
}