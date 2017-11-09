<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    const PARAM_ACTION = 'Office365Action';
    const ACTION_AUTHENTICATE = 'Authentication';
    const DEFAULT_ACTION = self::ACTION_AUTHENTICATE;

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\Office365Service
     */
    protected function getOffice365Service()
    {
        return $this->getService(
            'chamilo.application.weblcms.tool.implementation.course_group.extension.office365.service.office365_service');
    }
}