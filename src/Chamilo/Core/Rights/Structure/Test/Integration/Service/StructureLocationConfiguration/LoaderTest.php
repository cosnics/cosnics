<?php

namespace Chamilo\Core\Rights\Structure\Test\Integration\Service\StructureLocationConfiguration;

use Chamilo\Core\Rights\Structure\Service\StructureLocationConfiguration\Loader;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\File\Path;

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

    public function setUp()
    {
        $this->configurationLoader = new Loader(Path::getInstance());
    }

    public function testLoadConfiguration()
    {
        $configuration = $this->configurationLoader->loadConfiguration(array('Chamilo\Core\Repository'));
        $expectedConfig = array('Chamilo\Core\Repository' => array(array('Package' => 'ROLE_DEFAULT_USER')));

        $this->assertEquals($expectedConfig, $configuration);
    }

    /**
     * Tests the loadConfiguration function for a package that does not exist or does not have a valid
     * configuration file
     */
    public function testLoadConfigurationWithoutValidConfiguration()
    {
        $config = $this->configurationLoader->loadConfiguration(array('Chamilo\Core\NotExistingPackage'));
        $this->assertEquals(array(), $config);
    }
}