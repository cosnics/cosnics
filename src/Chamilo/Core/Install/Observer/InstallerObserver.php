<?php
namespace Chamilo\Core\Install\Observer;

use Chamilo\Core\Install\StepResult;

/**
 *
 * @package Chamilo\Core\Install\Observer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface InstallerObserver
{

    /**
     *
     * @return string
     */
    public function beforeInstallation();

    /**
     *
     * @return string
     */
    public function beforePreProduction();

    /**
     *
     * @param \Chamilo\Core\Install\StepResult $result
     * @return string
     */
    public function afterPreProductionDatabaseCreated(StepResult $result);

    /**
     *
     * @param \Chamilo\Core\Install\StepResult $result
     * @return string
     */
    public function afterPreProductionConfigurationFileWritten(StepResult $result);

    /**
     *
     * @return string
     */
    public function afterPreProduction();

    /**
     *
     * @return string
     */
    public function beforePackagesInstallation();

    /**
     *
     * @return string
     */
    public function afterPackagesInstallation();

    /**
     *
     * @param string $context
     * @return string
     */
    public function beforePackageInstallation($context);

    /**
     *
     * @param \Chamilo\Core\Install\StepResult $result
     * @return string
     */
    public function afterPackageInstallation(StepResult $result);

    /**
     *
     * @return string
     */
    public function beforeFilesystemPrepared();

    /**
     *
     * @param \Chamilo\Core\Install\StepResult $result
     * @return string
     */
    public function afterFilesystemPrepared(StepResult $result);

    /**
     *
     * @return string
     */
    public function afterInstallation();
}
