<?php
namespace Chamilo\Core\Install\Architecture\Interfaces;

use Chamilo\Core\Install\Architecture\Domain\StepResult;

/**
 *
 * @package Chamilo\Core\Install\Observer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface InstallerObserverInterface
{

    public function beforeInstallation(): string;

    public function beforePreProduction(): string;

    public function afterPreProductionDatabaseCreated(StepResult $result): string;

    public function afterPreProductionConfigurationFileWritten(StepResult $result): string;

    public function afterPreProduction(): string;

    public function beforePackagesInstallation(): string;

    public function afterPackagesInstallation(): string;

    public function beforePackageInstallation(string $context): string;

    public function afterPackageInstallation(StepResult $result): string;

    public function beforeFilesystemPrepared(): string;

    public function afterFilesystemPrepared(StepResult $result): string;

    public function afterInstallation(): string;
}
