<?php

namespace Chamilo\Libraries\Console\Command;

use Chamilo\Libraries\Format\Utilities\ResourceProcessor;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\Translator;

/**
 * Command to process resources of all (or a given) package(s)
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ProcessResourcesCommand extends ChamiloCommand
{
    const ARG_CONTEXT = 'context';

    /**
     * @var ResourceProcessor
     */
    protected $resourceProcessor;

    /**
     * ProcessResourcesCommand constructor.
     *
     * @param Translator $translator
     * @param ResourceProcessor $resourceProcessor
     */
    public function __construct(Translator $translator, ResourceProcessor $resourceProcessor)
    {
        $this->resourceProcessor = $resourceProcessor;

        parent::__construct($translator);
    }

    /**
     * Configures this command
     */
    protected function configure()
    {
        $this->setName('chamilo:process_resources')
            ->setDescription(
                $this->translator->trans('ProcessResourcesCommandDescription', array(), 'Chamilo\Libraries')
            )
            ->addArgument(
                self::ARG_CONTEXT, InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                $this->translator->trans('ProcessResourcesContextDescription', array(), 'Chamilo\Libraries')
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packages = $input->getArgument(self::ARG_CONTEXT);
        $this->resourceProcessor->processResources($packages, $output);
    }
}
