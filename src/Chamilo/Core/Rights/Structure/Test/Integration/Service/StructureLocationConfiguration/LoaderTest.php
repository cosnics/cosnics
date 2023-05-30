<?php
namespace Chamilo\Core\Rights\Structure\Test\Integration\Service\StructureLocationConfiguration;

use Chamilo\Core\Rights\Structure\Service\StructureLocationConfiguration\Loader;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;

/**
 * Integration test for the Chamilo\Core\Rights\Structure\Service\StructureLocationConfiguration\Loader class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LoaderTest extends ChamiloTestCase
{
    /**
     * @var Loader
     */
    protected $configurationLoader;

    public function setUp(): void
    {
        /**
         * @var \Chamilo\Libraries\File\SystemPathBuilder $systemPathBuilder
         */
        $systemPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SystemPathBuilder::class);

        $this->configurationLoader = new Loader($systemPathBuilder);
    }

    public function testLoadConfiguration()
    {
        $configuration = $this->configurationLoader->loadConfiguration(['Chamilo\Core\Repository']);
        $expectedConfig = ['Chamilo\Core\Repository' => [['Package' => 'ROLE_DEFAULT_USER']]];

        $this->assertEquals($expectedConfig, $configuration);
    }

    /**
     * Tests the loadConfiguration function for a package that does not exist or does not have a valid
     * configuration file
     */
    public function testLoadConfigurationWithoutValidConfiguration()
    {
        $config = $this->configurationLoader->loadConfiguration(['Chamilo\Core\NotExistingPackage']);
        $this->assertEquals([], $config);
    }
}