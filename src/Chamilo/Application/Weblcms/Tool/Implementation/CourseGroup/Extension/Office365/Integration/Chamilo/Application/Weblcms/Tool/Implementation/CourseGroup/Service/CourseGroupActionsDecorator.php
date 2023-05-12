<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component\IntegrationLauncherComponent;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupActionsDecoratorInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupActionsDecorator implements CourseGroupActionsDecoratorInterface
{
    /**
     * @var \Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService
     */
    protected $courseGroupOffice365ReferenceService;

    /**
     * CourseGroupActionsDecorator constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator $urlGenerator
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService $courseGroupOffice365ReferenceService
     */
    public function __construct(
        UrlGenerator $urlGenerator, Translator $translator,
        CourseGroupOffice365ReferenceService $courseGroupOffice365ReferenceService
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->courseGroupOffice365ReferenceService = $courseGroupOffice365ReferenceService;
    }

    /**
     * Adds actions to the toolbar of integration actions
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar $courseGroupActionsToolbar
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param bool $isCourseTeacher
     */
    public function addCourseGroupActions(
        ButtonToolBar $courseGroupActionsToolbar, CourseGroup $courseGroup, User $user, $isCourseTeacher = false
    )
    {
        if (!$this->courseGroupOffice365ReferenceService->courseGroupHasLinkedReference($courseGroup))
        {
            return;
        }

        $visitGroupUrl = $this->urlGenerator->fromRequest(
            [
                \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager::PARAM_ACTION =>
                    \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager::ACTION_LAUNCH_INTEGRATION,
                IntegrationLauncherComponent::PARAM_BASE_CONTEXT =>
                    'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365',
                Manager::PARAM_ACTION => Manager::ACTION_VISIT_GROUP
            ]
        );

        $visitGroupLabel = $this->translator->trans('VisitGroup', [], Manager::CONTEXT);

        $visitPlannerUrl = $this->urlGenerator->fromRequest(
            [
                \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager::PARAM_ACTION =>
                    \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager::ACTION_LAUNCH_INTEGRATION,
                IntegrationLauncherComponent::PARAM_BASE_CONTEXT =>
                    'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365',
                Manager::PARAM_ACTION => Manager::ACTION_VISIT_PLANNER
            ]
        );

        $groupReference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);

        if ($groupReference->hasTeam()) {
            $visitTeamLabel = $this->translator->trans('VisitTeam', [], Manager::CONTEXT);

            $visitTeamUrl = $this->urlGenerator->fromRequest(
                [
                    \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager::PARAM_ACTION =>
                        \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager::ACTION_LAUNCH_INTEGRATION,
                    IntegrationLauncherComponent::PARAM_BASE_CONTEXT =>
                        'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365',
                    Manager::PARAM_ACTION => Manager::ACTION_VISIT_TEAM
                ]
            );
        }

        $visitPlannerLabel = $this->translator->trans('VisitPlanner', [], Manager::CONTEXT);

        $synchronizePlannerUrl = $this->urlGenerator->fromRequest(
            [
                \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager::PARAM_ACTION =>
                    \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager::ACTION_LAUNCH_INTEGRATION,
                IntegrationLauncherComponent::PARAM_BASE_CONTEXT =>
                    'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365',
                Manager::PARAM_ACTION => Manager::ACTION_SYNC_COURSE_GROUP
            ]
        );

        $synchronizePlannerLabel = $this->translator->trans('SynchronizeUsersToPlanner', [], Manager::CONTEXT);

        $visitGroupButton = new SplitDropdownButton(
            $visitGroupLabel, null, $visitGroupUrl,
            Button::DISPLAY_ICON_AND_LABEL, false, null, '_blank'
        );

        $visitGroupButton->addSubButton(
            new SubButton(
                $visitPlannerLabel, null, $visitPlannerUrl, Button::DISPLAY_ICON_AND_LABEL, null, null, '_blank'
            )
        );

        if($groupReference->hasTeam()){
            $visitGroupButton->addSubButton(
                new SubButton(
                $visitTeamLabel, null, $visitTeamUrl, Button::DISPLAY_ICON_AND_LABEL, null, [], '_blank'
               )
            );
        }


        if ($isCourseTeacher)
        {
            $visitGroupButton->addSubButton(new SubButton($synchronizePlannerLabel, null, $synchronizePlannerUrl));
        }

        $courseGroupActionsToolbar->addItem($visitGroupButton);
    }
}
