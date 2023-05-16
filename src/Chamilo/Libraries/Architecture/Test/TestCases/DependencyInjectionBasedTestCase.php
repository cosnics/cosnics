<?php
namespace Chamilo\Libraries\Architecture\Test\TestCases;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
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
     * @return object | StorageUnitRepository
     */
    protected function getTestStorageUnitRepository()
    {
        return $this->getService('Chamilo\Libraries\Storage\DataManager\Doctrine\Test\StorageUnitRepository');
    }

    /**
     * @return object | DataClassRepository
     */
    protected function getTestDataClassRepository()
    {
        return $this->getService('Chamilo\Libraries\Storage\DataManager\Doctrine\Test\DataClassRepository');
    }

    /**
     * Returns the test entity manager from the dependency injection container
     *
     * @return \Doctrine\ORM\EntityManager | object
     */
    public function getTestEntityManager()
    {
        return $this->getService('Doctrine\ORM\Test\EntityManager');
    }
}