<?php
namespace Chamilo\Core\Install;

use Chamilo\Configuration\Package\Sequencer;
use Chamilo\Core\Install\Exception\InstallFailedException;
use Chamilo\Core\Install\Observer\InstallerObserver;
use Chamilo\Core\Install\Observer\Type\WebInterfaceInstaller;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;
use Exception;

class PlatformInstaller
{

    /**
     *
     * @var Configuration $installer_config
     */
    private $installer_config;

    /**
     *
     * @var string where to write the new config file
     */
    private $config_file_destination;

    /**
     *
     * @var install\InstallerObservers list of the observers to be notified when performing the install
     */
    private $observers;

    /**
     *
     * @var install\DataManagerInterface the storage manager used to perform install
     */
    private $data_manager;

    /**
     *
     * @var multitype:string
     */
    private $packages;

    public function __construct(Configuration $installer_config, $data_manager)
    {
        $this->set_installer_config($installer_config);
        $default_config_file = Path :: getInstance()->getStoragePath() . 'configuration/configuration.ini';
        $this->set_config_file_destination($default_config_file);

        $this->set_data_manager($data_manager);

        $this->packages = array();
        $this->observers = new WebInterfaceInstaller();
    }

    public function set_data_manager($data_manager)
    {
        $this->data_manager = $data_manager;
    }

    public function set_installer_config($installer_config)
    {
        $this->installer_config = $installer_config;
    }

    public function set_config_file_destination($config_file_destination)
    {
        $this->config_file_destination = $config_file_destination;
    }

    public function add_observer(InstallerObserver $observer)
    {
        $this->observers->add_observer($observer);
    }

    /**
     *
     * @return multitype:string
     */
    public function get_packages()
    {
        return $this->packages;
    }

    /**
     *
     * @param multitype:string $packages
     */
    public function set_packages($packages)
    {
        $this->packages = $packages;
    }

    /**
     *
     * @param string $context
     */
    public function add_package($context)
    {
        array_push($this->packages, $context);
    }

    /**
     *
     * @param multitype:string $packages
     */
    public function add_packages($packages)
    {
        foreach ($packages as $package)
        {
            $this->add_package($package);
        }
    }

    public function order_packages()
    {
        $sequencer = new Sequencer($this->packages);
        $this->packages = $sequencer->run();
    }

    /**
     *
     * @return string
     */
    public function get_next_package()
    {
        return array_shift($this->packages);
    }

    public function perform_install()
    {
        $this->initialize_install();

        $html = array();

        $html[] = $this->observers->before_install();
        $html[] = $this->perform_preprod();
        $html[] = $this->install_packages();
        $html[] = $this->perform_config();
        $html[] = $this->observers->after_install();

        return implode(PHP_EOL, $html);
    }

    private function initialize_install()
    {
        Translation :: getInstance()->setLanguageIsocode($this->installer_config->get_platform_language());
    }

    private function perform_preprod()
    {
        $html = array();

        $html[] = $this->observers->before_preprod();

        $configuration = \Chamilo\Configuration\Configuration :: get_instance();

        $configuration->set(array('Chamilo\Configuration', 'general', 'data_manager'), 'mdb2');
        $configuration->set(
            array('Chamilo\Configuration', 'database', 'driver'),
            $this->installer_config->get_db_driver());
        $configuration->set(array('Chamilo\Configuration', 'database', 'host'), $this->installer_config->get_db_host());
        $configuration->set(
            array('Chamilo\Configuration', 'database', 'username'),
            $this->installer_config->get_db_username());
        $configuration->set(
            array('Chamilo\Configuration', 'database', 'password'),
            $this->installer_config->get_db_password());
        $configuration->set(array('Chamilo\Configuration', 'database', 'name'), $this->installer_config->get_db_name());

        $this->data_manager->init_storage_access();
        $this->data_manager->init_storage_structure();

        $result = new StepResult(true, Translation :: get('DatabaseCreated'));

        $html[] = $this->observers->preprod_db_created($result);
        $html[] = $this->create_folders();
        $html[] = $this->observers->after_preprod();

        return implode(PHP_EOL, $html);
    }

    private function perform_config()
    {
        $result_config = $this->write_config_file();
        return $this->observers->preprod_config_file_written($result_config);
    }

    private function write_config_file()
    {
        $configuration = array();
        $configuration['general']['root_web'] = $this->installer_config->get_base_url();
        $configuration['general']['url_append'] = $this->installer_config->get_url_append();
        $configuration['general']['security_key'] = md5(uniqid(rand() . time()));
        $configuration['general']['hashing_algorithm'] = $this->installer_config->get_crypt_algorithm();
        $configuration['general']['install_date'] = time();
        $configuration['database']['driver'] = $this->installer_config->get_db_driver();
        $configuration['database']['username'] = $this->installer_config->get_db_username();
        $configuration['database']['password'] = $this->installer_config->get_db_password();
        $configuration['database']['host'] = $this->installer_config->get_db_host();
        $configuration['database']['name'] = $this->installer_config->get_db_name();
        $configuration['debug']['show_errors'] = false;

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

        $write_status = Filesystem :: write_to_file($this->config_file_destination, implode(PHP_EOL, $content));

        if ($write_status === false)
        {
            throw new \Exception(Translation :: get('ConfigWriteFailed'));
        }

        return new StepResult(true, Translation :: get('ConfigWriteSuccess'));
    }

    private function install_packages()
    {
        $this->add_packages($this->installer_config->get_packages());
        $this->order_packages();

        $html = array();

        $html[] = $this->observers->before_packages_install();

        while (($package = $this->get_next_package()) != null)
        {

            $values = $this->installer_config->as_values_array();
            $installer = \Chamilo\Configuration\Package\Action\Installer :: factory($package, $values);

            $success = $installer->run();

            if ($success !== true)
            {
                throw new InstallFailedException($package, implode(PHP_EOL, $html), $installer->retrieve_message());
            }
            else
            {
                $html[] = $this->observers->before_package_install($package);
                $step_result = new StepResult($success, $installer->get_message(), $package);
                $html[] = $this->observers->after_package_install($step_result);
            }
        }

        $html[] = $this->observers->after_packages_install();

        return implode(PHP_EOL, $html);
    }

    private function create_folders()
    {
        $html = array();

        $html[] = $this->observers->before_filesystem_prepared();
        $files_path = Path :: getInstance()->getStoragePath();
        $directories = array(
            'archive',
            'cache',
            'garbage',
            'repository',
            'Temp',
            'userpictures',
            'scorm',
            'logs',
            'hotpotatoes');

        foreach ($directories as $directory)
        {
            $path = $files_path . $directory;

            // if (file_exists($path) && is_dir($path))
            // {
            // Filesystem :: remove($path);
            // }

            if (! Filesystem :: create_dir($path))
            {
                throw new \Exception(Translation :: get('FoldersCreatedFailed'));
            }
        }

        $step_result = new StepResult(true, Translation :: get('FoldersCreatedSuccess'));
        $html[] = $this->observers->after_filesystem_prepared($step_result);

        return implode(PHP_EOL, $html);
    }
}
