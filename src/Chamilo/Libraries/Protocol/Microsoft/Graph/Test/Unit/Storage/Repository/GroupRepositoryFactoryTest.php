<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Unit\Storage\Repository;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\AccessTokenRepositoryInterface;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepositoryFactory;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Tests the Office365RepositoryFactory
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupRepositoryFactoryTest extends ChamiloTestCase
{
    /**
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepositoryFactory
     */
    protected $groupRepositoryFactory;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationConsulterMock;

    /**
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $graphRepositoryMock;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->configurationConsulterMock = $this->getMockBuilder(ConfigurationConsulter::class)
            ->disableOriginalConstructor()->getMock();

        $this->graphRepositoryMock = $this->getMockBuilder(GraphRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->groupRepositoryFactory =
            new GroupRepositoryFactory($this->graphRepositoryMock, $this->configurationConsulterMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->configurationConsulterMock);
        unset($this->graphRepositoryMock);
        unset($this->groupRepositoryFactory);
    }

    public function testBuildGroupRepository()
    {
        $this->configurationConsulterMock->expects($this->at(0))
            ->method('getSetting')
            ->with(['Chamilo\Libraries\Protocol\Microsoft\Graph', 'cosnics_prefix'])
            ->will($this->returnValue('cosnics_'));

        $groupRepository = $this->groupRepositoryFactory->buildGroupRepository();
        $this->assertInstanceOf(GroupRepository::class, $groupRepository);
        $cosnicsId = $this->get_property_value($groupRepository, 'cosnicsPrefix');

        $this->assertEquals('cosnics_', $cosnicsId);
    }
}

