<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus;

use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;

/**
 * This class represents the manager for the ephorus tool
 * 
 * @author Pieterjan Broekaert - Hogeschool Gent
 * @author Tom Goethals - Hogeschool Gent
 * @author Vanpoucke Sven - Hogeschool Gent
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements IntroductionTextSupportInterface
{
    const ACTION_PUBLISH_DOCUMENT = 'DocumentPublisher';
    const ACTION_EPHORUS_REQUEST = 'EphorusRequest';
    const ACTION_ASSIGNMENT_EPHORUS_REQUEST = 'AssignmentEphorusRequest';
    const ACTION_PUBLISH_LATEST_DOCUMENTS = 'AssignmentLatestDocumentsPublisher';
    const ACTION_ASSIGNMENT_BROWSER = 'AssignmentBrowser';
    const ACTION_INDEX_VISIBILITY_CHANGER = 'IndexVisibilityChanger';
    // const ACTION_HIDE_ON_INDEX = 'IndexHider';
    const PARAM_CONTENT_OBJECT_IDS = 'co_ids';
    const PARAM_REQUEST_IDS = 'req_ids';
    const PARAM_TREE_NODE_ID = 'tree_node_id';

    const DEFAULT_ACTION = self::ACTION_BROWSE;

    const PARAM_SOURCE = 'source';
    const SOURCE_ASSIGNMENT = 'Assignment';
    const SOURCE_LEARNING_PATH_ASSIGNMENT = 'LearningPathAssignment';
    const SOURCE_DEFAULT = 'default';

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->getRequest()->getFromUrl(self::PARAM_SOURCE, self::SOURCE_DEFAULT);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository\AssignmentRequestRepository
     */
    public function getAssignmentRequestRepository()
    {
        return $this->getService('chamilo.application.weblcms.tool.implementation.ephorus.storage.repository.assignment_request_repository');
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service\RequestManager
     */
    public function getRequestManager()
    {
        return $this->getService('chamilo.application.weblcms.tool.implementation.ephorus.service.request_manager');
    }
}
