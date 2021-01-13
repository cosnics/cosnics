<?php
namespace Chamilo\Libraries\Console\Command;

use Chamilo\Libraries\File\Path;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to generate the phpunit configuration file for all the chamilo packages individually
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @package Chamilo\Libraries\Console\Command
 */
class PackagesPHPUnitGeneratorCommand extends Command
{

    /**
     *
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * PackagesPHPUnitGeneratorCommand constructor.
     *
     * @param \Twig\Environment $twig
     */
    public function __construct(\Twig\Environment $twig)
    {
        parent::__construct();

        $this->twig = $twig;
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('chamilo:phpunit:generate-packages-config')->setDescription(
            'Generates PHPUnit for every package in the system');
    }

    /**
     * Executes the current command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input An InputInterface instance
     * @param \Symfony\Component\Console\Output\OutputInterface $output An OutputInterface instance
     * @return null
     *
     * @see Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packages = \Chamilo\Configuration\Package\PlatformPackageBundles::getInstance()->get_package_list()->get_list(
            true);

        foreach ($packages as $packageContext => $package)
        {
            $packagePath = Path::getInstance()->namespaceToFullPath($packageContext);
            $testPath = $packagePath . 'Test';
            $phpUnitFile = $testPath . DIRECTORY_SEPARATOR . 'phpunit.xml';

            $sourcePathExists = $integrationPathExists = $unitPathExists = false;

            $sourcePath = $testPath . DIRECTORY_SEPARATOR . 'Source';
            if (is_dir($sourcePath))
            {
                $sourcePathExists = true;
            }

            $integrationPath = $testPath . DIRECTORY_SEPARATOR . 'Integration';
            if (is_dir($integrationPath))
            {
                $output->writeln('[INTEGRATION] ' . $packageContext);
                $integrationPathExists = true;
            }

            $unitPath = $testPath . DIRECTORY_SEPARATOR . 'Unit';
            if (is_dir($unitPath))
            {
                $output->writeln('[UNIT] ' . $packageContext);
                $unitPathExists = true;
            }

            if (file_exists($phpUnitFile) || (! $sourcePathExists && ! $integrationPathExists && ! $unitPathExists))
            {
                continue;
            }

            $output->writeln($phpUnitFile);

            $packageParts = explode('\\', $packageContext);
            $bootstrapPath = str_repeat('../', (count($packageParts) + 1)) .
                 'Chamilo/Libraries/Architecture/Test/bootstrap.php';

            $phpunitContent = $this->twig->render(
                'Chamilo\Libraries:PHPUnitGenerator/package_phpunit.xml.twig',
                [
                    'SourcePathExists' => $sourcePathExists,
                    'IntegrationPathExists' => $integrationPathExists,
                    'UnitPathExists' => $unitPathExists,
                    'BootstrapPath' => $bootstrapPath]);

            file_put_contents($phpUnitFile, $phpunitContent);
        }

        return null;
    }
}
