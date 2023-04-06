<?php

namespace Chamilo\Core\Rights\Structure\Test\Unit\Service;

use Chamilo\Core\Rights\Structure\Service\StructureLocationService;
use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocation;
use Chamilo\Core\Rights\Structure\Storage\Repository\Interfaces\StructureLocationRepositoryInterface;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the Chamilo\Core\Rights\Structure\Service\StructureLocationService class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class StructureLocationServiceTest extends ChamiloTestCase
{
    /**
     * @var StructureLocationRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $structureLocationRepositoryMock;

    /**
     * Subject Under Test
     *
     * @var StructureLocationService
     */
    protected $structureLocationService;

    protected function setUp(): void    {
        $this->structureLocationRepositoryMock = $this->getMockForAbstractClass(
            'Chamilo\Core\Rights\Structure\Storage\Repository\Interfaces\StructureLocationRepositoryInterface'
        );

        $this->structureLocationService = new StructureLocationService($this->structureLocationRepositoryMock);
    }

    protected function tearDown(): void    {
        unset($this->structureLocationRepositoryMock);
        unset($this->structureLocationService);
    }

    public function testCreateStructureLocation()
    {
        $context = 'Chamilo\Application\Weblcms';
        $action = 'ManagePersonalCourses';

        $this->structureLocationRepositoryMock->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(
                    function ($structureLocation) use ($context, $action)
                    {
                        return $structureLocation instanceof StructureLocation &&
                        $structureLocation->getContext() == $context && $structureLocation->getAction() == $action;
                    }
                )
            )
            ->will($this->returnValue(true));

        $this->assertInstanceOf(
            StructureLocation::class, $this->structureLocationService->createStructureLocation($context, $action)
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testCreateStructureLocationFails()
    {
        $context = 'Chamilo\Application\Weblcms';
        $action = 'ManagePersonalCourses';

        $this->structureLocationRepositoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue(false));

        $this->structureLocationService->createStructureLocation($context, $action);
    }

    public function testDeleteStructureLocation()
    {
        $structureLocation = new StructureLocation();

        $this->structureLocationRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($structureLocation)
            ->will($this->returnValue(true));

        $this->structureLocationService->deleteStructureLocation($structureLocation);
    }

    /**
     * @expectedException \Exception
     */
    public function testDeleteStructureLocationFails()
    {
        $structureLocation = new StructureLocation();

        $this->structureLocationRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($structureLocation)
            ->will($this->returnValue(false));

        $this->structureLocationService->deleteStructureLocation($structureLocation);
    }

    public function testTruncateStructureLocations()
    {
        $this->structureLocationRepositoryMock->expects($this->once())
            ->method('truncateStructureLocationsAndRoles')
            ->will($this->returnValue(true));

        $this->structureLocationService->truncateStructureLocations();
    }

    /**
     * @expectedException \Exception
     */
    public function testTruncateStructureLocationsFails()
    {
        $this->structureLocationRepositoryMock->expects($this->once())
            ->method('truncateStructureLocationsAndRoles')
            ->will($this->returnValue(false));

        $this->structureLocationService->truncateStructureLocations();
    }


}
