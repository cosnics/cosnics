<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AssessmentQuestionAttemptsBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AssessmentQuestionInformationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AssessmentQuestionUsersBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\QuestionOptions\AssessmentQuestionOptionsBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Reporting\Viewer\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package application.weblcms.php.reporting.templates Reporting template with an overview of the scores of an
 *          assessment question per user
 * @author  Joris Willems <joris.willems@gmail.com>
 * @author  Alexander Van Paemel
 */
class AssessmentQuestionUsersTemplate extends ReportingTemplate
{

    public function __construct($parent)
    {
        parent::__construct($parent);

        $this->initialize_parameters();

        $this->add_reporting_block(new AssessmentQuestionInformationBlock($this));
        $this->add_reporting_block(AssessmentQuestionOptionsBlock::factory($this->getRequest(), $this));
        $this->add_reporting_block(new AssessmentQuestionUsersBlock($this));
        $this->add_reporting_block(new AssessmentQuestionAttemptsBlock($this));

        $this->add_breadcrumbs();
    }

    /**
     * Adds the breadcrumbs to the breadcrumbtrail
     */
    protected function add_breadcrumbs()
    {
        $assessment = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class, $this->publication_id
        )->get_content_object();

        $question = DataManager::retrieve_by_id(
            ComplexContentObjectItem::class, $this->question_id
        );

        $trail = BreadcrumbTrail::getInstance();

        $trail->add(
            new Breadcrumb(
                $this->get_url(
                    [Manager::PARAM_BLOCK_ID => 2], [\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID]
                ), Translation::get('Assessments')
            )
        );

        $params = [];
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] = AssessmentAttemptsTemplate::class;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $this->publication_id;

        $trail->add(new Breadcrumb($this->get_url($params), $assessment->get_title()));

        $params[Manager::PARAM_BLOCK_ID] = 2;

        $trail->add(new Breadcrumb($this->get_url($params), Translation::get('Questions')));

        $trail->add(new Breadcrumb($this->get_url(), $question->get_ref_object()->get_title()));
    }

    private function initialize_parameters()
    {
        $this->question_id = $this->getRequest()->query->get(
            \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::PARAM_QUESTION
        );
        if ($this->question_id)
        {
            $this->set_parameter(
                \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::PARAM_QUESTION, $this->question_id
            );
        }

        $this->publication_id =
            $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);
        if ($this->publication_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION, $this->publication_id);
        }

        $this->user_id = $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);
        if ($this->user_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_USERS, $this->user_id);
        }
    }
}
