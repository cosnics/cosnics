<?php
namespace Chamilo\Core\Install\Observer;

use Chamilo\Core\Install\Configuration;
use Chamilo\Core\Install\Factory;
use Chamilo\Core\Install\StepResult;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;

class CommandLineInstaller implements InstallerObserver
{
    use DependencyInjectionContainerTrait;

    private string $configurationFile;

    public function __construct($configurationFile)
    {
        $this->configurationFile = $configurationFile;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Exception
     */
    public function run(): void
    {
        $installer_config = new Configuration();
        $installer_config->load_config_file($this->configurationFile);

        $installer = $this->getInstallerFactory()->getInstallerFromArray($this, $installer_config->as_values_array());
        $installer->run();
    }

    public function afterFilesystemPrepared(StepResult $result): string
    {
        return "\t\t File system prepared ... " . $this->checkResult($result) . PHP_EOL;
    }

    public function afterInstallation(): string
    {
        return "\n\nInstallation completed !\n";
    }

    public function afterPackageInstallation(StepResult $result): string
    {
        return $this->checkResult($result) . PHP_EOL;
    }

    public function afterPackagesInstallation(): string
    {
        return PHP_EOL;
    }

    public function afterPreProduction(): string
    {
        return PHP_EOL;
    }

    public function afterPreProductionConfigurationFileWritten(StepResult $result): string
    {
        return "\t\t Config File Written ... " . $this->checkResult($result) . PHP_EOL;
    }

    public function afterPreProductionDatabaseCreated(StepResult $result): string
    {
        return "\t\t DB created ... " . $this->checkResult($result) . PHP_EOL;
    }

    public function beforeFilesystemPrepared(): string
    {
        return "\tFILE SYSTEM PREPARATION\n";
    }

    public function beforeInstallation(): string
    {
        return "install started ...\n\n";
    }

    public function beforePackageInstallation($context): string
    {
        return "\t\t Installing package $context ... ";
    }

    public function beforePackagesInstallation(): string
    {
        return "\PACKAGES INSTALLATION\n";
    }

    public function beforePreProduction(): string
    {
        return "\tPRE-PRODUCTION\n";
    }

    private function checkResult(StepResult $result): string
    {
        if ($result->get_success())
        {
            return 'Ok';
        }

        $reason = implode(', ', $result->get_messages());

        return "Ko ($reason)";
    }

    protected function getInstallerFactory(): Factory
    {
        return $this->getService(Factory::class);
    }
}
