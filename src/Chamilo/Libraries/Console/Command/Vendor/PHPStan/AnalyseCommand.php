<?php

namespace Chamilo\Libraries\Console\Command\Vendor\PHPStan;

use PHPStan\Command\ErrorsConsoleStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Extension on the analyse command to work with configuration files in packages instead of directly with paths.
 * Use this command
 *
 * @package Chamilo\Libraries\Console\Command\Vendor\PHPStan
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AnalyseCommand extends \PHPStan\Command\AnalyseCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this->setName('phpstan:analyse')
            ->setDescription('Analyses source code (uses a chamilo extension');
//            ->setDefinition([
//                new InputOption('configuration', 'c', InputOption::VALUE_REQUIRED, 'Path to project configuration file'),
//                new InputOption(self::OPTION_LEVEL, 'l', InputOption::VALUE_REQUIRED, 'Level of rule options - the higher the stricter'),
//                new InputOption(ErrorsConsoleStyle::OPTION_NO_PROGRESS, null, InputOption::VALUE_NONE, 'Do not show progress bar, only results'),
//                new InputOption('debug', null, InputOption::VALUE_NONE, 'Show debug information - which file is analysed, do not catch internal errors'),
//                new InputOption('autoload-file', 'a', InputOption::VALUE_REQUIRED, 'Project\'s additional autoload file path'),
//                new InputOption('error-format', null, InputOption::VALUE_REQUIRED, 'Format in which to print the result of the analysis', 'table'),
//                new InputOption('memory-limit', null, InputOption::VALUE_REQUIRED, 'Memory limit for analysis'),
//            ]);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     * @throws \PHPStan\ShouldNotHappenException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $paths = ['src/Chamilo/Core/Group/Service', 'src/Chamilo/Core/Group/Repository'];
        $input->setArgument('paths', $paths);
        return parent::execute($input, $output);
    }
}