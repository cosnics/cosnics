<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class VisitTeamComponent
 */
class VisitTeamComponent extends Manager
{
    /**
     * @return string
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    function run()
    {
        $parentComponent = $this->getIntegrationLauncherComponent();
        $courseGroup = $parentComponent->get_course_group();

        if (!$this->getUser()->is_platform_admin() &&
            !$parentComponent->get_course()->is_course_admin($this->getUser()) &&
            !$courseGroup->is_member($this->getUser()))
        {
            throw new NotAllowedException();
        }

        try
        {
            $groupUrl =
                $this->getCourseGroupOffice365Connector()->getTeamUrlForVisit($courseGroup, $this->getUser());
        }
        catch(Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);
            throw new NotAllowedException();
        }

        return new RedirectResponse($groupUrl);
    }
}