<?php

namespace Chamilo\Libraries\Console\Command;

use Chamilo\Libraries\Architecture\Test\PHPUnitGenerator\PHPUnitGeneratorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to generate the global phpunit configuration file for Chamilo
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PHPUnitGeneratorCommand extends Command
{
    const OPT_INCLUDE_SOURCE = 'include-source';
    const OPT_INCLUDE_SOURCE_SHORT = 's';

    /**
     * The PHPUnitGenerator
     *
     * @var PHPUnitGeneratorInterface
     */
    protected $phpUnitGenerator;

    /**
     * Constructor
     *
     * @param PHPUnitGeneratorInterface $phpUnitGenerator
     */
    public function __construct(PHPUnitGeneratorInterface $phpUnitGenerator)
    {
        parent::__construct();

        $this->phpUnitGenerator = $phpUnitGenerator;
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('chamilo:phpunit:generate-config')
            ->setDescription('Generates PHPUnit configuration based on the current installed packages')
            ->addOption(
                self::OPT_INCLUDE_SOURCE, self::OPT_INCLUDE_SOURCE_SHORT, InputOption::VALUE_NONE,
                'Includes the CheckSourceCode tests for every package'
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null
     *
     * @see Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $includeSource = $input->getOption(self::OPT_INCLUDE_SOURCE);
        $this->phpUnitGenerator->generate($includeSource);

        return null;
    }
}