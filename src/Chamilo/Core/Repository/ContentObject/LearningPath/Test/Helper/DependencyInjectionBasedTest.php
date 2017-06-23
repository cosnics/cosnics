<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Helper;

use Chamilo\Libraries\Architecture\Test\Test;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base for test class that uses the DependencyInjectionContainer
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class DependencyInjectionBasedTest extends Test
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Setup before each test
     */
    protected function setUp()
    {
        $containerBuilder = new DependencyInjectionContainerBuilder();
        $containerBuilder->clearContainerInstance();
        $this->container = $containerBuilder->createContainer();
    }

    /**
     * Tear down after each test
     */
    protected function tearDown()
    {
        unset($this->container);
    }

    /**
     * Returns a service from the container
     *
     * @param string $serviceId
     *
     * @return object
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
        return $this->getService('chamilo.libraries.storage.data_manager.doctrine.test.storage_unit_repository');
    }

    /**
     * @return object | DataClassRepository
     */
    protected function getTestDataClassRepository()
    {
        return $this->getService('chamilo.libraries.storage.data_manager.doctrine.test.data_class_repository');
    }
}