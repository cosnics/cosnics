<?php

namespace Chamilo\Core\Group\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GenerateGroupClosureTableCommand extends Command
{
    /**
     * @var \Chamilo\Core\Group\Service\GroupClosureTableGenerator
     */
    protected $groupClosureTableGenerator;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * GenerateGroupClosureTableCommand constructor.
     *
     * @param \Chamilo\Core\Group\Service\GroupClosureTableGenerator $groupClosureTableGenerator
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(
        \Chamilo\Core\Group\Service\GroupClosureTableGenerator $groupClosureTableGenerator, Translator $translator
    )
    {
        $this->groupClosureTableGenerator = $groupClosureTableGenerator;
        $this->translator = $translator;

        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('chamilo:groups:generate_closure_table')
            ->setDescription(
                $this->translator->trans('GenerateClosureTableCommandDescription', [], 'Chamilo\Core\Group')
            );
    }

    /**
     * Executes this command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->groupClosureTableGenerator->generate();
    }

}