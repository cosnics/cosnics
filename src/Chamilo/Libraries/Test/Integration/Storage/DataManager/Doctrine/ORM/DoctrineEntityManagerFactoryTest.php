<?php

namespace Chamilo\Test\Integration\Storage\DataManager\Doctrine\ORM;

use Chamilo\Libraries\Architecture\Test\TestCases\DependencyInjectionBasedTestCase;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ORM\DoctrineEntityManagerFactory;

/**
 * Integration test for the doctrine entity manager factory
 *
 * @package common\libraries\test\integration
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DoctrineEntityManagerFactoryTest extends DependencyInjectionBasedTestCase
{
    /**
     * The SUT
     *
     * @var DoctrineEntityManagerFactory
     */
    private $entityManagerFactory;

    /**
     * Setup before each test
     */
    protected function setUp()
    {
        parent::setUp();
        $this->entityManagerFactory = $this->getService('Doctrine\ORM\EntityManagerFactory');
    }

    /**
     * Teardown after each test
     */
    protected function tearDown()
    {
        unset($this->entityManagerFactory);
    }

    /**
     * Tests that the entity manager can be created
     */
    public function testEntityManagerCanBeCreated()
    {
        $this->assertInstanceOf('\Doctrine\ORM\EntityManager', $this->entityManagerFactory->createEntityManager());
    }

}