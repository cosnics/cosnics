<?php

namespace Chamilo\Core\Queue;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * @package Chamilo\Core\Queue
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    const ACTION_BROWSE_FAILED_JOBS = 'FailedJobsBrowser';
    const ACTION_RETRY_FAILED_JOB = 'RetryFailedJob';

    const DEFAULT_ACTION = self::ACTION_BROWSE_FAILED_JOBS;

    const PARAM_JOB_ID = 'JobId';

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->checkAuthorization(Manager::context());
    }
}