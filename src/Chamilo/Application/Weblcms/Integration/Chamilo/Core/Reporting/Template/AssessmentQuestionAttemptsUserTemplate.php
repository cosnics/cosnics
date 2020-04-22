<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AssessmentQuestionAttemptsUserBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AssessmentQuestionUserInformationBlock;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.weblcms.php.reporting.templates Reporting template with the assessment question attempts of one
 *          user
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class AssessmentQuestionAttemptsUserTemplate extends ReportingTemplate
{

    public function __construct($parent)
    {
        parent::__construct($parent);
        
        $this->set_parameter(
            \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::PARAM_QUESTION, 
            Request::get(\Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::PARAM_QUESTION));
        $this->set_parameter(
            Manager::PARAM_PUBLICATION,
            Request::get(Manager::PARAM_PUBLICATION));
        $this->set_parameter(
            Manager::PARAM_USERS,
            Request::get(Manager::PARAM_USERS));
        
        $this->add_reporting_block(new AssessmentQuestionUserInformationBlock($this));
        
        $this->add_reporting_block(new AssessmentQuestionAttemptsUserBlock($this));
        $this->add_breadcrumbs();
    }

    /**
     * Adds the breadcrumbs to the breadcrumbtrail
     */
    protected function add_breadcrumbs()
    {
        $assessment = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $this->get_parameter(Manager::PARAM_PUBLICATION))->get_content_object();
        
        $question = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ComplexContentObjectItem::class_name(), 
            $this->get_parameter(\Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::PARAM_QUESTION));
        
        $trail = BreadcrumbTrail::getInstance();
        
        $trail->add(
            new Breadcrumb(
                $this->get_url(
                    array(\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID => 2), 
                    array(Manager::PARAM_TEMPLATE_ID)),
                Translation::get('Assessments')));
        
        $filters = array(
            Manager::PARAM_USERS,
            \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::PARAM_QUESTION);
        
        $params = array();
        $params[Manager::PARAM_TEMPLATE_ID] = AssessmentAttemptsTemplate::class_name();
        $params[Manager::PARAM_PUBLICATION] = $this->publication_id;
        
        $trail->add(new Breadcrumb($this->get_url($params, $filters), $assessment->get_title()));
        
        $params[\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID] = 2;
        
        $trail->add(new Breadcrumb($this->get_url($params, $filters), Translation::get('Questions')));
        
        $filters = array(Manager::PARAM_USERS);
        
        $params = array();
        $params[Manager::PARAM_TEMPLATE_ID] = AssessmentQuestionUsersTemplate::class_name();
        $params[Manager::PARAM_PUBLICATION] = $this->publication_id;
        
        $trail->add(new Breadcrumb($this->get_url($params, $filters), $question->get_ref_object()->get_title()));
        
        $params[\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID] = 0;
        
        $trail->add(new Breadcrumb($this->get_url($params, $filters), Translation::get('Users')));
        
        $trail->add(
            new Breadcrumb(
                $this->get_url(), 
                DataManager::get_fullname_from_user(
                    $this->get_parameter(Manager::PARAM_USERS))));
    }
}
