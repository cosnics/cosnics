<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Renderer\ReportRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service\RequestManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;

/**
 * This class represents the manager for the ephorus tool
 *
 * @author Pieterjan Broekaert - Hogeschool Gent
 * @author Tom Goethals - Hogeschool Gent
 * @author Vanpoucke Sven - Hogeschool Gent
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements IntroductionTextSupportInterface
{
    public const ACTION_CREATE = 'Creator';
    public const ACTION_EPHORUS_REQUEST = 'EphorusRequest';
    public const ACTION_EXPORT_RESULT = 'ResultExporter';
    public const ACTION_INDEX_VISIBILITY_CHANGER = 'IndexVisibilityChanger';
    public const ACTION_PUBLISH_DOCUMENT = 'DocumentPublisher';
    public const ACTION_VIEW_RESULT = 'ResultViewer';

    public const CONTEXT = __NAMESPACE__;

    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public const PARAM_CONTENT_OBJECT_IDS = 'co_ids';
    public const PARAM_REQUEST_IDS = 'req_ids';
    public const PARAM_TREE_NODE_ID = 'tree_node_id';

    /**
     * @return bool
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function canUseEphorus()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT);
    }

    /**
     * @return \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository
     */
    public function getContentObjectRepository()
    {
        return $this->getService(ContentObjectRepository::class);
    }

    /**
     * Returns the request guids whose visibilities should be changed
     *
     * @return Request[]
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function getEphorusRequestsFromRequest()
    {
        $requestTranslation = $this->getTranslator()->trans('Request', [], \Chamilo\Core\Repository\Manager::context());

        $ids = $this->getRequest()->getFromPostOrUrl(self::PARAM_REQUEST_IDS);

        if (!$ids)
        {
            throw new NoObjectSelectedException($requestTranslation);
        }

        if (!is_array($ids))
        {
            $ids = [$ids];
        }

        $ids = (array) $ids;

        $requests = [];
        foreach ($ids as $id)
        {
            $request = $this->getRequestManager()->findRequestById($id);

            if (!$request)
            {
                throw new ObjectNotExistException($requestTranslation, $id);
            }

            $requests[] = $request;
        }

        return $requests;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Renderer\ReportRenderer
     */
    public function getReportRenderer()
    {
        return $this->getService(ReportRenderer::class);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service\RequestManager
     */
    public function getRequestManager()
    {
        return $this->getService(RequestManager::class);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function validateAccess()
    {
        if (!$this->canUseEphorus())
        {
            throw new NotAllowedException();
        }
    }
}
