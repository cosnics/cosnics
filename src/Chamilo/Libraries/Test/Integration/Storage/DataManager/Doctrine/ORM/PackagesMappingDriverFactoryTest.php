<?php
namespace Chamilo\Test\Integration\Storage\DataManager\Doctrine\ORM;

use Chamilo\Libraries\Architecture\Test\TestCases\DependencyInjectionBasedTestCase;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ORM\PackagesMappingDriverFactory;

/**
 * Integration test for the packages mapping driver factory
 *
 * @package common\libraries\test\integration
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PackagesMappingDriverFactoryTest extends DependencyInjectionBasedTestCase
{
    /**
     * The SUT
     *
     * @var PackagesMappingDriverFactory
     */
    private $packagesMappingDriverFactory;

    /**
     * Setup before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->packagesMappingDriverFactory = $this->getService('Doctrine\ORM\PackagesPappingDriverFactory');
    }

    /**
     * Teardown after each test
     */
    protected function tearDown(): void
    {
        unset($this->packagesMappingDriverFactory);
    }

    /**
     * Tests the packages mapping driver factory
     */
    public function testPackagesMappingDriverFactory()
    {
        $packages = array('common\libraries\test' => __DIR__ . '/config/config.yml');

        $this->assertInstanceOf(
            '\Doctrine\Persistence\Mapping\Driver\MappingDriverChain',
            $this->packagesMappingDriverFactory->createMappingDriverForPackages($packages)
        );
    }

    /**
     * Tests that the packages mapping driver factory supports multiple package configurations
     */
    public function testPackagesMappingDriverFactorySupportsMultiplePackages()
    {
        $packages = array(
            'common\libraries\test' => __DIR__ . '/config/config.yml',
            'common\libraries\test2' => __DIR__ . '/config/config2.yml',
        );

        /** @var \Doctrine\Persistence\Mapping\Driver\MappingDriverChain $mappingDriver */
        $mappingDriver = $this->packagesMappingDriverFactory->createMappingDriverForPackages($packages);

        /** @var \Doctrine\ORM\Mapping\Driver\AnnotationDriver $defaultDriver */
        $defaultDriver = $mappingDriver->getDefaultDriver();

        $this->assertEquals(2, count($defaultDriver->getPaths()));
    }

    /**
     * Tests that the mapping driver chain contains the default mapping driver
     */
    public function testMappingDriverContainsDefaultMappingDriver()
    {
        $packages = array('common\libraries\test' => __DIR__ . '/config/config.yml');

        /** @var \Doctrine\Persistence\Mapping\Driver\MappingDriverChain $mappingDriver */
        $mappingDriver = $this->packagesMappingDriverFactory->createMappingDriverForPackages($packages);

        $this->assertInstanceOf(
            '\Doctrine\ORM\Mapping\Driver\AnnotationDriver', $mappingDriver->getDefaultDriver()
        );
    }

    /**
     * Tests that the default mapping driver has the correct paths
     */
    public function testDefaultMappingDriverHasCorrectPaths()
    {
        $packages = array('common\libraries\test' => __DIR__ . '/config/config.yml');

        /** @var \Doctrine\Persistence\Mapping\Driver\MappingDriverChain $mappingDriver */
        $mappingDriver = $this->packagesMappingDriverFactory->createMappingDriverForPackages($packages);

        /** @var \Doctrine\ORM\Mapping\Driver\AnnotationDriver $defaultMappingDriver */
        $defaultMappingDriver = $mappingDriver->getDefaultDriver();

        $this->assertTrue(in_array(__DIR__ . '/config', $defaultMappingDriver->getPaths()));
    }

    /**
     * Tests that the mapping driver chain contains the custom mapping driver
     */
    public function testMappingDriverContainsCustomMappingDriver()
    {
        $packages = array('common\libraries\test' => __DIR__ . '/config/config.yml');

        /** @var \Doctrine\Persistence\Mapping\Driver\MappingDriverChain $mappingDriver */
        $mappingDriver = $this->packagesMappingDriverFactory->createMappingDriverForPackages($packages);
        $customDrivers = $mappingDriver->getDrivers();

        $this->assertInstanceOf(
            '\Doctrine\ORM\Mapping\Driver\YamlDriver', $customDrivers['common\libraries\test']
        );
    }

    /**
     * Tests that the custom mapping driver has the correct paths
     */
    public function testCustomMappingDriverHasCorrectPaths()
    {
        $packages = array('common\libraries\test' => __DIR__ . '/config/config.yml');

        /** @var \Doctrine\Persistence\Mapping\Driver\MappingDriverChain $mappingDriver */
        $mappingDriver = $this->packagesMappingDriverFactory->createMappingDriverForPackages($packages);

        /** @var \Doctrine\ORM\Mapping\Driver\YamlDriver $customMappingDriver */
        $customMappingDriver = $mappingDriver->getDrivers()['common\libraries\test'];

        $this->assertTrue(in_array(__DIR__ . '/config', $customMappingDriver->getLocator()->getPaths()));
    }

    /**
     * Tests that the createMappingDriverForPackages function can not accept an empty package list
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCanNotAcceptEmptyPackages()
    {
        $this->packagesMappingDriverFactory->createMappingDriverForPackages([]);
    }

    /**
     * Tests that the package config files exist
     *
     * @expectedException \InvalidArgumentException
     */
    public function testPackageConfigFileMustExist()
    {
        $this->packagesMappingDriverFactory->createMappingDriverForPackages(array('common\libraries' => ''));
    }

    /**
     * Tests that there must be at least one package which has mapping information in their configuration
     *
     * @expectedException \RuntimeException
     */
    public function testCanNotExceptConfigWithoutMappingInformation()
    {
        $packages = array('common\libraries\test' => __DIR__ . '/config/invalid_config.yml');

        $this->packagesMappingDriverFactory->createMappingDriverForPackages($packages);
    }

    /**
     * Tests that there must be at least one package which has common libraries mapping information in their
     * configuration
     *
     * @expectedException \RuntimeException
     */
    public function testCanNotExceptConfigWithoutCommonLibrariesInformation()
    {
        $packages = array('common\libraries\test' => __DIR__ . '/config/invalid_config2.yml');

        $this->packagesMappingDriverFactory->createMappingDriverForPackages($packages);
    }
}