<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Renderer\ReportRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service\RequestManager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\EphorusComponent;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use RuntimeException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_CHANGE_INDEX_VISIBILITY = 'IndexVisibilityChanger';
    public const ACTION_CREATE = 'Creator';
    public const ACTION_EXPORT_RESULT = 'ResultExporter';
    public const ACTION_PUBLISH_LATEST_DOCUMENTS = 'LatestDocumentsPublisher';
    public const ACTION_VIEW_RESULT = 'ResultViewer';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE;
    public const EPHORUS_TRANSLATION_CONTEXT = 'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus';

    public const PARAM_ACTION = 'assignment_ephorus_action';
    public const PARAM_ENTRY_ID = 'entry_id';
    public const PARAM_SOURCE = 'source';

    /**
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        if (!$this->get_application() instanceof EphorusComponent)
        {
            throw new RuntimeException(
                'This extension can only be run from within the assignment application with the EphorusComponent'
            );
        }
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment|\Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function getAssignment()
    {
        return $this->getEphorusComponent()->getAssignment();
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider |
     *     \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentEphorusSupportInterface
     */
    public function getDataProvider(): AssignmentDataProvider
    {
        return $this->getEphorusComponent()->getDataProvider();
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Application\Application | EphorusComponent
     */
    public function getEphorusComponent()
    {
        return $this->get_application();
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
}