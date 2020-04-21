<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Renderer\ReportRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service\RequestManager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\EphorusComponent;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use RuntimeException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    const EPHORUS_TRANSLATION_CONTEXT = 'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus';

    const PARAM_ACTION = 'assignment_ephorus_action';
    const PARAM_ENTRY_ID = 'entry_id';
    const PARAM_SOURCE = 'source';

    const ACTION_BROWSE = 'Browser';
    const ACTION_CREATE = 'Creator';
    const ACTION_CHANGE_INDEX_VISIBILITY = 'IndexVisibilityChanger';
    const ACTION_PUBLISH_LATEST_DOCUMENTS = 'LatestDocumentsPublisher';
    const ACTION_VIEW_RESULT = 'ResultViewer';
    const ACTION_EXPORT_RESULT = 'ResultExporter';

    const DEFAULT_ACTION = self::ACTION_BROWSE;

    /**
     *
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
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider |
     *     \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentEphorusSupportInterface
     */
    public function getDataProvider()
    {
        return $this->getEphorusComponent()->getDataProvider();
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment|\Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function getAssignment()
    {
        return $this->getEphorusComponent()->getAssignment();
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Application\Application | EphorusComponent
     */
    public function getEphorusComponent()
    {
        return $this->get_application();
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service\RequestManager
     */
    public function getRequestManager()
    {
        return $this->getService(RequestManager::class);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Renderer\ReportRenderer
     */
    public function getReportRenderer()
    {
        return $this->getService(ReportRenderer::class);
    }
}