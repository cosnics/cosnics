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
 * @package Chamilo\Libraries\Console\Command
 */
class PHPUnitGeneratorCommand extends Command
{
    public const OPT_INCLUDE_SOURCE = 'include-source';
    public const OPT_INCLUDE_SOURCE_SHORT = 's';

    protected PHPUnitGeneratorInterface $phpUnitGenerator;

    public function __construct(PHPUnitGeneratorInterface $phpUnitGenerator)
    {
        parent::__construct();

        $this->phpUnitGenerator = $phpUnitGenerator;
    }

    protected function configure()
    {
        $this->setName('chamilo:phpunit:generate-config')->setDescription(
                'Generates PHPUnit configuration based on the current installed packages'
            )->addOption(
                self::OPT_INCLUDE_SOURCE, self::OPT_INCLUDE_SOURCE_SHORT, InputOption::VALUE_NONE,
                'Includes the CheckSourceCode tests for every package'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $includeSource = $input->getOption(self::OPT_INCLUDE_SOURCE);
        $this->phpUnitGenerator->generate($includeSource);

        return 0;
    }
}