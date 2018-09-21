<?php

namespace Chamilo\Core\Queue\Console\Command;

use Chamilo\Core\Queue\Service\Producer;
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
class ProducerCommand extends Command
{
    const ARG_TOPIC = 'topic';
    const ARG_MESSAGE = 'message';

    /**
     * @var \Chamilo\Core\Queue\Service\Producer
     */
    protected $producer;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * WorkerCommand constructor.
     *
     * @param \Chamilo\Core\Queue\Service\Producer $producer
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(Producer $producer, Translator $translator)
    {
        $this->producer = $producer;
        $this->translator = $translator;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('chamilo:queue:producer')
            ->addArgument(
                self::ARG_TOPIC, InputArgument::REQUIRED,
                $this->translator->trans('QueueWorkerCommandDescription', [], 'Chamilo\Core\Queue')
            )
            ->addArgument(
                self::ARG_MESSAGE, InputArgument::REQUIRED, 'message'
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\InvalidDestinationException
     * @throws \Interop\Queue\InvalidMessageException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->producer->sendMessage($input->getArgument(self::ARG_TOPIC), $input->getArgument(self::ARG_MESSAGE));
    }

}