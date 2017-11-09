<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class VisitPlannerComponent extends Manager
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

        if (!$parentComponent->get_course()->is_course_admin($this->getUser()) ||
            !$courseGroup->is_member($this->getUser()))
        {
            throw new NotAllowedException();
        }

        $office365ReferenceService = $this->getCourseGroupOffice365ReferenceService();
        if (!$office365ReferenceService->courseGroupHasReference($courseGroup))
        {
            throw new NotAllowedException();
        }

        $reference = $office365ReferenceService->getCourseGroupReference($courseGroup);

        $office365Service = $this->getOffice365Service();
        if (!$office365Service->isMemberOfGroup($reference->getOffice365GroupId(), $this->getUser()))
        {
            try
            {
                $office365Service->addMemberToGroup($reference->getOffice365GroupId(), $this->getUser());
            }
            catch (\Exception $ex)
            {
                throw new NotAllowedException();
            }
        }

        $baseUrl = $this->getConfigurationConsulter()->getSetting(
            ['Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365', 'planner_base_uri']
        );

        $planId = $reference->getOffice365PlanId();
        if (empty($planId))
        {
            $planId = $this->getOffice365Service()->getDefaultGroupPlanId($reference->getOffice365GroupId());
            $office365ReferenceService->storePlannerReferenceForCourseGroup(
                $courseGroup, $reference->getOffice365GroupId(), $planId
            );
        }

        $plannerUrl = $baseUrl . '/#/plantaskboard?groupId=%s&planId=%s';
        $plannerUrl = sprintf($plannerUrl, $reference->getOffice365GroupId(), $planId);

        return new RedirectResponse($plannerUrl);
    }
}