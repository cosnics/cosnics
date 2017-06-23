<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Core\Repository\ContentObject\LearningPath\Test\Helper\CommonDataClassRepositoryExtension;
use Chamilo\Libraries\Architecture\Test\Test;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;

/**
 * Tests the CommonDataClassRepository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CommonDataClassRepositTest extends Test
{
    /**
     * @var CommonDataClassRepository
     */
    protected $commonDataClassRepository;

    /**
     * @var DataClass | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataClassMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        /** @var DataClassRepository $dataClassRepositoryMock */
        $dataClassRepositoryMock = $this->getMockBuilder(DataClassRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->commonDataClassRepository = new CommonDataClassRepositoryExtension($dataClassRepositoryMock);

        $this->dataClassMock = $this->getMockBuilder(DataClass::class)
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->commonDataClassRepository);
    }

    public function testCreate()
    {
        $this->dataClassMock->expects($this->once())
            ->method('create');

        $this->commonDataClassRepository->create($this->dataClassMock);
    }

    public function testUpdate()
    {
        $this->dataClassMock->expects($this->once())
            ->method('update');

        $this->commonDataClassRepository->update($this->dataClassMock);
    }

    public function testDelete()
    {
        $this->dataClassMock->expects($this->once())
            ->method('delete');

        $this->commonDataClassRepository->delete($this->dataClassMock);
    }
}

