<?php
namespace Chamilo\Core\Install\Observer;

use Chamilo\Core\Install\StepResult;

interface InstallerObserver
{

    public function before_install();

    public function before_preprod();

    public function preprod_db_created(StepResult $result);

    public function preprod_config_file_written(StepResult $result);

    public function after_preprod();

    public function before_packages_install();

    public function after_packages_install();

    public function before_package_install($context);

    public function after_package_install(StepResult $result);

    public function before_filesystem_prepared();

    public function after_filesystem_prepared(StepResult $result);

    public function after_install();
}
