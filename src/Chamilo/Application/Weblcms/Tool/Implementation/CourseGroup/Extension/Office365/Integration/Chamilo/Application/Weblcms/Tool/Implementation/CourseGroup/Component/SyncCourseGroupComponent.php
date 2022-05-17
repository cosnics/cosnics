<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component\IntegrationLauncherComponent;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Exception;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SyncCourseGroupComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    function run()
    {
        $parentComponent = $this->getIntegrationLauncherComponent();
        if (!$parentComponent->get_course()->is_course_admin($this->getUser()))
        {
            throw new NotAllowedException();
        }

        try
        {
            $this->getCourseGroupOffice365Connector()->syncCourseGroupSubscriptions(
                $this->getIntegrationLauncherComponent()->get_course_group()
            );

            $success = true;
            $message = 'CourseGroupSyncSuccessful';
        }
        catch (Exception $ex)
        {
            $success = false;
            $message = 'CourseGroupSyncFailed';
            $this->getExceptionLogger()->logException($ex, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);
        }

        $translator = $this->getTranslator();

        $this->redirect(
            $translator->trans(
                $message, [],
                'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup'
            ), !$success, [
            \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager::ACTION_GROUP_DETAILS
        ], [self::PARAM_ACTION, IntegrationLauncherComponent::PARAM_BASE_CONTEXT]
        );
    }

    /**
     * @return array
     */
    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = IntegrationLauncherComponent::PARAM_BASE_CONTEXT;
        $additionalParameters[] =
            \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager::PARAM_COURSE_GROUP;

        return parent::get_additional_parameters($additionalParameters);
    }
}