<?php

namespace Chamilo\Libraries\Console\Command\Vendor\PHPStan;

use PHPStan\Command\AnalyseApplication;
use PHPStan\Command\CommandHelper;
use PHPStan\Command\ErrorFormatter\ErrorFormatter;
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
class AnalyseCommand extends \Symfony\Component\Console\Command\Command
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
            ->setDescription('Analyses source code (uses a chamilo extension)')
            ->addArgument(
                'paths', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Paths with source code to run analysis on'
            )
            ->addOption(
                'level', 'l', InputOption::VALUE_REQUIRED,
                'Level of rule options - the higher the stricter (only used when using paths manually, otherwise the configured level is used)',
                1
            )
            ->addOption('package', 'p', InputOption::VALUE_REQUIRED, 'Limit the analysis to a single Chamilo package')
            ->addOption('list-packages', 'y', InputOption::VALUE_NONE, 'Lists the available packages to choose from')
            ->addOption(
                'inspect-package', 'i', InputOption::VALUE_REQUIRED,
                'Inspects the configured sources for a given package'
            )
            ->addOption(
                ErrorsConsoleStyle::OPTION_NO_PROGRESS, null, InputOption::VALUE_NONE,
                'Do not show progress bar, only results'
            );
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
        if (!empty($input->getOption('list-packages')))
        {
            return $this->listPackages($output);
        }

        if (!empty($input->getOption('inspect-package')))
        {
            return $this->inspectPackage($input, $output);
        }

        $packageName = $input->getOption('package');
        if (!empty($packageName))
        {
            return $this->handlePackage($input, $output, $packageName);
        }

        $paths = $input->getArgument('paths');
        if (!empty($paths))
        {
            $level = $input->getOption('level');

            $output->writeln(
                sprintf('<info>Analysing all sources for paths %s (level %s)</info>', implode(', ', $paths), $level)
            );

            return $this->analysePaths($input, $output, $paths, $level);
        }

        $packages = $this->PHPStanPackages->getPackageNames();
        $returnCode = 0;

        foreach ($packages as $packageName)
        {
            $packageReturnCode = $this->handlePackage($input, $output, $packageName);
            if ($packageReturnCode != 0)
            {
                $returnCode = 1;
            }
        }

        return $returnCode;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string $packageName
     *
     * @return int
     */
    protected function handlePackage(InputInterface $input, OutputInterface $output, string $packageName)
    {
        $packageName = $this->normalizePackageName($packageName);

        try
        {
            $phpStanPackage = $this->PHPStanPackages->getPackage($packageName);
            $paths = $phpStanPackage->getPaths();

            $output->writeln(
                sprintf(
                    '<info>Analysing all sources for package %s (level %s)</info>', $packageName,
                    $phpStanPackage->getLevel()
                )
            );

            foreach ($paths as $path)
            {
                $output->writeln($path);
            }

            $output->writeln('');

            return $this->analysePaths($input, $output, $paths, $phpStanPackage->getLevel());
        }
        catch (\Exception $ex)
        {
            $output->writeln(
                sprintf('<error>The package %s could not be found in the list of available paths</error>', $packageName)
            );

            return 1;
        }
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param array $paths
     * @param int $level
     *
     * @return int
     * @throws \PHPStan\ShouldNotHappenException
     */
    protected function analysePaths(InputInterface $input, OutputInterface $output, array $paths, int $level)
    {
        try
        {
            $inceptionResult = CommandHelper::begin($input, $output, $paths, null, null, null, null, $level);
        }
        catch (\PHPStan\Command\InceptionNotSuccessfulException $e)
        {
            return 1;
        }

        $container = $inceptionResult->getContainer();

        /** @var ErrorFormatter $errorFormatter */
        $errorFormatter = $container->getService('errorFormatter.table');

        /** @var AnalyseApplication $application */
        $application = $container->getByType(AnalyseApplication::class);

        return $inceptionResult->handleReturn(
            $application->analyse(
                $inceptionResult->getFiles(),
                $inceptionResult->isOnlyFiles(),
                $inceptionResult->getConsoleStyle(),
                $errorFormatter,
                $inceptionResult->isDefaultLevelUsed(),
                false
            )
        );
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
        if (substr($package, - 1) == '\\')
        {
            $package = substr($package, 0, - 1);
        }

        return $package;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function listPackages(OutputInterface $output): int
    {
        $namespaces = $this->PHPStanPackages->getPackageNames();
        foreach ($namespaces as $namespace)
        {
            $output->writeln('<info>' . $namespace . '</info>');
        }

        return 0;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function inspectPackage(InputInterface $input, OutputInterface $output): int
    {
        $package = $input->getOption('inspect-package');
        $package = $this->normalizePackageName($package);

        try
        {
            $phpStanPackage = $this->PHPStanPackages->getPackage($package);
            $paths = $phpStanPackage->getPaths();

            $output->writeln('<info>Package:</info>');
            $output->writeln($phpStanPackage->getPackageName());
            $output->writeln('');
            $output->writeln('<info>Level: </info>');
            $output->writeln($phpStanPackage->getLevel());
            $output->writeln('');
            $output->writeln('<info>Paths:</info>');

            foreach ($paths as $path)
            {
                $output->writeln('- ' . $path);
            }

            return 0;
        }
        catch (\Exception $ex)
        {
            $output->writeln(
                sprintf('<error>The package %s could not be found in the list of available paths</error>', $package)
            );

            return 1;
        }
    }
}