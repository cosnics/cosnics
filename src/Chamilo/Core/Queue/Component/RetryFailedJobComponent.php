<?php

namespace Chamilo\Core\Queue\Component;

use Chamilo\Core\Queue\Manager;
use Chamilo\Core\Queue\Service\FailedJobExecutor;
use Chamilo\Core\Queue\Service\JobEntityManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @package Chamilo\Core\Queue\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RetryFailedJobComponent extends Manager
{

    /**
     * @return string
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    function run()
    {
        if (!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $jobId = $this->getRequest()->getFromUrl(self::PARAM_JOB_ID);
        try
        {
            $job = $this->getJobEntityManager()->findJob($jobId);
        }
        catch (\Exception $ex)
        {
            throw new ObjectNotExistException($this->getTranslator()->trans('Job', [], Manager::context()), $jobId);
        }

        try
        {
            $output = new NullOutput();
            $this->getFailedJobExecutor()->retryJob($job, $output);
        }
        catch (\Throwable $ex)
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = '<div class="alert alert-danger">' . $ex->getMessage() . '</div>';
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }

        $this->redirect(
            $this->getTranslator()->trans('JobRetrySuccessful', [], Manager::PARAM_CONTEXT), false,
            [self::PARAM_ACTION => self::ACTION_BROWSE_FAILED_JOBS]
        );

        return '';
    }

    /**
     * @return \Chamilo\Core\Queue\Service\JobEntityManager
     */
    protected function getJobEntityManager()
    {
        return $this->getService(JobEntityManager::class);
    }

    /**
     * @return \Chamilo\Core\Queue\Service\FailedJobExecutor
     */
    protected function getFailedJobExecutor()
    {
        return $this->getService(FailedJobExecutor::class);
    }
}