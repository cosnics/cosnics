<?php
namespace Chamilo\Core\Install;

use Chamilo\Core\Install\Observer\InstallerObserver;
use Chamilo\Core\Install\Storage\DataManager;
use Chamilo\Libraries\File\SystemPathBuilder;

/**
 * @package Chamilo\Core\Install
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class Factory
{
    protected SystemPathBuilder $systemPathBuilder;

    public function __construct(SystemPathBuilder $systemPathBuilder)
    {
        $this->systemPathBuilder = $systemPathBuilder;
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

    public function getInstaller(InstallerObserver $installerObserver, Configuration $configuration)
    {
        $dataManager = $this->getDataManager($configuration);

        return new PlatformInstaller($installerObserver, $configuration, $dataManager, $this->getSystemPathBuilder());
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
