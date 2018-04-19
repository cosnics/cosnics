<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\EphorusComponent;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    const PARAM_ACTION = 'assignment_ephorus_action';
    const PARAM_ENTRY_ID = 'entry_id';

    const ACTION_BROWSE = 'Browser';
    const ACTION_CREATE = 'Creator';
    const ACTION_CHANGE_INDEX_VISIBILITY = 'IndexVisibilityChanger';
    const ACTION_PUBLISH_LATEST_DOCUMENTS = 'LatestDocumentsPublisher';

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
            throw new \RuntimeException(
                'This extension can only be run from within the assignment application with the EphorusComponent'
            );
        }
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
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
}