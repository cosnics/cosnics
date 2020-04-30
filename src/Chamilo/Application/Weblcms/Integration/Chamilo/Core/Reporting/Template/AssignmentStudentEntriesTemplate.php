<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentEntityInformationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentEntriesBlock;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.weblcms.php.reporting.templates Reporting template with an overview of the assignment
 *          submissions from a user/group
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class AssignmentStudentEntriesTemplate extends ReportingTemplate
{
    /**
     * @var int
     */
    protected $publicationId;

    /**
     * @var int
     */
    protected $entityId;

    /**
     * @var int
     */
    protected $entityType;

    public function __construct($parent)
    {
        parent::__construct($parent);

        $this->init_parameters();
        $this->add_reporting_block(new AssignmentEntityInformationBlock($this));
        $this->add_reporting_block(new AssignmentEntriesBlock($this));

        /** @var \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment */
        $assignment = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class,
            $this->publicationId
        )->get_content_object();

        $params = [];
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] =
            CourseStudentTrackerTemplate::class;
        $params[\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID] = 0;

        $breadcrumbTrail = BreadcrumbTrail::getInstance();

        $breadcrumbTrail->add(
            new Breadcrumb(
                $this->get_url($params), Translation::getInstance()->getTranslation('UsersTrackingBlock')
            )
        );

        $user = DataManager::retrieve_by_id(
            User::class,
            (int) $this->entityId
        );

        if ($user)
        {
            $params = [];
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] =
                CourseStudentTrackerDetailTemplate::class;
            $params[\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID] = 0;

            BreadcrumbTrail::getInstance()->add(new Breadcrumb($this->get_url($params), $user->get_fullname()));
        }

        $params = [];
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] =
            CourseStudentTrackerDetailTemplate::class;
        $params[\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID] = 1;

        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(
                $this->get_url($params),
                Translation::getInstance()->getTranslation('CourseUserAssignmentInformationBlock')
            )
        );

        $this->registerParameters();

        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(
                $this->get_url([], [\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID]),
                $assignment->get_title()
            )
        );

        $this->addCurrentBlockBreadcrumb();
    }

    /**
     *
     */
    private function init_parameters()
    {
        $this->publicationId = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);
        if ($this->publicationId)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION, $this->publicationId);
        }

        $this->entityId = $this->getEntityId();
        $this->entityType = $this->getEntityType();
    }

    /**
     * @return \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager
     */
    protected function getEntityServiceManager()
    {
        return $this->getService(
            'Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager'
        );
    }

    /**
     * Retrieves the submitter type from the url.
     *
     * @return int the submitter type.
     */
    public function getEntityType()
    {
        return $this->getRequest()->getFromUrl(
            Manager::PARAM_ENTITY_TYPE,
            Entry::ENTITY_TYPE_USER
        );
    }

    /**
     * Retrieves the target id from the url.
     *
     * @return int the target id.
     */
    public function getEntityId()
    {
        return $this->getRequest()->getFromUrl(
            Manager::PARAM_ENTITY_ID
        );
    }

    /**
     *
     */
    protected function registerParameters()
    {
        $this->set_parameter(
            Manager::PARAM_ENTITY_TYPE, $this->entityType
        );

        $this->set_parameter(
            Manager::PARAM_ENTITY_ID, $this->entityId
        );
    }
}
