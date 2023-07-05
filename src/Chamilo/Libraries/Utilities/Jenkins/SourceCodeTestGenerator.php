<?php
namespace Chamilo\Libraries\Utilities\Jenkins;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Libraries\Architecture\Bootstrap\Bootstrap;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Exception;
use Symfony\Component\Filesystem\Filesystem;

require_once realpath(__DIR__ . '/../../../../') . '/vendor/autoload.php';

/**
 * @package Chamilo\Libraries\Utilities\Jenkins
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SourceCodeTestGenerator
{

    /**
     * @var \configuration\package\storage\data_class\PackageList
     */
    private $package_list;

    /**
     * @param \configuration\package\storage\data_class\PackageList $package_list
     */
    public function __construct(PackageList $package_list)
    {
        $this->package_list = $package_list;
    }

    public function run()
    {
        $this->process($this->package_list);
    }

    public function getFilesystem(): Filesystem
    {
        return $this->getService(Filesystem::class);
    }

    /**
     * @template getService
     *
     * @param class-string<getService> $serviceName
     *
     * @return getService
     */
    protected function getService(string $serviceName)
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            $serviceName
        );
    }

    /**
     * @param string $context
     *
     * @return string
     */
    public function get_folder($context)
    {
        return $this->getService(SystemPathBuilder::class)->namespaceToFullPath($context) . 'test/php/source/';
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
        $this->write_source_code_test($package_list->getType());

        if ($package_list->hasPackageLists())
        {
            foreach ($package_list->getPackageLists() as $child_list)
            {
                $this->process($child_list);
            }
        }
    }

    /**
     * @param \configuration\package\storage\data_class\PackageList $package_list
     */
    public function set_package_list($package_list)
    {
        $this->package_list = $package_list;
    }

    /**
     * @param string $context
     */
    public function write_source_code_test($context)
    {
        $manager_class_name = $context . '\Manager';

        if (class_exists($manager_class_name) &&
            is_subclass_of($manager_class_name, '\Chamilo\Libraries\Architecture\Application\Application'))
        {
            $content = '<?php
namespace ' . $context . '\test;

use \libraries\architecture\test\TestApplication;

/**
 * This test case checks the php syntax for the php files of this package
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CheckSourceCodeTest extends \libraries\architecture\test\source\CheckSourceCodeTest
{
    use TestApplication;
}';
        }
        else
        {
            $content = '<?php
namespace ' . $context . '\test;

/**
 * This test case checks the php syntax for the php files of this package
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CheckSourceCodeTest extends \libraries\architecture\test\source\CheckSourceCodeTest
{
}';
        }

        $systemPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SystemPathBuilder::class);

        $path = $this->get_folder($context) . 'check_source_code_test.class.php';
        $php_class_path = $systemPathBuilder->namespaceToFullPath($context) . 'php/';

        if (!file_exists($path) && is_dir($php_class_path))
        {
            echo $context . PHP_EOL;
            $this->getFilesystem()->dumpFile($path, $content);
        }
    }
}

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get(Bootstrap::class)->setup();

try
{
    /**
     * @var \Chamilo\Configuration\Package\Service\PackageBundlesCacheService $packageBundlesCacheService
     */
    $packageBundlesCacheService = $container->get(PackageBundlesCacheService::class);
    $packageList = $packageBundlesCacheService->getAllPackages();

    $generator = new SourceCodeTestGenerator($packageList);
    $generator->run();
}
catch (Exception)
{

}