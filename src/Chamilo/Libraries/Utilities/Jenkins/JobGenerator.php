<?php
namespace Chamilo\Libraries\Utilities\Jenkins;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Libraries\Architecture\Bootstrap\Bootstrap;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\SystemPathBuilder;

require_once realpath(__DIR__ . '/../../../../') . '/vendor/autoload.php';

class JobGenerator
{

    /**
     * @var string
     */
    private $job_path;

    /**
     * @var \configuration\package\storage\data_class\PackageList
     */
    private $package_list;

    /**
     * @param \configuration\package\storage\data_class\PackageList $package_list
     * @param string $job_path
     */
    public function __construct(PackageList $package_list, $job_path)
    {
        $this->package_list = $package_list;
        $this->job_path = $job_path;
    }

    public function run()
    {
        $this->process($this->package_list);
    }

    /**
     * @param string $context
     *
     * @return string
     */
    public function get_folder($context)
    {
        $systemPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SystemPathBuilder::class);

        return $systemPathBuilder->namespaceToFullPath($context) . 'build/config/';
    }

    /**
     * @param string $context
     *
     * @return string
     */
    public function get_job_name($context)
    {
        if ($context)
        {
            return str_replace('\\', '_', $context);
        }
        else
        {
            return 'chamilo';
        }
    }

    /**
     * @return string
     */
    public function get_job_path()
    {
        return $this->job_path;
    }

    /**
     * @return \configuration\package\storage\data_class\PackageList
     */
    public function get_package_list()
    {
        return $this->package_list;
    }

    /**
     * @param \configuration\package\storage\data_class\PackageList $package_list
     */
    public function process(PackageList $package_list)
    {
        $job_folder = $this->get_job_path() . $this->get_job_name($package_list->getType()) . DIRECTORY_SEPARATOR;

        if (!is_dir($job_folder))
        {
            $package_config_path = $this->get_folder($package_list->getType()) . 'config.xml';
            $job_config_path = $job_folder . 'config.xml';

            Filesystem::copy_file($package_config_path, $job_config_path);
        }

        if ($package_list->has_children())
        {
            foreach ($package_list->get_children() as $child_list)
            {
                $this->process($child_list);
            }
        }
    }

    /**
     * @param string $job_path
     */
    public function set_job_path($job_path)
    {
        $this->job_path = $job_path;
    }

    /**
     * @param \configuration\package\storage\data_class\PackageList $package_list
     */
    public function set_package_list($package_list)
    {
        $this->package_list = $package_list;
    }
}

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get(Bootstrap::class)->setup();

$package_list = PlatformPackageBundles::getInstance()->get_package_list();
$job_path = 'E:/jenkins/';

$generator = new JobGenerator($package_list, $job_path);
$generator->run();