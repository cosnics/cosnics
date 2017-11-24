<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Unit\Storage\Repository;

use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository;

/**
 * Tests the GroupRepository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupRepositoryTest extends ChamiloTestCase
{
    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    private $graphRepositoryMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->graphRepositoryMock = $this->getMockBuilder(GraphRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->groupRepository = new GroupRepository($this->graphRepositoryMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->graphRepositoryMock);
        unset($this->groupRepository);
    }

}

