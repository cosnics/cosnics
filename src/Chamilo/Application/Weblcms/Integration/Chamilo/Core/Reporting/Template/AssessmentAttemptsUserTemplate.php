<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AssessmentAttemptsUserBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AssessmentUserInformationBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package application.weblcms.php.reporting.templates Reporting template with an overview of the assessment attempts
 *          of one user
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
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

    public function initialize_parameters()
    {
        $this->publication_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);
        $this->user_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);

        if ($this->publication_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION, $this->publication_id);
        }

        if ($this->user_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_USERS, $this->user_id);
        }

        $course_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE);
        if ($course_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE, $course_id);
        }
    }

    /**
     * Adds the breadcrumbs to the breadcrumbtrail
     */
    protected function add_breadcrumbs()
    {
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

        $assessment = $publication->get_content_object();

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

        $filters = array(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);

        $params = array();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] = AssessmentAttemptsTemplate::class_name();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $this->publication_id;

        $trail->add(new Breadcrumb($this->get_url($params, $filters), $assessment->get_title()));

        $params[\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID] = 1;

        $trail->add(new Breadcrumb($this->get_url($params, $filters), Translation::get('Users')));

        $trail->add(
            new Breadcrumb(
                $this->get_url(),
                \Chamilo\Core\User\Storage\DataManager::get_fullname_from_user($this->user_id)
            )
        );
    }
}
