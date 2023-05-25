<?php
namespace Chamilo\Core\Install\Observer\Type;

use Chamilo\Core\Install\Configuration;
use Chamilo\Core\Install\Factory;
use Chamilo\Core\Install\Observer\InstallerObserver;
use Chamilo\Core\Install\StepResult;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;

class CommandLineInstaller implements InstallerObserver
{

    private $config_file;

    private $installer;

    public function __construct($config_file)
    {
        $this->config_file = $config_file;
    }

    public function run()
    {
        $installer_config = new Configuration();
        $installer_config->load_config_file($this->config_file);

        $installer_factory = new Factory(new SystemPathBuilder(new ClassnameUtilities(new StringUtilities())));
        $this->installer = $installer_factory->build_installer($installer_config);
        $this->installer->add_observer($this);
        $this->installer->perform_install();
    }

    public function afterFilesystemPrepared(StepResult $result)
    {
        echo "\t\t File system prepared ... " . $this->check_result($result) . PHP_EOL;
    }

    public function afterInstallation()
    {
        echo "\n\nInstallation completed !\n";
        ob_flush();
    }

    public function afterPackageInstallation(StepResult $result)
    {
        echo $this->check_result($result) . PHP_EOL;
        ob_flush();
    }

    public function afterPackagesInstallation()
    {
        echo PHP_EOL;
        ob_flush();
    }

    public function afterPreProduction()
    {
        echo PHP_EOL;
        ob_flush();
    }

    public function afterPreProductionConfigurationFileWritten(StepResult $result)
    {
        echo "\t\t Config File Written ... " . $this->check_result($result) . PHP_EOL;
    }

    public function afterPreProductionDatabaseCreated(StepResult $result)
    {
        echo "\t\t DB created ... " . $this->check_result($result) . PHP_EOL;
    }

    public function beforeFilesystemPrepared()
    {
        echo "\tFILE SYSTEM PREPARATION\n";
    }

    public function beforeInstallation()
    {
        echo "install started ...\n\n";
    }

    public function beforePackageInstallation($context)
    {
        echo "\t\t Installing package {$context} ... ";
    }

    public function beforePackagesInstallation()
    {
        echo "\PACKAGES INSTALLATION\n";
    }

    public function beforePreProduction()
    {
        echo "\tPRE-PRODUCTION\n";
    }

    private function check_result(StepResult $result)
    {
        if ($result->get_success())
        {
            echo 'Ok';
            ob_flush();

            return;
        }

        $reason = implode(', ', $result->get_messages());
        echo "Ko ({$reason})";
        ob_flush();
    }
}
