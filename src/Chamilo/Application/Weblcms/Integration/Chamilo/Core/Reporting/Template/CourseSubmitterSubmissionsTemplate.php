<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentCourseGroupInformationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentPlatformGroupInformationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentSubmitterInformationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentEntriesBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentUserInformationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
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
        parent::__construct($parent);

        $this->init_parameters();
        $this->add_reporting_block(new AssignmentSubmitterInformationBlock($this));
        $this->add_reporting_block(new AssignmentEntriesBlock($this));
        
        $assignment = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $this->publication_id)->get_content_object();
        
        $custom_breadcrumbs = array();
        $params = array();
        $params[\Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_SUBMITTER_TYPE] = null;
        $params[\Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_TARGET_ID] = null;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] = CourseAssignmentSubmittersTemplate::class_name();
        $custom_breadcrumbs[] = new Breadcrumb($this->get_url($params), $assignment->get_title());
        $custom_breadcrumbs[] = new Breadcrumb($this->get_url(), $this->getEntityServiceForEntityType($this->submitter_type)->renderEntityNameById($this->target_id));
        $this->set_custom_breadcrumb_trail($custom_breadcrumbs);
    }

    private function init_parameters()
    {
        $this->publication_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);
        if ($this->publication_id)
        {
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION, $this->publication_id);
        }
        
        $this->target_id = Request::get(
            \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_TARGET_ID);
        if ($this->target_id)
        {
            $this->set_parameter(
                \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_TARGET_ID, 
                $this->target_id);
        }
        
        $this->submitter_type = Request::get(
            \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_SUBMITTER_TYPE);
        if (isset($this->submitter_type))
        {
            $this->set_parameter(
                \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_SUBMITTER_TYPE, 
                $this->submitter_type);
        }
    }

    /**
     * @param int $entityType
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity\EntityServiceInterface
     */
    protected function getEntityServiceForEntityType($entityType)
    {
        switch($entityType)
        {
            case Entry::ENTITY_TYPE_COURSE_GROUP:
                return $this->getCourseGroupEntityService();
            case Entry::ENTITY_TYPE_PLATFORM_GROUP:
                return $this->getPlatformGroupEntityService();
            case Entry::ENTITY_TYPE_USER:
            default:
                return $this->getUserEntityService();

        }
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity\UserEntityService
     */
    protected function getUserEntityService()
    {
        return $this->getService(
            'chamilo.application.weblcms.tool.implementation.assignment.service.entity.user_entity_service'
        );
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity\PlatformGroupEntityService
     */
    protected function getPlatformGroupEntityService()
    {
        return $this->getService(
            'chamilo.application.weblcms.tool.implementation.assignment.service.entity.platform_group_entity_service'
        );
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity\CourseGroupEntityService
     */
    protected function getCourseGroupEntityService()
    {
        return $this->getService(
            'chamilo.application.weblcms.tool.implementation.assignment.service.entity.course_group_entity_service'
        );
    }
}
