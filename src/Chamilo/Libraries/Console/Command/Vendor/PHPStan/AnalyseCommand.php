<?php
namespace Chamilo\Libraries\Console\Command\Vendor\PHPStan;

use Symfony\Component\Console\Input\InputInterface;
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