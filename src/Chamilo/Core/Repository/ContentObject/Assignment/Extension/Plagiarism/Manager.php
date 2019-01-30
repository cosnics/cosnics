<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\EphorusServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\EphorusComponent;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\ExtensionComponent;
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
    const ACTION_RETRY_CHECK_PLAGIARISM = 'RetryCheckPlagiarism';

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
}