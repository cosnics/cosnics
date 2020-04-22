<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AverageExerciseScoreBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Tool\LastAccessToToolsBlock;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package application.lib.weblcms.reporting.templates
 */
/**
 *
 * @author Michael Kyndt
 */
class CourseTrackerTemplate extends ReportingTemplate
{

    public function __construct($parent)
    {
        parent::__construct($parent);

        $this->add_reporting_block($this->get_last_access_to_tool());
        // $this->add_reporting_block($this->get_average_exercise_score());
    }

    public function get_last_access_to_tool()
    {
        $course_weblcms_block = new LastAccessToToolsBlock($this);
        $course_id = Request::get(Manager::PARAM_COURSE);
        $user_id = request::get(Manager::PARAM_USERS);
        if ($course_id)
        {
            $this->set_parameter(Manager::PARAM_COURSE, $course_id);
        }
        if ($user_id)
        {
            $this->set_parameter(Manager::PARAM_USERS, $user_id);
        }
        return $course_weblcms_block;
    }

    public function get_average_exercise_score()
    {
        $course_weblcms_block = new AverageExerciseScoreBlock($this);
        $course_id = Request::get(Manager::PARAM_COURSE);
        if ($course_id)
        {
            $this->set_parameter(Manager::PARAM_COURSE, $course_id);
        }
        return $course_weblcms_block;
    }
}
