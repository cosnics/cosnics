<?php
namespace Chamilo\Core\Install;

use Chamilo\Configuration\Package\Sequencer;
use Chamilo\Core\Install\Exception\InstallFailedException;
use Chamilo\Core\Install\Observer\InstallerObserver;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 *
 * @package Chamilo\Core\Install
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class PlatformInstaller
{

    /**
     *
     * @var \Chamilo\Core\Install\Configuration
     */
    private $configuration;

    /**
     *
     * @var string
     */
    private $configurationFilePath;

    /**
     *
     * @var \Chamilo\Core\Install\Observer\InstallerObserver
     */
    private $installerObserver;

    /**
     *
     * @var install\DataManagerInterface the storage manager used to perform install
     */
    private $dataManager;

    /**
     *
     * @var string[]
     */
    private $packages;

    /**
     *
     * @param \Chamilo\Core\Install\Observer\InstallerObserver $installerObserver
     * @param \Chamilo\Core\Install\ $configuration
     * @param unknown $dataManager
     */
    public function __construct(InstallerObserver $installerObserver, Configuration $configuration, $dataManager)
    {
        $this->installerObserver = $installerObserver;
        $this->configuration = $configuration;
        $this->dataManager = $dataManager;

        $this->configurationFilePath = Path::getInstance()->getStoragePath() . 'configuration/configuration.ini';
        $this->packages = array();
    }

    /**
     *
     * @param unknown $dataManager
     */
    public function setDataManager($dataManager)
    {
        $this->dataManager = $dataManager;
    }

    /**
     *
     * @param \Chamilo\Core\Install\Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     *
     * @param string $configurationFilePath
     */
    public function setConfigurationFilePath($configurationFilePath)
    {
        $this->configurationFilePath = $configurationFilePath;
    }

    /**
     *
     * @return string[]
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     *
     * @param string[] $packages
     */
    public function setPackages($packages)
    {
        $this->packages = $packages;
    }

    /**
     *
     * @param string $context
     */
    public function addPackage($context)
    {
        array_push($this->packages, $context);
    }

    /**
     *
     * @param string[] $packages
     */
    public function addPackages($packages)
    {
        foreach ($packages as $package)
        {
            $this->addPackage($package);
        }
    }

    public function orderPackages()
    {
        $sequencer = new Sequencer($this->packages);
        $this->packages = $sequencer->run();
    }

    /**
     *
     * @return string
     */
    public function getNextPackage()
    {
        return array_shift($this->packages);
    }

    public function run()
    {
        $this->initializeInstallation();

        echo $this->installerObserver->beforeInstallation();
        flush();

        echo $this->performPreProduction();
        flush();

        try
        {
            $this->installPackages();
        }
        catch (InstallFailedException $exception)
        {
            $stepResult = new StepResult(false, $exception->getMessage(), $exception->getPackage());

            echo $this->installerObserver->beforePackageInstallation($exception->getPackage());
            echo $this->installerObserver->afterPackageInstallation($stepResult);

            return;
        }

        // echo $this->performConfiguration();
        // flush();

        echo $this->installerObserver->afterInstallation();
        flush();
    }

    private function initializeInstallation()
    {
        Translation::getInstance()->setLanguageIsocode($this->configuration->get_platform_language());
    }

    /**
     *
     * @return string
     */
    private function performPreProduction()
    {
        $html = array();

        $html[] = $this->installerObserver->beforePreProduction();

        $result = new StepResult($this->dataManager->initializeStorage(), Translation::get('DatabaseCreated'));

        $html[] = $this->installerObserver->afterPreProductionDatabaseCreated($result);
        $html[] = $this->createFolders();
        $html[] = $this->installerObserver->afterPreProduction();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    private function performConfiguration()
    {
        return $this->installerObserver->afterPreProductionConfigurationFileWritten($this->writeConfigurationFile());
    }

    private function writeConfigurationFile()
    {
        $configuration = array();
        $configuration['general']['security_key'] = md5(uniqid(rand() . time()));
        $configuration['general']['hashing_algorithm'] = $this->configuration->get_crypt_algorithm();
        $configuration['general']['install_date'] = time();
        $configuration['database']['driver'] = $this->configuration->get_db_driver();
        $configuration['database']['username'] = $this->configuration->get_db_username();
        $configuration['database']['password'] = $this->configuration->get_db_password();
        $configuration['database']['host'] = $this->configuration->get_db_host();
        $configuration['database']['name'] = $this->configuration->get_db_name();
        $configuration['debug']['show_errors'] = false;
        $configuration['debug']['enable_query_cache'] = true;
        $configuration['storage']['archive_path'] = $this->configuration->get_archive_path();
        $configuration['storage']['cache_path'] = $this->configuration->get_cache_path();
        $configuration['storage']['garbage_path'] = $this->configuration->get_garbage_path();
        $configuration['storage']['hotpotatoes_path'] = $this->configuration->get_hotpotatoes_path();
        $configuration['storage']['logs_path'] = $this->configuration->get_logs_path();
        $configuration['storage']['repository_path'] = $this->configuration->get_repository_path();
        $configuration['storage']['scorm_path'] = $this->configuration->get_scorm_path();
        $configuration['storage']['temp_path'] = $this->configuration->get_temp_path();
        $configuration['storage']['userpictures_path'] = $this->configuration->get_userpictures_path();

        $content = array();

        $content[] = ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;';
        $content[] = '; The chamilo base configuration file.            ;';
        $content[] = '; Values were entered during installation,        ;';
        $content[] = '; don\'t change unless you know what you\'re doing. ;';
        $content[] = ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;';

        foreach ($configuration as $section => $settings)
        {
            $content[] = '[' . $section . ']';

            foreach ($settings as $name => $value)
            {
                if (is_numeric($value))
                {
                    $content[] = $name . ' = ' . $value;
                }
                else
                {
                    $content[] = $name . ' = "' . $value . '"';
                }
            }

            $content[] = '';
        }

        $write_status = Filesystem::write_to_file($this->config_file_destination, implode(PHP_EOL, $content));

        if ($write_status === false)
        {
            throw new \Exception(Translation::get('ConfigWriteFailed'));
        }

        \Chamilo\Configuration\Configuration::getInstance()->reset();

        return new StepResult(true, Translation::get('ConfigWriteSuccess'));
    }

    private function installPackages()
    {
        $this->addPackages($this->configuration->get_packages());
        $this->orderPackages();

        $html = array();

        echo $this->installerObserver->beforePackagesInstallation();
        flush();

        while (($package = $this->getNextPackage()) != null)
        {

            $values = $this->configuration->as_values_array();
            $installer = \Chamilo\Configuration\Package\Action\Installer::factory($package, $values);

            $success = $installer->run();

            if ($success !== true)
            {
                throw new InstallFailedException($package, implode(PHP_EOL, $html), $installer->retrieve_message());
            }
            else
            {
                $isIntegrationPackage = StringUtilities::getInstance()->createString($package)->contains(
                    '\Integration\\',
                    true);

                if (! $isIntegrationPackage)
                {
                    echo $this->installerObserver->beforePackageInstallation($package);
                    echo $this->installerObserver->afterPackageInstallation(
                        new StepResult($success, $installer->get_message(), $package));

                    flush();
                }
            }
        }

        echo $this->installerObserver->afterPackagesInstallation();
        flush();
    }

    private function createFolders()
    {
        $html = array();

        $html[] = $this->installerObserver->beforeFilesystemPrepared();

        $values = $this->configuration->as_values_array();

        $directories = array(
            $values['archive_path'],
            $values['cache_path'],
            $values['garbage_path'],
            $values['repository_path'],
            $values['temp_path'],
            $values['userpictures_path'],
            $values['scorm_path'],
            $values['logs_path'],
            $values['hotpotatoes_path']);

        foreach ($directories as $directory)
        {
            if (! file_exists($directory))
            {
                if (! Filesystem::create_dir($directory))
                {
                    throw new \Exception(Translation::get('FoldersCreatedFailed'));
                }
            }
        }

        $publicFilesPath = Path::getInstance()->getPublicStoragePath();

        if (! Filesystem::create_dir($publicFilesPath))
        {
            throw new \Exception(Translation::get('FoldersCreatedFailed'));
        }

        $html[] = $this->installerObserver->afterFilesystemPrepared(
            new StepResult(true, Translation::get('FoldersCreatedSuccess')));

        return implode(PHP_EOL, $html);
    }
}
