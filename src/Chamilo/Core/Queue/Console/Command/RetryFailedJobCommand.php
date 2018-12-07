<?php

namespace Chamilo\Core\Queue\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package Chamilo\Core\Queue\Console\Command
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RetryFailedJobCommand extends Command
{
    /**
     * @var \Chamilo\Core\Queue\Service\FailedJobExecutor
     */
    protected $failedJobExecutor;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * RetryFailedJobCommand constructor.
     *
     * @param \Chamilo\Core\Queue\Service\FailedJobExecutor $failedJobExecutor
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(
        \Chamilo\Core\Queue\Service\FailedJobExecutor $failedJobExecutor,
        \Symfony\Component\Translation\Translator $translator
    )
    {
        $this->failedJobExecutor = $failedJobExecutor;
        $this->translator = $translator;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('chamilo:queue:retry_failed_job')
            ->setDescription($this->translator->trans('RetryFailedJobCommandDescription', [], 'Chamilo\Core\Queue'));
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null|void
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->failedJobExecutor->retryFirstFailedJob($output);
    }

}