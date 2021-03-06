<?php
namespace Chamilo\Libraries\Utilities\Jenkins;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;

require_once realpath(__DIR__ . '/../../../../') . '/vendor/autoload.php';

/**
 *
 * @package Chamilo\Libraries\Utilities\Jenkins
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SourceCodeTestGenerator
{

    /**
     *
     * @var \configuration\package\storage\data_class\PackageList
     */
    private $package_list;

    /**
     *
     * @param \configuration\package\storage\data_class\PackageList $package_list
     */
    public function __construct(PackageList $package_list)
    {
        $this->package_list = $package_list;
    }

    /**
     *
     * @return \configuration\package\storage\data_class\PackageList
     */
    public function get_package_list()
    {
        return $this->package_list;
    }

    /**
     *
     * @param \configuration\package\storage\data_class\PackageList $package_list
     */
    public function set_package_list($package_list)
    {
        $this->package_list = $package_list;
    }

    public function run()
    {
        $this->process($this->package_list);
    }

    /**
     *
     * @param \configuration\package\storage\data_class\PackageList $package_list
     */
    public function process(PackageList $package_list)
    {
        $this->write_source_code_test($package_list->get_type());

        if ($package_list->has_children())
        {
            foreach ($package_list->get_children() as $child_list)
            {
                $this->process($child_list);
            }
        }
    }

    /**
     *
     * @param string $context
     * @return string
     */
    public function get_folder($context)
    {
        return Path::getInstance()->namespaceToFullPath($context) . 'test/php/source/';
    }

    /**
     *
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

        $path = $this->get_folder($context) . 'check_source_code_test.class.php';
        $php_class_path = Path::getInstance()->namespaceToFullPath($context) . 'php/';

        if (! file_exists($path) && is_dir($php_class_path))
        {
            echo $context . "\n";
            Filesystem::write_to_file($path, $content, false);
        }
    }
}

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get('chamilo.libraries.architecture.bootstrap.bootstrap')->setup();

$package_list = \Chamilo\Configuration\Package\PlatformPackageBundles::getInstance()->get_package_list();

$generator = new SourceCodeTestGenerator($package_list);
$generator->run();