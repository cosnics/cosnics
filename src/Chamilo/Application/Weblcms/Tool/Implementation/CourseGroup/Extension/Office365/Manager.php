<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    const PARAM_ACTION = 'Office365Action';
    const ACTION_AUTHENTICATE = 'Authentication';
    const DEFAULT_ACTION = self::ACTION_AUTHENTICATE;

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Service\Office365Service
     */
    protected function getOffice365Service()
    {
        return $this->getService(
            'chamilo.application.weblcms.tool.implementation.course_group.extension.office365.service.office365_service'
        );
    }
}