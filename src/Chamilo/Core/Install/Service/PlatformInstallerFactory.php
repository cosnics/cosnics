<?php
namespace Chamilo\Core\Install\Service;

use Chamilo\Configuration\Package\Action\PackageActionFactory;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Core\Install\Architecture\Interfaces\InstallerObserverInterface;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\FileExceptionLogger;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Database\StorageUnitDatabase;
use Chamilo\Libraries\Storage\DataManager\Doctrine\DataSourceName;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Install
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class PlatformInstallerFactory
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

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     */
    public function getInstaller(InstallerObserverInterface $installerObserver, array $configurationValues): PlatformInstaller
    {
        $storageUnitRepository = $this->getStorageUnitRepository($configurationValues);

        return new PlatformInstaller(
            $installerObserver, $configurationValues, $storageUnitRepository, $this->getSystemPathBuilder(),
            $this->getFilesystem(), $this->getPackageBundlesCacheService(), $this->getPackageActionFactory(),
            $this->getTranslator()
        );
    }

    /**
     * @param string[][] $values
     *
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     */
    public function getInstallerFromArray(InstallerObserverInterface $installerObserver, array $values): PlatformInstaller
    {
        return $this->getInstaller($installerObserver, $values);
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
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     * @throws \Exception
     */
    public function getStorageUnitRepository(array $configurationValues): StorageUnitRepository
    {
        $connectionFactory = new ConnectionFactory(
            new DataSourceName(
                [
                    'driver' => $configurationValues['database']['driver'],
                    'username' => $configurationValues['database']['username'],
                    'password' => $configurationValues['database']['password'],
                    'host' => $configurationValues['database']['host'],
                    'port' => $configurationValues['database']['port'],
                    'name' => $configurationValues['database']['name'],
                    'charset' => $configurationValues['database']['charset']
                ]
            )
        );

        $connection = $connectionFactory->getConnection();
        $storageAliasGenerator = new StorageAliasGenerator(new ClassnameUtilities(new StringUtilities()));
        $exceptionLogger =
            new FileExceptionLogger($this->getSystemPathBuilder()->getStoragePath() . 'installer.database.log');

        return new StorageUnitRepository(
            new StorageUnitDatabase($connection, $storageAliasGenerator, $exceptionLogger)
        );
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
