<?php
namespace Chamilo\Core\Install\Observer\Type;

use Chamilo\Core\Install\Configuration;
use Chamilo\Core\Install\Factory;
use Chamilo\Core\Install\Observer\InstallerObserver;
use Chamilo\Core\Install\StepResult;

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

        $installer_factory = new Factory();
        $this->installer = $installer_factory->build_installer($installer_config);
        $this->installer->add_observer($this);
        $this->installer->perform_install();
    }

    private function check_result(StepResult $result)
    {
        if ($result->get_success())
        {
            echo "Ok";
            ob_flush();
            return;
        }

        $reason = implode(", ", $result->get_messages());
        echo "Ko ({$reason})";
        ob_flush();
    }

    public function before_install()
    {
        echo "install started ...\n\n";
    }

    public function before_preprod()
    {
        echo "\tPRE-PRODUCTION\n";
    }

    public function preprod_db_created(StepResult $result)
    {
        echo "\t\t DB created ... " . $this->check_result($result) . "\n";
    }

    public function preprod_config_file_written(StepResult $result)
    {
        echo "\t\t Config File Written ... " . $this->check_result($result) . "\n";
    }

    public function after_preprod()
    {
        echo "\n";
        ob_flush();
    }

    public function before_filesystem_prepared()
    {
        echo "\tFILE SYSTEM PREPARATION\n";
    }

    public function after_filesystem_prepared(StepResult $result)
    {
        echo "\t\t File system prepared ... " . $this->check_result($result) . "\n";
    }

    public function after_install()
    {
        echo "\n\nInstallation completed !\n";
        ob_flush();
    }

    public function before_packages_install()
    {
        echo "\PACKAGES INSTALLATION\n";
    }

    public function after_packages_install()
    {
        echo "\n";
        ob_flush();
    }

    public function before_package_install($context)
    {
        echo "\t\t Installing package {$context} ... ";
    }

    public function after_package_install(StepResult $result)
    {
        echo $this->check_result($result) . "\n";
        ob_flush();
    }
}
