<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\Component;

use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager;

class BrowserComponent extends Manager
{

    public function run()
    {
        $courseTools = $this->get_visible_tools();

        $introductionAllowed = CourseSettingsController::getInstance()->get_course_setting(
            $this->get_course(), CourseSettingsConnector::ALLOW_INTRODUCTION_TEXT
        );

        $type = 'Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\Type\ListHomeRenderer';

        $homeRenderer = new $type($this, $courseTools, $introductionAllowed, $this->get_introduction_text());

        $html = [];

        $html[] = $this->render_header($courseTools, $introductionAllowed);
        $html[] = $homeRenderer->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID;

        return parent::getAdditionalParameters($additionalParameters);
    }
}
