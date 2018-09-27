<?php

namespace Chamilo\Core\Queue\Console\Command;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Domain\EntryNotificationJobParameters;
use Chamilo\Core\Queue\Service\EchoProcessor;
use Chamilo\Core\Queue\Service\JobProducer;
use Chamilo\Core\Queue\Storage\Entity\Job;
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
class ProducerCommand extends Command
{
    const ARG_QUEUE = 'topic';
    const ARG_MESSAGE = 'message';

    /**
     * @var \Chamilo\Core\Queue\Service\JobProducer
     */
    protected $producer;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * WorkerCommand constructor.
     *
     * @param \Chamilo\Core\Queue\Service\JobProducer $producer
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(JobProducer $producer, Translator $translator)
    {
        $this->producer = $producer;
        $this->translator = $translator;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('chamilo:queue:producer')
            ->addArgument(
                self::ARG_QUEUE, InputArgument::REQUIRED,
                $this->translator->trans('QueueWorkerCommandDescription', [], 'Chamilo\Core\Queue')
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $job = new Job();
        $job->setProcessorClass(EchoProcessor::class);
        $job->setJobParameters(new EntryNotificationJobParameters(200));

        $this->producer->produceJob($job, $input->getArgument(self::ARG_QUEUE));
    }

}