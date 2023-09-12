<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AssessmentOverviewBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AssessmentUserScoresBlock;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package application.weblcms.php.reporting.templates Reporting template with an overview of scores of each assessment
 *          per user
 * @author  Joris Willems <joris.willems@gmail.com>
 * @author  Alexander Van Paemel
 */
class AssessmentScoresTemplate extends ReportingTemplate
{

    public function __construct($parent)
    {
        parent::__construct($parent);

        $this->init_parameters();

        $this->add_breadcrumbs();

        // Calculate number of assessments
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
            ), new StaticConditionVariable($this->course_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
            ), new StaticConditionVariable(Assessment::class)
        );
        $condition = new AndCondition($conditions);

        $order_by = [
            new OrderProperty(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_MODIFIED_DATE
                )
            )
        ];

        $publications = DataManager::retrieve_content_object_publications(
            $condition, new OrderBy($order_by)
        );

        $this->size = $publications->count();

        foreach ($publications as $publication)
        {
            $this->th_titles[] = $publication->get_content_object()->get_title();
        }

        $this->add_reporting_block(new AssessmentUserScoresBlock($this, true));
        $this->add_reporting_block(new AssessmentOverviewBlock($this));
    }

    /**
     * Adds the breadcrumbs to the breadcrumbtrail
     */
    protected function add_breadcrumbs()
    {
        $trail = $this->getBreadcrumbTrail();

        $trail->add(
            new Breadcrumb(
                $this->get_url(
                    [\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID => 4], [Manager::PARAM_TEMPLATE_ID]
                ), Translation::get('LastAccessToToolsBlock')
            )
        );

        $trail->add(new Breadcrumb($this->get_url(), Translation::get('AssessmentScores')));
    }

    private function init_parameters()
    {
        $this->course_id = $this->getRequest()->query->get(Manager::PARAM_COURSE);
        if ($this->course_id)
        {
            $this->set_parameter(Manager::PARAM_COURSE, $this->course_id);
        }
        $sel = $this->getRequest()->request->get('sel', $this->getRequest()->query->get('sel'));
        if ($sel)
        {
            $this->set_parameter('sel', $sel);
        }
    }
}
