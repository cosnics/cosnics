<?php
namespace Chamilo\Core\Install;

use Chamilo\Core\Install\Observer\InstallerObserver;
use Chamilo\Core\Install\Storage\DataManager;
use Chamilo\Libraries\File\SystemPathBuilder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @package Chamilo\Core\Install
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class Factory
{
    protected Filesystem $filesystem;

    protected SystemPathBuilder $systemPathBuilder;

    public function __construct(SystemPathBuilder $systemPathBuilder, Filesystem $filesystem)
    {
        $this->systemPathBuilder = $systemPathBuilder;
        $this->filesystem = $filesystem;
    }

    public function buildConfigurationFromArray(array $values)
    {
        $configuration = new Configuration();
        $configuration->load_array($values);

        return $configuration;
    }

    public function getDataManager(Configuration $configuration)
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

        return new PlatformInstaller($installerObserver, $configuration, $dataManager, $this->getSystemPathBuilder(), $this->getFilesystem());
    }

    public function getInstallerFromArray(InstallerObserver $installerObserver, array $values)
    {
        return $this->getInstaller($installerObserver, $this->buildConfigurationFromArray($values));
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }
}
