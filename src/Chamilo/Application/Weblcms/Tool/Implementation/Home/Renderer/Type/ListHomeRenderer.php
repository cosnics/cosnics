<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\Type;

use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\HomeRenderer;
use Chamilo\Application\Weblcms\Renderer\ToolList\ToolListRenderer;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ListHomeRenderer extends HomeRenderer
{

    /**
     *
     * @see \Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\HomeRenderer::render()
     */
    public function render()
    {
        $renderer = ToolListRenderer::factory(
            ToolListRenderer::TYPE_FIXED,
            $this->getHomeTool(),
            $this->getCourseTools()
        );

        $html = array();

        if ($this->isCourseClosedForStudents())
        {
            $html[] = '<div class="alert alert-warning">';
            $html[] = '<div class="closed-course-for-students-message pull-left">';
            $html[] = Translation::getInstance()->getTranslation('CourseClosedForStudents');
            $html[]= '</div>';
            $html[] = '<div class="pull-right">';
            $html[] = '<a type="button" class="btn btn-primary" href="' . $this->getOpenCourseUrl() . '">';
            $html[] = Translation::getInstance()->getTranslation('OpenCourseForStudents');
            $html[] = '</a>';
            $html[] = '</div>';
            $html[] = '<div class="clearfix"></div>';
            $html[] = '</div>';
        }

        $html[] = $this->homeRendererExtensionManager->renderTopLevelInformation($this);

        $html[] = $this->getHomeTool()->renderHomeActions();

        $html[] = '<div class="clearfix"></div>';

        if ($this->getIntroductionAllowed())
        {
            $html[] = $this->getHomeTool()->display_introduction_text($this->getIntroduction());
        }

        $html[] = $renderer->toHtml();

        return implode(PHP_EOL, $html);
    }

    protected function isCourseClosedForStudents()
    {
        $courseSettingsController = CourseSettingsController::getInstance();
        $courseAccess =
            $courseSettingsController->get_course_setting($this->getCourse(), CourseSettingsConnector::COURSE_ACCESS);

        if ($courseAccess == CourseSettingsConnector::COURSE_ACCESS_CLOSED)
        {
            return true;
        }

        $courseVisibility =
            $courseSettingsController->get_course_setting($this->getCourse(), CourseSettingsConnector::VISIBILITY);

        return !$courseVisibility;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
     */
    protected function getCourse()
    {
        return $this->getHomeTool()->get_course();
    }

    /**
     * @return string
     */
    protected function getOpenCourseUrl()
    {
        return $this->getHomeTool()->getOpenCourseUrl();
    }
}
