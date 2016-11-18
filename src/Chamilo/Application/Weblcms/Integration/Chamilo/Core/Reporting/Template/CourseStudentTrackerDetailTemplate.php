<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\CourseUserExerciseInformationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\CourseUserAssignmentInformationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath\CourseUserLearningPathInformationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Tool\LastAccessToToolsUserBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\User\UserInformationBlock;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * $Id: course_student_tracker_detail_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.reporting.templates
 */
/**
 *
 * @author Michael Kyndt
 */
class CourseStudentTrackerDetailTemplate extends ReportingTemplate
{

    public function __construct($parent)
    {
        parent::__construct($parent);
        
        $user_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);
        $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_USERS, $user_id);
        
        $this->add_reporting_block(new UserInformationBlock($this));
        $this->add_reporting_block(new CourseUserAssignmentInformationBlock($this));
        $this->add_reporting_block(new CourseUserExerciseInformationBlock($this));
        $this->add_reporting_block(new CourseUserLearningPathInformationBlock($this));
        $this->add_reporting_block(new LastAccessToToolsUserBlock($this));
        
        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
            (int) $user_id);
        
        if ($user)
        {
            BreadcrumbTrail::getInstance()->add(new Breadcrumb($this->get_url(), $user->get_fullname()));
        }
    }
}
