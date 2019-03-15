<?php
namespace Chamilo\Libraries\Console\Command\Vendor\PHPStan;

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
    /**
     * @var \Chamilo\Libraries\Console\Command\Vendor\PHPStan\PHPStanPackages
     */
    protected $PHPStanPackages;

    /**
     * AnalyseCommand constructor.
     *
     * @param \Chamilo\Libraries\Console\Command\Vendor\PHPStan\PHPStanPackages $PHPStanPackages
     */
    public function __construct(PHPStanPackages $PHPStanPackages)
    {
        parent::__construct();
        $this->PHPStanPackages = $PHPStanPackages;
    }

    protected function configure(): void
    {
        parent::configure();
        $this->setName('phpstan:analyse')
            ->setDescription('Analyses source code (uses a chamilo extension')
            ->addOption('package', 'p', InputOption::VALUE_REQUIRED, 'Limit the analysis to a single Chamilo package')
            ->addOption('list-packages', 'y', InputOption::VALUE_NONE, 'Lists the available packages to choose from')
            ->addOption('inspect-package', 'i', InputOption::VALUE_REQUIRED, 'Inspects the configured sources for a given package');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if(!empty($input->getOption('list-packages')))
        {
            $namespaces = $this->PHPStanPackages->getNamespaces();
            foreach($namespaces as $namespace)
            {
                $output->writeln('<info>' . $namespace . '</info>');
            }

            return 0;
        }

        if(!empty($input->getOption('inspect-package')))
        {
            $package = $input->getOption('inspect-package');
            $package = $this->normalizePackageName($package);

            try
            {
                $paths = $this->PHPStanPackages->getPathsForNamespace($package);
                foreach($paths as $path)
                {
                    $output->writeln('<info>' . $path . '</info>');
                }

                return 0;
            }
            catch(\Exception $ex)
            {
                $output->writeln(sprintf('<error>The package %s could not be found in the list of available paths</error>', $package));
                return 1;
            }
        }

        $package = $input->getOption('package');
        if($package)
        {
            $package = $this->normalizePackageName($package);
            $output->writeln(sprintf('<info>Analysing all sources for package %s</info>', $package));

            try
            {
                $paths = $this->PHPStanPackages->getPathsForNamespace($package);
                foreach($paths as $path)
                {
                    $output->writeln($path);
                }

                $output->writeln('');

                $input->setArgument('paths', $paths);
            }
            catch(\Exception $ex)
            {
                $output->writeln(sprintf('<error>The package %s could not be found in the list of available paths</error>', $package));
                return 1;
            }

        }

        if(empty($package) && empty($input->getArgument('paths')))
        {
            $output->writeln('<info>Analysing all sources</info>');

            $paths = $this->PHPStanPackages->getAllPaths();
            $input->setArgument('paths', $paths);
        }

        return parent::execute($input, $output);
    }

    /**
     * @param string $package
     *
     * @return string
     */
    protected function normalizePackageName(string $package)
    {
        $package = str_replace('src/', '', $package);
        $package = str_replace('/', '\\', $package);
        if(substr($package, -1) == '\\')
        {
            $package = substr($package, 0, -1);
        }

        return $package;
    }
}