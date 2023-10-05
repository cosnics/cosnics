<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AssessmentAttemptsUserBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AssessmentUserInformationBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Reporting\Viewer\Manager;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package application.weblcms.php.reporting.templates Reporting template with an overview of the assessment attempts
 *          of one user
 * @author  Joris Willems <joris.willems@gmail.com>
 * @author  Alexander Van Paemel
 */
class AssessmentAttemptsUserTemplate extends ReportingTemplate
{

    public function __construct($parent)
    {
        parent::__construct($parent);

        $this->initialize_parameters();
        $this->add_reporting_block(new AssessmentUserInformationBlock($this));
        $this->add_reporting_block(new AssessmentAttemptsUserBlock($this));

        $this->add_breadcrumbs();
    }

    /**
     * Adds the breadcrumbs to the breadcrumbtrail
     */
    protected function add_breadcrumbs()
    {
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class, $this->publication_id
        );

        if (!$publication instanceof ContentObjectPublication)
        {
            throw new ObjectNotExistException(
                Translation::getInstance()->getTranslation(
                    'ContentObjectPublication', null, 'Chamilo\Application\Weblcms'
                ), $this->publication_id
            );
        }

        $assessment = $publication->get_content_object();

        $trail = $this->getBreadcrumbTrail();

        $trail->add(
            new Breadcrumb(
                $this->get_url(
                    [Manager::PARAM_BLOCK_ID => 2], [\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID]
                ), Translation::get('Assessments')
            )
        );

        $filters = [\Chamilo\Application\Weblcms\Manager::PARAM_USERS];

        $params = [];
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] = AssessmentAttemptsTemplate::class;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $this->publication_id;

        $trail->add(new Breadcrumb($this->get_url($params, $filters), $assessment->get_title()));

        $params[Manager::PARAM_BLOCK_ID] = 1;

        $trail->add(new Breadcrumb($this->get_url($params, $filters), Translation::get('Users')));

        $trail->add(
            new Breadcrumb(
                $this->get_url(), $this->getUserService()->getUserFullNameByIdentifier($this->user_id)
            )
        );
    }

    public function initialize_parameters()
    {
        $this->publication_id =
            $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);
        $this->user_id = $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);

        if ($this->publication_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION, $this->publication_id);
        }

        if ($this->user_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_USERS, $this->user_id);
        }

        $course_id = $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE);
        if ($course_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE, $course_id);
        }
    }
}
