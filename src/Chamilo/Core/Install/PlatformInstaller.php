<?php
namespace Chamilo\Core\Install;

use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Configuration\Package\Sequencer;
use Chamilo\Core\Install\Exception\InstallFailedException;
use Chamilo\Core\Install\Observer\InstallerObserver;
use Chamilo\Libraries\DependencyInjection\ExtensionFinder\PackagesContainerExtensionFinder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesClassFinder;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use Chamilo\Core\Install\Service\ConfigurationWriter;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

/**
 *
 * @package Chamilo\Core\Install
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class PlatformInstaller
{

    /**
     *
     * @var \Chamilo\Core\Install\Configuration
     */
    private $configuration;

    /**
     *
     * @var string
     */
    private $configurationFilePath;

    /**
     *
     * @var \Chamilo\Core\Install\Observer\InstallerObserver
     */
    private $installerObserver;

    /**
     *
     * @var install\DataManagerInterface the storage manager used to perform install
     */
    private $dataManager;

    /**
     *
     * @var string[]
     */
    private $packages;

    /**
     *
     * @param \Chamilo\Core\Install\Observer\InstallerObserver $installerObserver
     * @param \Chamilo\Core\Install\ $configuration
     * @param unknown $dataManager
     */
    public function __construct(InstallerObserver $installerObserver, Configuration $configuration, $dataManager)
    {
        $this->installerObserver = $installerObserver;
        $this->configuration = $configuration;
        $this->dataManager = $dataManager;

        $this->configurationFilePath = Path::getInstance()->getStoragePath() . 'configuration/configuration.xml';
        $this->packages = array();
    }

    /**
     *
     * @param unknown $dataManager
     */
    public function setDataManager($dataManager)
    {
        $this->dataManager = $dataManager;
    }

    /**
     *
     * @param \Chamilo\Core\Install\Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     *
     * @param string $configurationFilePath
     */
    public function setConfigurationFilePath($configurationFilePath)
    {
        $this->configurationFilePath = $configurationFilePath;
    }

    /**
     *
     * @return string[]
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     *
     * @param string[] $packages
     */
    public function setPackages($packages)
    {
        $this->packages = $packages;
    }

    /**
     *
     * @param string $context
     */
    public function addPackage($context)
    {
        array_push($this->packages, $context);
    }

    /**
     *
     * @param string[] $packages
     */
    public function addPackages($packages)
    {
        foreach ($packages as $package)
        {
            $this->addPackage($package);
        }
    }

    public function orderPackages()
    {
        $sequencer = new Sequencer($this->packages);
        $this->packages = $sequencer->run();
    }

    /**
     *
     * @return string
     */
    public function getNextPackage()
    {
        return array_shift($this->packages);
    }

    public function run()
    {
        $this->initializeInstallation();

        echo $this->installerObserver->beforeInstallation();
        flush();

        echo $this->performPreProduction();
        flush();

        echo $this->performConfiguration();
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

    private function initializeInstallation()
    {
        Translation::getInstance()->setLanguageIsocode($this->configuration->get_platform_language());
    }

    /**
     *
     * @return string
     */
    private function performPreProduction()
    {
        $html = array();

        $html[] = $this->installerObserver->beforePreProduction();

        $result = new StepResult($this->dataManager->initializeStorage(), Translation::get('DatabaseCreated'));

        $html[] = $this->installerObserver->afterPreProductionDatabaseCreated($result);
        $html[] = $this->createFolders();
        $html[] = $this->installerObserver->afterPreProduction();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    private function performConfiguration()
    {
        return $this->installerObserver->afterPreProductionConfigurationFileWritten($this->writeConfigurationFile());
    }

    private function writeConfigurationFile()
    {
        $pathBuilder = new PathBuilder(ClassnameUtilities::getInstance());

        try
        {
            $configurationTemplatePath =
                $pathBuilder->getTemplatesPath('Chamilo\Core\Install') . 'configuration.xml.tpl';

            $configurationWriter = new ConfigurationWriter($configurationTemplatePath);
            $configurationWriter->writeConfiguration($this->configuration, $this->configurationFilePath);

            $platformPackageBundles = new PlatformPackageBundles();
            $packages = array_keys($platformPackageBundles->get_packages());

            $containerExtensionFinder = new PackagesContainerExtensionFinder(
                new PackagesClassFinder(new PathBuilder(new ClassnameUtilities(new StringUtilities())), $packages)
            );

            $dependencyInjectionContainerBuilder = DependencyInjectionContainerBuilder::getInstance();
            $dependencyInjectionContainerBuilder->rebuildContainer(null, $containerExtensionFinder);

            $result = true;
        }
        catch (\Exception $exception)
        {
            $result = false;
        }

        return new StepResult($result, Translation::get($result ? 'ConfigWriteSuccess' : 'ConfigWriteFailed'));
    }

    private function installPackages()
    {
        $this->addPackages($this->configuration->get_packages());
        $this->orderPackages();
        $html = array();

        echo $this->installerObserver->beforePackagesInstallation();
        flush();

        while (($package = $this->getNextPackage()) != null)
        {

            $values = $this->configuration->as_values_array();
            $installer = \Chamilo\Configuration\Package\Action\Installer::factory($package, $values);

            $success = $installer->run();

            if ($success !== true)
            {
                throw new InstallFailedException($package, implode(PHP_EOL, $html), $installer->retrieve_message());
            }
            else
            {
                $isIntegrationPackage = StringUtilities::getInstance()->createString($package)->contains(
                    '\Integration\\',
                    true
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

    private function createFolders()
    {
        $html = array();

        $html[] = $this->installerObserver->beforeFilesystemPrepared();

        $values = $this->configuration->as_values_array();

        $directories = array(
            $values['archive_path'],
            $values['cache_path'],
            $values['garbage_path'],
            $values['repository_path'],
            $values['temp_path'],
            $values['userpictures_path'],
            $values['scorm_path'],
            $values['logs_path'],
            $values['hotpotatoes_path']
        );

        foreach ($directories as $directory)
        {
            if (!file_exists($directory))
            {
                if (!Filesystem::create_dir($directory))
                {
                    throw new \Exception(Translation::get('FoldersCreatedFailed'));
                }
            }
        }

        $publicFilesPath = Path::getInstance()->getPublicStoragePath();

        if (!Filesystem::create_dir($publicFilesPath))
        {
            throw new \Exception(Translation::get('FoldersCreatedFailed'));
        }

        $html[] = $this->installerObserver->afterFilesystemPrepared(
            new StepResult(true, Translation::get('FoldersCreatedSuccess'))
        );

        return implode(PHP_EOL, $html);
    }
}
