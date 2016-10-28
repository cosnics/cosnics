<?php

namespace Chamilo\Application\Weblcms\Course\OpenCourse\Component;

use Chamilo\Application\Weblcms\Course\OpenCourse\Form\OpenCourseForm;
use Chamilo\Application\Weblcms\Course\OpenCourse\Manager;
use Chamilo\Libraries\Platform\Translation;

/**
 * Component to define existing courses as open by adding roles to them
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CreateComponent extends Manager
{
    /**
     * Runs this component and returns it's output
     */
    function run()
    {
        $form = new OpenCourseForm($this->get_url(), Translation::getInstance());
        $html = array();

        $html[] = $this->render_header();
        $html[] = $form->toHtml();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}