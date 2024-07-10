<?php
namespace Chamilo\Libraries\Architecture\Test\TestCases;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Repository\StorageUnitRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base for test class that uses the DependencyInjectionContainer
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class DependencyInjectionBasedTestCase extends ChamiloTestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Returns a service from the container
     *
     * @param string $serviceId
     *
     * @return object | mixed
     */
    protected function getService($serviceId)
    {
        return $this->container->get($serviceId);
    }

    /**
     * @return object | DataClassRepository
     */
    protected function getTestDataClassRepository()
    {
        return $this->getService('Chamilo\Libraries\Storage\Implementations\Doctrine\Test\DataClassRepository');
    }

    /**
     * @return object | StorageUnitRepository
     */
    protected function getTestStorageUnitRepository()
    {
        return $this->getService('Chamilo\Libraries\Storage\Implementations\Doctrine\Test\StorageUnitRepository');
    }

    /**
     * Setup before each test
     */
    protected function setUp(): void
    {
        $containerBuilder = new DependencyInjectionContainerBuilder();
        $containerBuilder->clearContainerInstance();
        $this->container = $containerBuilder->createContainer();
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void
    {
        unset($this->container);
    }
}