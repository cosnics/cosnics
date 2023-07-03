<?php
namespace Chamilo\Libraries\Utilities\Jenkins;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Libraries\Architecture\Bootstrap\Bootstrap;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;

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

    public function getFilesystem(): \Symfony\Component\Filesystem\Filesystem
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            \Symfony\Component\Filesystem\Filesystem::class
        );
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

        return $systemPathBuilder->namespaceToFullPath($context) . 'test/php/source/';
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

        if ($package_list->has_children())
        {
            foreach ($package_list->get_children() as $child_list)
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

$package_list = PlatformPackageBundles::getInstance()->get_package_list();

$generator = new SourceCodeTestGenerator($package_list);
$generator->run();