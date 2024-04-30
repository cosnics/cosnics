<?php

namespace Chamilo\Libraries\Console\Command;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Use this command for testing purposes
 */
class TestCommand extends \Symfony\Component\Console\Command\Command
{
    protected Serializer $symfonySerializer;

    public function __construct(Serializer $symfonySerializer)
    {
        $this->symfonySerializer = $symfonySerializer;
    }

    protected function configure()
    {
        $this->setName('chamilo:test');
    }

    /**
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return 0;
    }


}