<?php

namespace Chamilo\Core\Rights\Structure\Test\Unit\Service\StructureLocationConfiguration;

use Chamilo\Configuration\Service\RegistrationConsulter;
use Chamilo\Core\Rights\Structure\Service\Interfaces\StructureLocationRoleServiceInterface;
use Chamilo\Core\Rights\Structure\Service\Interfaces\StructureLocationServiceInterface;
use Chamilo\Core\Rights\Structure\Service\StructureLocationConfiguration\Interfaces\LoaderInterface;
use Chamilo\Core\Rights\Structure\Service\StructureLocationConfiguration\Synchronizer;
use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocation;
use Chamilo\Libraries\Architecture\Test\Test;

/**
 * Tests the Chamilo\Core\Rights\Structure\Service\StructureLocationConfiguration\Synchronizer class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SynchronizerTest extends Test
{
    /**
     * @var LoaderInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationLoader;

    /**
     * @var RegistrationConsulter | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $registrationConsulterMock;

    /**
     * @var StructureLocationServiceInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $structureLocationServiceMock;

    /**
     * @var StructureLocationRoleServiceInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $structureLocationRoleServiceMock;

    /**
     * Subject under test
     *
     * @var Synchronizer
     */
    protected $configurationSynchronizer;

    public function setUp()
    {
        $this->configurationLoader = $this->getMockForAbstractClass(LoaderInterface::class);
        $this->registrationConsulterMock = $this->getMock(RegistrationConsulter::class, array(), array(), '', false);
        $this->structureLocationServiceMock = $this->getMockForAbstractClass(StructureLocationServiceInterface::class);

        $this->structureLocationRoleServiceMock =
            $this->getMockForAbstractClass(StructureLocationRoleServiceInterface::class);

        $this->configurationSynchronizer = new Synchronizer(
            $this->configurationLoader, $this->registrationConsulterMock, $this->structureLocationServiceMock,
            $this->structureLocationRoleServiceMock
        );
    }

    public function tearDown()
    {
        unset($this->configurationLoader);
        unset($this->registrationConsulterMock);
        unset($this->structureLocationServiceMock);
        unset($this->structureLocationRoleServiceMock);
        unset($this->configurationSynchronizer);
    }

    public function testSynchronize()
    {
        $packages = array('Chamilo\Core\Repository');

        $configuration = array(
            'Chamilo\Core\Repository' => array(
                array(
                    'Package' => 'ROLE_DEFAULT_USER',
                    'ManageCategories' => array('ROLE_DEFAULT_USER', 'ROLE_ADMINISTRATOR')
                )
            )
        );

        $this->registrationConsulterMock->expects($this->once())
            ->method('getRegistrationContexts')
            ->will($this->returnValue($packages));

        $this->structureLocationServiceMock->expects($this->once())
            ->method('truncateStructureLocations');

        $this->configurationLoader->expects($this->once())
            ->method('loadConfiguration')
            ->with($packages)
            ->will($this->returnValue($configuration));

        $structureLocation = new StructureLocation();

        $this->structureLocationServiceMock->expects($this->exactly(2))
            ->method('createStructureLocation')
            ->withConsecutive(
                array('Chamilo\Core\Repository', null),
                array('Chamilo\Core\Repository', 'ManageCategories')
            )
            ->will($this->returnValue($structureLocation));

        $this->structureLocationRoleServiceMock->expects($this->exactly(3))
            ->method('addRoleToStructureLocation')
            ->withConsecutive(
               array($structureLocation, 'ROLE_DEFAULT_USER'),
               array($structureLocation, 'ROLE_DEFAULT_USER'),
               array($structureLocation, 'ROLE_ADMINISTRATOR')
            );

        $this->configurationSynchronizer->synchronize();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSynchronizeFailsWithEmptyConfiguration()
    {
        $this->registrationConsulterMock->expects($this->once())
            ->method('getRegistrationContexts')
            ->will($this->returnValue(array()));

        $this->configurationLoader->expects($this->once())
            ->method('loadConfiguration')
            ->will($this->returnValue(array()));

        $this->configurationSynchronizer->synchronize();
    }
}