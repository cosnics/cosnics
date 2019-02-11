<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\ExtensionComponent;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    const PARAM_ACTION = 'PlagiarismAction';

    const ACTION_CHECK_PLAGIARISM = 'CheckPlagiarism';
    const ACTION_VIEW_PLAGIARISM_RESULT = 'ViewPlagiarismResult';
    const ACTION_BROWSE = 'Browser';
    const ACTION_CHECK_ALL_ENTRIES = 'CheckAllEntries';

    const DEFAULT_ACTION = self::ACTION_CHECK_PLAGIARISM;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        if (!$this->get_application() instanceof ExtensionComponent)
        {
            throw new \RuntimeException(
                'This extension can only be run from within the assignment application with the ExtensionComponent'
            );
        }
    }

    /**
     * @return EntryPlagiarismResultServiceBridgeInterface
     */
    protected function getEntryPlagiarismResultServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(EntryPlagiarismResultServiceBridgeInterface::class);
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface
     */
    protected function getAssignmentServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(AssignmentServiceBridgeInterface::class);
    }
}