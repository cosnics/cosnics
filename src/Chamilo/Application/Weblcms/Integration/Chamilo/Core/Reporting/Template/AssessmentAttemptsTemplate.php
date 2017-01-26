<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AssessmentAttemptsBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AssessmentInformationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AssessmentQuestionsBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AssessmentQuestionsUsersBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AssessmentUsersBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.php.reporting.templates Reporting template with information about the assessment, the
 *          attempts per user and questions score stats
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class AssessmentAttemptsTemplate extends ReportingTemplate
{

    public function __construct($parent)
    {
        parent::__construct($parent);

        $this->course_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE);
        if ($this->course_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE, $this->course_id);
        }

        $this->publication_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);
        if ($this->publication_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION, $this->publication_id);
        }

        $sel = (Request::post('sel')) ? Request::post('sel') : Request::get('sel');
        if ($sel)
        {
            $this->set_parameter('sel', $sel);
        }

        // Retrieve the questions of the assessment
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $this->publication_id
        );

        if (!$publication instanceof ContentObjectPublication)
        {
            throw new ObjectNotExistException(
                Translation::getInstance()->getTranslation(
                    'ContentObjectPublication', null, 'Chamilo\Application\Weblcms'
                ), $this->publication_id
            );
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class_name(),
                ComplexContentObjectItem::PROPERTY_PARENT
            ),
            new StaticConditionVariable($publication->get_content_object_id())
        );
        $questions_resultset = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class_name(),
            new DataClassRetrievesParameters($condition)
        );

        while ($question = $questions_resultset->next_result())
        {
            $this->th_titles[] = $question->get_ref_object()->get_title();
        }

        $this->add_reporting_block(new AssessmentInformationBlock($this));
        $this->add_reporting_block(new AssessmentQuestionsBlock($this));
        $this->add_reporting_block(new AssessmentUsersBlock($this));
        $this->add_reporting_block(new AssessmentAttemptsBlock($this));
        $this->add_reporting_block(new AssessmentQuestionsUsersBlock($this, true));

        $this->add_breadcrumbs();
    }

    /**
     * Adds the breadcrumbs to the breadcrumbtrail
     */
    protected function add_breadcrumbs()
    {
        $assessment = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $this->publication_id
        )->get_content_object();

        $trail = BreadcrumbTrail::getInstance();

        $trail->add(
            new Breadcrumb(
                $this->get_url(
                    array(\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID => 2),
                    array(\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID)
                ),
                Translation::get('Assessments')
            )
        );

        $trail->add(new Breadcrumb($this->get_url(), $assessment->get_title()));
    }
}
