<?php

namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service\CourseTeamService;
use Chamilo\Core\Install\Format\Structure\Header;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\IdentRenderer;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 * Class TabContentComponent
 * @todo: location
 */
class TeamsTabContentComponent extends Manager implements NoAuthenticationSupport
{

    use ContainerAwareTrait;

    /**
     * @return string
     * @throws UserException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function run(): string
    {
        $course_tools = \Chamilo\Application\Weblcms\Storage\DataManager::retrieves(
            CourseTool::class_name(),
            new DataClassRetrievesParameters(
                new InCondition(
                    new PropertyConditionVariable(CourseTool::class_name(), CourseTool::PROPERTY_CONTEXT),
                    [
                        'Chamilo\Application\Weblcms\Tool\Implementation\LearningPath',
                        'Chamilo\Application\Weblcms\Tool\Implementation\Assignment',
                        'Chamilo\Application\Weblcms\Tool\Implementation\Home',
                        'Chamilo\Application\Weblcms\Tool\Implementation\Assessment',
                        'Hogent\Application\Weblcms\Tool\Implementation\Bamaflex'
                    ]
                )
            )
        );

        /**
         * @var ChamiloRequest $request
         */
        $request = $this->getService('symfony.component.http_foundation.request');
        $groupId = $request->getFromUrl(self::PARAM_OFFICE365_GROUP_ID);

        if (!$groupId) {
            throw new UserException("No Team ID specified");
        }

        $header = new Header(
            Page::VIEW_MODE_HEADERLESS,
            'container-fluid',
            Translation::getInstance()->getLanguageIsocode(),
            'ltr');

        echo $header->render();

        /**
         * @var CourseTeamService $courseTeamService
         */
        $courseTeamService = $this->getService(CourseTeamService::class);
        $course = $courseTeamService->getCourseByTeamId($groupId);

        if (!$course) {
            throw new UserException("This team is not linked to a Chamilo course");
        }

        $learningPathTool = null;
        $assignmentTool = null;

        $tools = [];

        while ($tool = $course_tools->next_result()) {
            /**
             * @var CourseTool $tool
             */
            $twigTool = [
                "ACTIVE" => $this->toolIsVisible($tool, $course),
                "URL" => $this->getToolUrl($tool, $course),
                "TITLE" => Translation::get('TypeName', null, $tool->getContext()),
                "ICON" => (new IdentRenderer($tool->getContext(), false, false))->render(),
            ];

            if ($tool->getContext() === 'Chamilo\Application\Weblcms\Tool\Implementation\Home') {
                array_unshift($tools, $twigTool);
            } else {
                $tools[] = $twigTool;
            }
        }

        return $this->getTwig()->render(
            'Chamilo\Application\Weblcms:TeamsTabContent.html.twig',
            [

                "TOOLS" => $tools
            ]
        );
    }

    /**
     * @param CourseTool $tool
     * @param Course $course
     * @return bool
     */
    protected function toolIsVisible(?CourseTool $tool, Course $course): bool
    {
        if (is_null($tool)) {
            return false;
        }

        $course_settings_controller = CourseSettingsController::getInstance();

        return $course_settings_controller->get_course_setting(
                $course,
                CourseSetting::COURSE_SETTING_TOOL_ACTIVE,
                $tool->getId()
            ) &&
            $course_settings_controller->get_course_setting(
                $course,
                CourseSetting::COURSE_SETTING_TOOL_VISIBLE,
                $tool->getId());
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }

    /**
     * @param CourseTool $tool
     * @param Course $course
     * @return string
     */
    protected function getToolUrl(?CourseTool $tool, Course $course): string
    {
        if (is_null($tool)) {
            return 'NA';
        }

        return $this->get_url(
            array(
                Manager::PARAM_ACTION => Manager::ACTION_VIEW_COURSE,
                Manager::PARAM_TOOL_ACTION => null,
                Manager::PARAM_COURSE => $course->getId(),
                Manager::PARAM_TOOL => $tool->get_name()
            )
        );
    }
}