<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Unit\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\UserRepository;

/**
 * Tests the UserService
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserServiceTest extends ChamiloTestCase
{
    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\UserRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userRepositoryMock;

    /**
     *
     * @var \Chamilo\Libraries\Platform\Configuration\LocalSetting | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $localSettingMock;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * Setup before each test
     */
    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->localSettingMock = $this->getMockBuilder(LocalSetting::class)
            ->disableOriginalConstructor()->getMock();


        $this->userService = new UserService($this->userRepositoryMock, $this->localSettingMock);
    }

    /**
     * Tear down after each test
     */
    protected  function tearDown(): void
    {
        unset($this->userService);
    }

    public function testGetAzureUserIdentifier()
    {
        $user = new User();

        $this->mockGetExternalUserIdentifier($user, null);

        $this->userRepositoryMock->expects($this->once())
            ->method('getAzureUser')
            ->with($user)
            ->will($this->returnValue(new \Microsoft\Graph\Model\User(['id' => 200])));

        $this->localSettingMock->expects($this->once())
            ->method('create')
            ->with(
                'external_user_id', 200,
                'Chamilo\Libraries\Protocol\Microsoft\Graph', $user
            );

        $this->assertEquals(200, $this->userService->getAzureUserIdentifier($user));
    }

    public function testGetAzureUserIdentifierFromCache()
    {
        $user = new User();

        $this->mockGetExternalUserIdentifier($user, 200);

        $this->userRepositoryMock->expects($this->never())
            ->method('getAzureUser');

        $this->localSettingMock->expects($this->never())
            ->method('create');

        $this->assertEquals(200, $this->userService->getAzureUserIdentifier($user));
    }

    public function testAuthorizeUserByAuthorizationCode()
    {
        $authorizationCode = 'VGhpcyBpcyBhbiBhdXRob3JpemF0aW9uIGNvZGU=';

        $this->userRepositoryMock->expects($this->once())
            ->method('authorizeUserByAuthorizationCode')
            ->with($authorizationCode);

        $this->userService->authorizeUserByAuthorizationCode($authorizationCode);
    }

    /**
     * Mocks the get function of the local setting for the external_user_id
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $returnValue
     */
    protected function mockGetExternalUserIdentifier(User $user, $returnValue = null)
    {
        $this->localSettingMock->expects($this->atLeastOnce())
            ->method('get')
            ->with(
                'external_user_id', 'Chamilo\Libraries\Protocol\Microsoft\Graph',
                $user
            )
            ->will($this->returnValue($returnValue));
    }
}

