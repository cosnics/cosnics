<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Unit\Storage\Repository;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\UserRepository;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Stub\ClientExceptionStub;

/**
 * Tests the UserRepository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserRepositoryTest extends ChamiloTestCase
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    private $graphRepositoryMock;

    /**
     * Setup before each test
     */
    protected function setUp(): void
    {
        $this->graphRepositoryMock = $this->getMockBuilder(GraphRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->userRepository = new UserRepository($this->graphRepositoryMock);
    }

    /**
     * Tear down after each test
     */
    protected  function tearDown(): void
    {
        unset($this->graphRepositoryMock);
        unset($this->userRepository);
    }

    public function testGetOffice365User()
    {
        $user = new User();
        $user->set_email('no-reply@example.com');

        $microsoftUser = new \Microsoft\Graph\Model\User();

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeGetWithAccessTokenExpirationRetry')
            ->with('/users/' . $user->get_email(), \Microsoft\Graph\Model\User::class)
            ->will($this->returnValue($microsoftUser));

        $this->assertEquals($microsoftUser, $this->userRepository->getAzureUser($user));
    }

    public function testGetOffice365UserWithResourceNotFoundException()
    {
        $user = new User();
        $user->set_email('no-reply@example.com');

        $microsoftUser = new \Microsoft\Graph\Model\User();

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeGetWithAccessTokenExpirationRetry')
            ->with('/users/' . $user->get_email(), \Microsoft\Graph\Model\User::class)
            ->will($this->throwException(new ClientExceptionStub(GraphRepository::RESPONSE_CODE_RESOURCE_NOT_FOUND)));

        $this->assertEmpty($this->userRepository->getAzureUser($user));

    }

    /**
     * @expectedException \Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Stub\ClientExceptionStub
     */
    public function testGetOffice365UserWithOtherException()
    {
        $user = new User();
        $user->set_email('no-reply@example.com');

        $microsoftUser = new \Microsoft\Graph\Model\User();

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeGetWithAccessTokenExpirationRetry')
            ->with('/users/' . $user->get_email(), \Microsoft\Graph\Model\User::class)
            ->will($this->throwException(new ClientExceptionStub(301)));

        $this->assertEquals($microsoftUser, $this->userRepository->getAzureUser($user));
    }

    public function testAuthorizeUserByAuthorizationCode()
    {
        $authorizationCode = 'RespectMyAuthoritay';

        $this->graphRepositoryMock->expects($this->once())
            ->method('authorizeUserByAuthorizationCode')
            ->with($authorizationCode);

        $this->userRepository->authorizeUserByAuthorizationCode($authorizationCode);
    }
}

