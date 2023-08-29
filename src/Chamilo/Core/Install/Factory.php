<?php
namespace Chamilo\Core\Install;

use Chamilo\Configuration\Package\Action\PackageActionFactory;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Core\Install\Observer\InstallerObserver;
use Chamilo\Core\Install\Storage\DataManager;
use Chamilo\Libraries\File\SystemPathBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Install
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class Factory
{
    protected Filesystem $filesystem;

    protected PackageActionFactory $packageActionFactory;

    protected PackageBundlesCacheService $packageBundlesCacheService;

    protected SystemPathBuilder $systemPathBuilder;

    protected Translator $translator;

    public function __construct(
        SystemPathBuilder $systemPathBuilder, Filesystem $filesystem,
        PackageBundlesCacheService $packageBundlesCacheService, PackageActionFactory $packageActionFactory,
        Translator $translator
    )
    {
        $this->systemPathBuilder = $systemPathBuilder;
        $this->filesystem = $filesystem;
        $this->packageBundlesCacheService = $packageBundlesCacheService;
        $this->packageActionFactory = $packageActionFactory;
        $this->translator = $translator;
    }

    public function buildConfigurationFromArray(array $values): Configuration
    {
        $configuration = new Configuration();
        $configuration->load_array($values);

        return $configuration;
    }

    public function getDataManager(Configuration $configuration): DataManager
    {
        return new DataManager($configuration);
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    public function getInstaller(InstallerObserver $installerObserver, Configuration $configuration): PlatformInstaller
    {
        $dataManager = $this->getDataManager($configuration);

        return new PlatformInstaller(
            $installerObserver, $configuration, $dataManager, $this->getSystemPathBuilder(), $this->getFilesystem(),
            $this->getPackageBundlesCacheService(), $this->getPackageActionFactory(), $this->getTranslator()
        );
    }

    public function getInstallerFromArray(InstallerObserver $installerObserver, array $values): PlatformInstaller
    {
        return $this->getInstaller($installerObserver, $this->buildConfigurationFromArray($values));
    }

    public function getPackageActionFactory(): PackageActionFactory
    {
        return $this->packageActionFactory;
    }

    public function getPackageBundlesCacheService(): PackageBundlesCacheService
    {
        return $this->packageBundlesCacheService;
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
