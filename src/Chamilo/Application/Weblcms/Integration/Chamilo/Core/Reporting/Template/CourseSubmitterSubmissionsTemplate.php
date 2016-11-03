<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentCourseGroupInformationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentPlatformGroupInformationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentSubmitterSubmissionsBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentUserInformationBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package application.weblcms.php.reporting.templates Reporting template with an overview of the assignment
 *          submissions from a user/group
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class CourseSubmitterSubmissionsTemplate extends ReportingTemplate
{

    public function __construct($parent)
    {
        parent :: __construct($parent);
        
        $this->init_parameters();
        $this->add_reporting_block($this->get_submitter_submissions_information());
        $this->add_reporting_block(new AssignmentSubmitterSubmissionsBlock($this));
        
        $assignment = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(), 
            $this->publication_id)->get_content_object();
        
        $custom_breadcrumbs = array();
        $params = array();
        $params[\Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager :: PARAM_SUBMITTER_TYPE] = null;
        $params[\Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager :: PARAM_TARGET_ID] = null;
        $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TEMPLATE_ID] = CourseAssignmentSubmittersTemplate :: class_name();
        $custom_breadcrumbs[] = new Breadcrumb($this->get_url($params), $assignment->get_title());
        if ($this->submitter_type ==
             \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_USER)
        {
            $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(), 
                (int) $this->target_id);
            $custom_breadcrumbs[] = new Breadcrumb($this->get_url(), $user->get_fullname());
        }
        if ($this->submitter_type ==
             \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_COURSE_GROUP)
        {
            $course_group = CourseGroupDataManager :: retrieve_by_id(CourseGroup :: class_name(), $this->target_id);
            $custom_breadcrumbs[] = new Breadcrumb($this->get_url(), $course_group->get_name());
        }
        if ($this->submitter_type ==
             \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_PLATFORM_GROUP)
        {
            $group = \Chamilo\Core\Group\Storage\DataManager :: retrieve_by_id(Group :: class_name(), $this->target_id);
            $custom_breadcrumbs[] = new Breadcrumb($this->get_url(), $group->get_name());
        }
        $this->set_custom_breadcrumb_trail($custom_breadcrumbs);
    }

    private function init_parameters()
    {
        $this->publication_id = Request :: get(\Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION);
        if ($this->publication_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION, $this->publication_id);
        }
        
        $this->target_id = Request :: get(
            \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager :: PARAM_TARGET_ID);
        if ($this->target_id)
        {
            $this->set_parameter(
                \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager :: PARAM_TARGET_ID, 
                $this->target_id);
        }
        
        $this->submitter_type = Request :: get(
            \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager :: PARAM_SUBMITTER_TYPE);
        if (isset($this->submitter_type))
        {
            $this->set_parameter(
                \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager :: PARAM_SUBMITTER_TYPE, 
                $this->submitter_type);
        }
    }

    public function get_submitter_submissions_information()
    {
        switch ($this->submitter_type)
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_COURSE_GROUP :
                return new AssignmentCourseGroupInformationBlock($this);
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_PLATFORM_GROUP :
                return new AssignmentPlatformGroupInformationBlock($this);
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_USER :
                return new AssignmentUserInformationBlock($this);
        }
    }
}
