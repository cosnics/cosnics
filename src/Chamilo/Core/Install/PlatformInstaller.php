<?php
namespace Chamilo\Core\Install;

use Chamilo\Configuration\Package\Action\Installer;
use Chamilo\Configuration\Package\Action\PackageActionFactory;
use Chamilo\Configuration\Package\Sequencer;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Core\Install\Exception\InstallFailedException;
use Chamilo\Core\Install\Observer\InstallerObserver;
use Chamilo\Core\Install\Service\ConfigurationWriter;
use Chamilo\Core\Install\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\DependencyInjection\ExtensionFinder\PackagesContainerExtensionFinder;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesClassFinder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Install
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class PlatformInstaller
{

    protected Filesystem $filesystem;

    protected PackageActionFactory $installerFactory;

    protected PackageBundlesCacheService $packageBundlesCacheService;

    protected SystemPathBuilder $systemPathBuilder;

    protected Translator $translator;

    private Configuration $configuration;

    private string $configurationFilePath;

    private DataManager $dataManager;

    private InstallerObserver $installerObserver;

    /**
     * @var string[]
     */
    private array $packages;

    public function __construct(
        InstallerObserver $installerObserver, Configuration $configuration, $dataManager,
        SystemPathBuilder $systemPathBuilder, Filesystem $filesystem,
        PackageBundlesCacheService $packageBundlesCacheService, PackageActionFactory $installerFactory,
        Translator $translator
    )
    {
        $this->installerObserver = $installerObserver;
        $this->configuration = $configuration;
        $this->dataManager = $dataManager;
        $this->systemPathBuilder = $systemPathBuilder;
        $this->filesystem = $filesystem;
        $this->packageBundlesCacheService = $packageBundlesCacheService;
        $this->installerFactory = $installerFactory;
        $this->translator = $translator;

        $this->configurationFilePath = $systemPathBuilder->getStoragePath() . 'configuration/configuration.xml';
        $this->packages = [];
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Exception
     */
    public function run(): void
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

    public function addPackage(string $context): void
    {
        $this->packages[] = $context;
    }

    /**
     * @param string[] $packages
     */
    public function addPackages(array $packages): void
    {
        foreach ($packages as $package)
        {
            $this->addPackage($package);
        }
    }

    /**
     * @throws \Exception
     */
    private function createFolders(): string
    {
        $translator = $this->getTranslator();
        $values = $this->configuration->as_values_array();

        $html = [];

        $html[] = $this->installerObserver->beforeFilesystemPrepared();

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
                    throw new Exception($translator->trans('FoldersCreatedFailed', [], Manager::CONTEXT));
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
            throw new Exception($translator->trans('FoldersCreatedFailed', [], Manager::CONTEXT));
        }

        $html[] = $this->installerObserver->afterFilesystemPrepared(
            new StepResult(true, $translator->trans('FoldersCreatedSuccess', [], Manager::CONTEXT))
        );

        return implode(PHP_EOL, $html);
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    public function getNextPackage(): string
    {
        return array_shift($this->packages);
    }

    public function getPackageBundlesCacheService(): PackageBundlesCacheService
    {
        return $this->packageBundlesCacheService;
    }

    /**
     * @return string[]
     */
    public function getPackages(): array
    {
        return $this->packages;
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @throws \Exception
     */
    private function initializeInstallation(): void
    {
        $this->getTranslator()->setLocale($this->configuration->get_platform_language());
    }

    /**
     * @throws \Chamilo\Core\Install\Exception\InstallFailedException
     */
    private function installPackages(): void
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
                $isIntegrationPackage =
                    StringUtilities::getInstance()->createString($package)->contains('\Integration\\');

                if (!$isIntegrationPackage)
                {
                    echo $this->installerObserver->beforePackageInstallation($package);
                    echo $this->installerObserver->afterPackageInstallation(
                        new StepResult(true, $installer->get_message(), $package)
                    );

                    flush();
                }
            }
        }

        echo $this->installerObserver->afterPackagesInstallation();
        flush();
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     */
    private function loadConfiguration(): void
    {
        $packages = array_keys($this->packageBundlesCacheService->getAllPackages()->getNestedPackages());

        $containerExtensionFinder = new PackagesContainerExtensionFinder(
            new PackagesClassFinder(new SystemPathBuilder(new ClassnameUtilities(new StringUtilities())), $packages)
        );

        $dependencyInjectionContainerBuilder = DependencyInjectionContainerBuilder::getInstance();
        $dependencyInjectionContainerBuilder->rebuildContainer(null, $containerExtensionFinder);
    }

    public function orderPackages(): void
    {
        $sequencer = new Sequencer($this->packages);
        $this->packages = $sequencer->run();
    }

    /**
     * @throws \Exception
     */
    private function performPreProduction(): string
    {
        $html = [];

        $html[] = $this->installerObserver->beforePreProduction();
        $html[] = $this->installerObserver->afterPreProductionDatabaseCreated(
            new StepResult(
                $this->dataManager->initializeStorage(),
                $this->getTranslator()->trans('DatabaseCreated', [], Manager::CONTEXT)
            )
        );
        $html[] = $this->createFolders();
        $html[] = $this->installerObserver->afterPreProductionConfigurationFileWritten($this->writeConfigurationFile());
        $html[] = $this->installerObserver->afterPreProduction();

        return implode(PHP_EOL, $html);
    }

    public function setConfiguration(Configuration $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function setConfigurationFilePath(string $configurationFilePath): void
    {
        $this->configurationFilePath = $configurationFilePath;
    }

    public function setDataManager(DataManager $dataManager): void
    {
        $this->dataManager = $dataManager;
    }

    /**
     * @param string[] $packages
     */
    public function setPackages(array $packages): void
    {
        $this->packages = $packages;
    }

    private function writeConfigurationFile(): StepResult
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
        catch (Exception)
        {
            $result = false;
        }

        return new StepResult(
            $result,
            $this->getTranslator()->trans($result ? 'ConfigWriteSuccess' : 'ConfigWriteFailed', [], Manager::CONTEXT)
        );
    }
}
