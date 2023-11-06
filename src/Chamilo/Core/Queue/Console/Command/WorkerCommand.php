<?php

namespace Chamilo\Core\Queue\Console\Command;

use Chamilo\Core\Queue\Service\Worker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Queue\Console\Command
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class WorkerCommand extends Command
{
    const ARG_QUEUE = 'queue';

    /**
     * @var Worker
     */
    protected $worker;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * WorkerCommand constructor.
     *
     * @param \Chamilo\Core\Queue\Service\Worker $worker
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(Worker $worker, Translator $translator)
    {
        $this->worker = $worker;
        $this->translator = $translator;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('chamilo:queue:worker')
            ->addArgument(
                self::ARG_QUEUE, InputArgument::REQUIRED,
                $this->translator->trans('QueueWorkerCommandQueueArgDescription', [], 'Chamilo\Core\Queue')
            )
            ->setDescription($this->translator->trans('QueueWorkerCommandDescription', [], 'Chamilo\Core\Queue'));
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
        $this->worker->waitForJobAndExecute($input->getArgument(self::ARG_QUEUE));

        return 0;
    }

}