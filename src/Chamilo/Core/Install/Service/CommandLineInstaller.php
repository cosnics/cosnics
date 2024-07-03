<?php
namespace Chamilo\Core\Install\Service;

use Chamilo\Core\Install\Architecture\Domain\StepResult;
use Chamilo\Core\Install\Architecture\Interfaces\InstallerObserverInterface;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class CommandLineInstaller implements InstallerObserverInterface
{
    use DependencyInjectionContainerTrait;

    private string $configurationFile;

    public function __construct($configurationFile)
    {
        $this->configurationFile = $configurationFile;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\ConnectionException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Exception
     */
    public function run(): void
    {
        $installer = $this->getInstallerFactory()->getInstallerFromArray(
            $this, $this->getConfigurationValuesFromFile($this->configurationFile)
        );
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
        if ($result->isSuccessful())
        {
            return 'Ok';
        }

        $reason = implode(', ', $result->getMessages());

        return "Ko ($reason)";
    }

    /**
     * @throws \Exception
     */
    protected function getConfigurationValuesFromFile(string $configurationFile): array
    {
        $fileContainer = new ContainerBuilder();
        $xmlFileLoader = new XmlFileLoader(
            $fileContainer, new FileLocator($configurationFile)
        );

        $xmlFileLoader->load($configurationFile);

        return $fileContainer->getParameterBag()->all();
    }

    protected function getInstallerFactory(): PlatformInstallerFactory
    {
        return $this->getService(PlatformInstallerFactory::class);
    }
}
