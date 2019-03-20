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
                    ['Chamilo\Application\Weblcms\Tool\Implementation\LearningPath', 'Chamilo\Application\Weblcms\Tool\Implementation\Assignment'])
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

        while ($tool = $course_tools->next_result()) {
            /**
             * @var CourseTool $tool
             */
            if ($tool->get_name() === 'Assignment') {// && ) {
                $assignmentTool = $tool;
            }

            if ($tool->get_name() === 'LearningPath') {// && $this->toolIsActive($tool, $course)) {
                $learningPathTool = $tool;
            }
        }

        return $this->getTwig()->render(
            'Chamilo\Application\Weblcms:TeamsTabContent.html.twig',
            [
                "LP_ACTIVE" => $this->toolIsActive($learningPathTool, $course),
                "LP_URL" => $this->getToolUrl($learningPathTool, $course),
                "LP_TRANSLATION" => Translation::get('TypeName', null, $learningPathTool->getContext()),
                "ASSIGNMENT_ACTIVE" => $this->toolIsActive($assignmentTool, $course),
                "ASSIGNMENT_URL" => $this->getToolUrl($assignmentTool, $course),
                "ASSIGNMENT_TRANSLATION" => Translation::get('TypeName', null, $assignmentTool->getContext())
            ]
        );
    }

    /**
     * @param CourseTool $tool
     * @param Course $course
     * @return bool
     */
    protected function toolIsActive(?CourseTool $tool, Course $course): bool
    {
        if (is_null($tool)) {
            return false;
        }

        $course_settings_controller = CourseSettingsController::getInstance();

        return $course_settings_controller->get_course_setting(
            $course,
            CourseSetting::COURSE_SETTING_TOOL_ACTIVE,
            $tool->getId()
        );
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