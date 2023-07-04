<?php
namespace Chamilo\Core\Install;

use Chamilo\Configuration\Package\Action\Installer;
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Configuration\Package\Sequencer;
use Chamilo\Core\Install\Exception\InstallFailedException;
use Chamilo\Core\Install\Observer\InstallerObserver;
use Chamilo\Core\Install\Service\ConfigurationWriter;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\DependencyInjection\ExtensionFinder\PackagesContainerExtensionFinder;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesClassFinder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @package Chamilo\Core\Install
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class PlatformInstaller
{

    protected Filesystem $filesystem;

    protected SystemPathBuilder $systemPathBuilder;

    /**
     * @var \Chamilo\Core\Install\Configuration
     */
    private $configuration;

    /**
     * @var string
     */
    private $configurationFilePath;

    /**
     * @var install\DataManagerInterface the storage manager used to perform install
     */
    private $dataManager;

    /**
     * @var \Chamilo\Core\Install\Observer\InstallerObserver
     */
    private $installerObserver;

    /**
     * @var string[]
     */
    private $packages;

    public function __construct(
        InstallerObserver $installerObserver, Configuration $configuration, $dataManager,
        SystemPathBuilder $systemPathBuilder, Filesystem $filesystem
    )
    {
        $this->installerObserver = $installerObserver;
        $this->configuration = $configuration;
        $this->dataManager = $dataManager;
        $this->systemPathBuilder = $systemPathBuilder;
        $this->filesystem = $filesystem;

        $this->configurationFilePath = $systemPathBuilder->getStoragePath() . 'configuration/configuration.xml';
        $this->packages = [];
    }

    public function run()
    {
        $this->initializeInstallation();

        echo $this->installerObserver->beforeInstallation();
        flush();

        echo $this->performPreProduction();
        $this->loadConfiguration();
        flush();

        try
        {
            $this->installPackages();
        }
        catch (InstallFailedException $exception)
        {
            $stepResult = new StepResult(false, $exception->getMessage(), $exception->getPackage());

            echo $this->installerObserver->beforePackageInstallation($exception->getPackage());
            echo $this->installerObserver->afterPackageInstallation($stepResult);

            return;
        }

        echo $this->installerObserver->afterInstallation();
        flush();
    }

    /**
     * @param string $context
     */
    public function addPackage($context)
    {
        array_push($this->packages, $context);
    }

    /**
     * @param string[] $packages
     */
    public function addPackages($packages)
    {
        foreach ($packages as $package)
        {
            $this->addPackage($package);
        }
    }

    private function createFolders()
    {
        $html = [];

        $html[] = $this->installerObserver->beforeFilesystemPrepared();

        $values = $this->configuration->as_values_array();

        $directories = [
            $values['archive_path'],
            $values['cache_path'],
            $values['garbage_path'],
            $values['repository_path'],
            $values['temp_path'],
            $values['userpictures_path'],
            $values['scorm_path'],
            $values['logs_path'],
            $values['hotpotatoes_path']
        ];

        foreach ($directories as $directory)
        {
            if (!file_exists($directory))
            {
                try
                {
                    $this->getFilesystem()->mkdir($directory);
                }
                catch (Exception)
                {
                    throw new Exception(Translation::get('FoldersCreatedFailed'));
                }
            }
        }

        $publicFilesPath = $this->getSystemPathBuilder()->getPublicStoragePath();

        try
        {
            $this->getFilesystem()->mkdir($publicFilesPath);
        }
        catch (Exception)
        {
            throw new Exception(Translation::get('FoldersCreatedFailed'));
        }

        $html[] = $this->installerObserver->afterFilesystemPrepared(
            new StepResult(true, Translation::get('FoldersCreatedSuccess'))
        );

        return implode(PHP_EOL, $html);
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * @return string
     */
    public function getNextPackage()
    {
        return array_shift($this->packages);
    }

    /**
     * @return string[]
     */
    public function getPackages()
    {
        return $this->packages;
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    private function initializeInstallation()
    {
        Translation::getInstance()->setLanguageIsocode($this->configuration->get_platform_language());
    }

    private function installPackages()
    {
        $this->addPackages($this->configuration->get_packages());
        $this->orderPackages();
        $html = [];

        echo $this->installerObserver->beforePackagesInstallation();
        flush();

        while (($package = $this->getNextPackage()) != null)
        {

            $values = $this->configuration->as_values_array();
            $installer = Installer::factory($package, $values);

            $success = $installer->run();

            if ($success !== true)
            {
                throw new InstallFailedException($package, implode(PHP_EOL, $html), $installer->retrieve_message());
            }
            else
            {
                $isIntegrationPackage = StringUtilities::getInstance()->createString($package)->contains(
                    '\Integration\\', true
                );

                if (!$isIntegrationPackage)
                {
                    echo $this->installerObserver->beforePackageInstallation($package);
                    echo $this->installerObserver->afterPackageInstallation(
                        new StepResult($success, $installer->get_message(), $package)
                    );

                    flush();
                }
            }
        }

        echo $this->installerObserver->afterPackagesInstallation();
        flush();
    }

    private function loadConfiguration()
    {
        $platformPackageBundles = new PlatformPackageBundles();
        $packages = array_keys($platformPackageBundles->get_packages());

        $containerExtensionFinder = new PackagesContainerExtensionFinder(
            new PackagesClassFinder(new SystemPathBuilder(new ClassnameUtilities(new StringUtilities())), $packages)
        );

        $dependencyInjectionContainerBuilder = DependencyInjectionContainerBuilder::getInstance();
        $dependencyInjectionContainerBuilder->rebuildContainer(null, $containerExtensionFinder);
    }

    public function orderPackages()
    {
        $sequencer = new Sequencer($this->packages);
        $this->packages = $sequencer->run();
    }

    /**
     * @return string
     */
    private function performPreProduction()
    {
        $html = [];

        $html[] = $this->installerObserver->beforePreProduction();
        $html[] = $this->installerObserver->afterPreProductionDatabaseCreated(
            new StepResult($this->dataManager->initializeStorage(), Translation::get('DatabaseCreated'))
        );
        $html[] = $this->createFolders();
        $html[] = $this->installerObserver->afterPreProductionConfigurationFileWritten($this->writeConfigurationFile());
        $html[] = $this->installerObserver->afterPreProduction();

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\Install\Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param string $configurationFilePath
     */
    public function setConfigurationFilePath($configurationFilePath)
    {
        $this->configurationFilePath = $configurationFilePath;
    }

    /**
     * @param unknown $dataManager
     */
    public function setDataManager($dataManager)
    {
        $this->dataManager = $dataManager;
    }

    /**
     * @param string[] $packages
     */
    public function setPackages($packages)
    {
        $this->packages = $packages;
    }

    private function writeConfigurationFile()
    {
        $pathBuilder = new SystemPathBuilder(ClassnameUtilities::getInstance());

        try
        {
            $configurationTemplatePath =
                $pathBuilder->getTemplatesPath('Chamilo\Core\Install') . 'configuration.xml.tpl';

            $configurationWriter = new ConfigurationWriter($this->getFilesystem(), $configurationTemplatePath);
            $configurationWriter->writeConfiguration($this->configuration, $this->configurationFilePath);

            $result = true;
        }
        catch (Exception $exception)
        {
            $result = false;
        }

        return new StepResult($result, Translation::get($result ? 'ConfigWriteSuccess' : 'ConfigWriteFailed'));
    }
}
