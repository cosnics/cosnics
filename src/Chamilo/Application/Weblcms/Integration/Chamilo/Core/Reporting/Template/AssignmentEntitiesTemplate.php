<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentInformationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentEntitiesBlock;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.weblcms.php.reporting.templates Reporting template with information about the assignment and the
 *          users, course groups and platform groups the assignment is published for
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Anthony Hurst (Hogeschool Gent)
 */
class AssignmentEntitiesTemplate extends ReportingTemplate
{

    /**
     * @var int
     */
    protected $publicationId;

    public function __construct($parent)
    {
        parent::__construct($parent);

        $this->publicationId = Request::get(Manager::PARAM_PUBLICATION);

        $assignment = DataManager::retrieve_by_id(
            ContentObjectPublication::class,
            $this->publicationId
        )->get_content_object();

        $this->init_parameters();
        $this->add_reporting_block(new AssignmentInformationBlock($this));
        $this->add_reporting_block(new AssignmentEntitiesBlock($this));

        $breadcrumbTrail = BreadcrumbTrail::getInstance();

        $params = [];
        $params[Manager::PARAM_TEMPLATE_ID] =
            CourseStudentTrackerTemplate::class;
        $params[\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID] = 1;

        $breadcrumbTrail->add(
            new Breadcrumb(
                $this->get_url($params), Translation::getInstance()->getTranslation('AssignmentBlock')
            )
        );

        $breadcrumbTrail->add(
            new Breadcrumb(
                $this->get_url([], [\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID]), $assignment->get_title()
            )
        );

        $this->addCurrentBlockBreadcrumb();
    }

    private function init_parameters()
    {
        if ($this->publicationId)
        {
            $this->set_parameter(Manager::PARAM_PUBLICATION, $this->publicationId);
        }
    }
}
