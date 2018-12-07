<?php

namespace Chamilo\Core\Queue\Component;

use Chamilo\Core\Queue\Manager;
use Chamilo\Core\Queue\Service\JobEntityManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Core\Queue\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FailedJobsBrowserComponent extends Manager
{

    /**
     * @return string
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    function run()
    {
        if(!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $retryURL = $this->get_url(
            [self::PARAM_ACTION => self::ACTION_RETRY_FAILED_JOB, self::PARAM_JOB_ID => '__JOB_ID__']
        );

        $failedJobs = $this->getJobEntityManager()->findFailedJobs();

        return $this->getTwig()->render(
            Manager::context() . ':FailedJobsBrowser.html.twig',
            [
                'FAILED_JOBS' => $failedJobs,
                'HEADER' => $this->render_header(),
                'FOOTER' => $this->render_footer(),
                'RETRY_URL' => $retryURL
            ]
        );
    }

    /**
     * @return JobEntityManager
     */
    protected function getJobEntityManager()
    {
        return $this->getService(JobEntityManager::class);
    }
}