<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentInformationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentSubmissionsBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * Description of assignment_submissions_reporting_template
 * 
 * @author Anthony Hurst (Hogeschool Gent)
 */
class AssignmentSubmissionsTemplate extends ReportingTemplate
{

    public function __construct($parent)
    {
        parent :: __construct($parent);
        
        $this->publication_id = Request :: get(\Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION);
        
        $assignment = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(), 
            $this->publication_id)->get_content_object();
        
        $this->init_parameters();
        $this->add_reporting_block(new AssignmentInformationBlock($this));
        $this->add_reporting_block(AssignmentSubmissionsBlock :: getInstance($this));
        
        $custom_breadcrumbs = array();
        
        $parameters = array();
        $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL] = \Chamilo\Application\Weblcms\Manager :: ACTION_REPORTING;
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID] = $this->publication_id;
        $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL_ACTION] = \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager :: ACTION_VIEW;
        $custom_breadcrumbs[] = new Breadcrumb($this->get_url($parameters), $assignment->get_title());
        $custom_breadcrumbs[] = new Breadcrumb($this->get_url(), Translation :: get('SubmissionsOverview'));
        $this->set_custom_breadcrumb_trail($custom_breadcrumbs);
    }

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID, 
            \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager :: PARAM_TARGET_ID, 
            \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager :: PARAM_SUBMITTER_TYPE);
    }

    private function init_parameters()
    {
        if ($this->publication_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION, $this->publication_id);
        }
    }
}
