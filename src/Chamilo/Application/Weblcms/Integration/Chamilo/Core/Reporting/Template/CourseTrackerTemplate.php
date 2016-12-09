<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AverageExerciseScoreBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath\AverageLearningPathScoreBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Tool\LastAccessToToolsBlock;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * $Id: course_tracker_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
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
        $this->add_reporting_block($this->get_average_learning_path_score());
        
        // $this->add_reporting_block($this->get_average_exercise_score());
    }

    public function get_last_access_to_tool()
    {
        $course_weblcms_block = new LastAccessToToolsBlock($this);
        $course_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE);
        $user_id = request::get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);
        if ($course_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE, $course_id);
        }
        if ($user_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_USERS, $user_id);
        }
        return $course_weblcms_block;
    }

    public function get_average_learning_path_score()
    {
        $course_weblcms_block = new AverageLearningPathScoreBlock($this);
        $course_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE);
        if ($course_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE, $course_id);
        }
        return $course_weblcms_block;
    }

    public function get_average_exercise_score()
    {
        $course_weblcms_block = new AverageExerciseScoreBlock($this);
        $course_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE);
        if ($course_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE, $course_id);
        }
        return $course_weblcms_block;
    }
}
