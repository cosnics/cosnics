<?php

namespace Chamilo\Test\Integration\Storage\DataManager\Doctrine\ORM;

use Chamilo\Libraries\Architecture\Test\TestCases\DependencyInjectionBasedTestCase;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ORM\MappingDriverFactory;

/**
 * Integration test for the mapping driver factory
 *
 * @package common\libraries\test\integration
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MappingDriverFactoryTest extends DependencyInjectionBasedTestCase
{
    /**
     * The SUT
     *
     * @var MappingDriverFactory
     */
    private $mappingDriverFactory;

    /**
     * Setup before each test
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mappingDriverFactory = $this->getService('Doctrine\ORM\MappingDriverFactory');
    }

    /**
     * Teardown after each test
     */
    protected function tearDown()
    {
        unset($this->mappingDriverFactory);
    }

    /**
     * Tests that the mapping driver factory only makes an annotation driver when the default mappings are used
     */
    public function testOnlyDefaultDriver()
    {
        $mapping = array(
            'default' => array('Chamilo/Libraries/Test/Integration/Storage/DataManager/Doctrine/ORM/config')
        );

        $this->assertInstanceOf(
            '\Doctrine\ORM\Mapping\Driver\AnnotationDriver', $this->mappingDriverFactory->createMappingDriver($mapping)
        );
    }

    /**
     * Tests that the default driver contains the correct paths
     */
    public function testDefaultDriverPaths()
    {
        $mapping = array(
            'default' => array('Chamilo/Libraries/Test/Integration/Storage/DataManager/Doctrine/ORM/config')
        );

        /** @var \Doctrine\ORM\Mapping\Driver\AnnotationDriver $mappingDriver */
        $mappingDriver = $this->mappingDriverFactory->createMappingDriver($mapping);

        $this->assertEquals(
            array(Path::getInstance()->getBasePath() . $mapping['default'][0]), $mappingDriver->getPaths()
        );
    }

    /**
     * Tests that the mapping driver factory makes a mapping chain when using custom mapping
     */
    public function testMappingChainWhenCustom()
    {
        $mapping = array(
            'default' => array('Chamilo/Libraries/Test/Integration/Storage/DataManager/Doctrine/ORM/config'),
            'custom' => array(
                'common_libraries_test' => array(
                    'type' => 'annotation', 'namespace' => 'common\libraries\test',
                    'paths' => array('Chamilo/Libraries/Test/Integration/Storage/DataManager/Doctrine/ORM/')
                )
            )
        );

        $this->assertInstanceOf(
            '\Doctrine\Persistence\Mapping\Driver\MappingDriverChain',
            $this->mappingDriverFactory->createMappingDriver($mapping)
        );
    }

    /**
     * Tests the custom annotation driver
     */
    public function testCustomAnnotationDriver()
    {
        $this->customDriverHelper('annotation', '\Doctrine\ORM\Mapping\Driver\AnnotationDriver');
    }

    /**
     * Tests the custom annotation driver
     */
    public function testCustomYamlDriver()
    {
        $this->customDriverHelper('yaml', '\Doctrine\ORM\Mapping\Driver\YamlDriver');
    }

    /**
     * Tests the custom annotation driver
     */
    public function testCustomXMLDriver()
    {
        $this->customDriverHelper('xml', '\Doctrine\ORM\Mapping\Driver\XmlDriver');
    }

    /**
     * Tests the custom annotation driver
     */
    public function testCustomPHPDriver()
    {
        $this->customDriverHelper('php', '\Doctrine\Common\Persistence\Mapping\Driver\PHPDriver');
    }

    /**
     * Tests the custom annotation driver
     */
    public function testCustomStaticPHPDriver()
    {
        $this->customDriverHelper('staticphp', '\Doctrine\Common\Persistence\Mapping\Driver\StaticPHPDriver');
    }

    /**
     * Tests that an invalid path in the mapping information throws an exception
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidPathThrowsException()
    {
        $mapping = array(
            'default' => array('bleh')
        );

        $this->mappingDriverFactory->createMappingDriver($mapping);
    }

    /**
     * Helper function to test a custom driver
     *
     * @param string $driverType
     * @param string $driverClass
     */
    protected function customDriverHelper($driverType, $driverClass)
    {
        $mapping = array(
            'default' => array('Chamilo/Libraries/Test/Integration/Storage/DataManager/Doctrine/ORM/config'),
            'custom' => array(
                'common_libraries_test' => array(
                    'type' => $driverType, 'namespace' => 'common\libraries\test',
                    'paths' => array('Chamilo/Libraries/Test/Integration/Storage/DataManager/Doctrine/ORM/')
                )
            )
        );

        /** @var \Doctrine\Persistence\Mapping\Driver\MappingDriverChain $mappingDriver */
        $mappingDriver = $this->mappingDriverFactory->createMappingDriver($mapping);
        $customDrivers = $mappingDriver->getDrivers();

        $customDriver = $customDrivers['common\libraries\test'];

        $this->assertInstanceOf($driverClass, $customDriver);
    }
}