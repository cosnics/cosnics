<?php
namespace Chamilo\Core\Install\Service;

use Chamilo\Configuration\Package\Action\PackageActionFactory;
use Chamilo\Configuration\Package\Sequencer;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Core\Install\Architecture\Domain\StepResult;
use Chamilo\Core\Install\Architecture\Interfaces\InstallerObserverInterface;
use Chamilo\Core\Install\Exception\InstallFailedException;
use Chamilo\Core\Install\Manager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\DependencyInjection\ExtensionFinder\PackagesContainerExtensionFinder;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesClassFinder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
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

    protected PackageActionFactory $packageActionFactory;

    protected PackageBundlesCacheService $packageBundlesCacheService;

    protected SystemPathBuilder $systemPathBuilder;

    protected Translator $translator;

    private string $configurationFilePath;

    /**
     * @var string[][] $configurationValues
     */
    private array $configurationValues;

    private InstallerObserverInterface $installerObserver;

    /**
     * @var string[]
     */
    private array $packages;

    private StorageUnitRepository $storageUnitRepository;

    public function __construct(
        InstallerObserverInterface $installerObserver, array $configurationValues, StorageUnitRepository $storageUnitRepository,
        SystemPathBuilder $systemPathBuilder, Filesystem $filesystem,
        PackageBundlesCacheService $packageBundlesCacheService, PackageActionFactory $installerFactory,
        Translator $translator
    )
    {
        $this->installerObserver = $installerObserver;
        $this->configurationValues = $configurationValues;
        $this->storageUnitRepository = $storageUnitRepository;
        $this->systemPathBuilder = $systemPathBuilder;
        $this->filesystem = $filesystem;
        $this->packageBundlesCacheService = $packageBundlesCacheService;
        $this->packageActionFactory = $installerFactory;
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
            $stepResult = new StepResult(false, [$exception->getMessage()], $exception->getPackage());

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
        $values = $this->configurationValues;

        $html = [];

        $html[] = $this->installerObserver->beforeFilesystemPrepared();

        $directories = $values['path'];

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
            new StepResult(true, [$translator->trans('FoldersCreatedSuccess', [], Manager::CONTEXT)])
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

    public function getPackageActionFactory(): PackageActionFactory
    {
        return $this->packageActionFactory;
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
        $this->getTranslator()->setLocale($this->configurationValues['platform_language']);
    }

    /**
     * @throws \Chamilo\Core\Install\Exception\InstallFailedException
     */
    private function installPackages(): void
    {
        $this->addPackages($this->configurationValues['packages']);
        $this->orderPackages();
        $html = [];

        echo $this->installerObserver->beforePackagesInstallation();
        flush();

        while (($package = $this->getNextPackage()) != null)
        {
            $installer = $this->getPackageActionFactory()->getPackageInstaller($package);

            $success = $installer->run($this->configurationValues);

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
                $this->storageUnitRepository->initializeStorage(
                    $this->configurationValues['database']['name'],
                    (bool) $this->configurationValues['database']['overwrite']
                ), [$this->getTranslator()->trans('DatabaseCreated', [], Manager::CONTEXT)]
            )
        );
        $html[] = $this->createFolders();
        $html[] = $this->installerObserver->afterPreProductionConfigurationFileWritten($this->writeConfigurationFile());
        $html[] = $this->installerObserver->afterPreProduction();

        return implode(PHP_EOL, $html);
    }

    public function setConfigurationFilePath(string $configurationFilePath): void
    {
        $this->configurationFilePath = $configurationFilePath;
    }

    /**
     * @param string[][] $configurationValues
     *
     * @return void
     */
    public function setConfigurationValues(array $configurationValues): void
    {
        $this->configurationValues = $configurationValues;
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
            $configurationWriter->writeConfiguration($this->configurationValues, $this->configurationFilePath);

            $result = true;
        }
        catch (Exception)
        {
            $result = false;
        }

        return new StepResult(
            $result,
            [$this->getTranslator()->trans($result ? 'ConfigWriteSuccess' : 'ConfigWriteFailed', [], Manager::CONTEXT)]
        );
    }
}
