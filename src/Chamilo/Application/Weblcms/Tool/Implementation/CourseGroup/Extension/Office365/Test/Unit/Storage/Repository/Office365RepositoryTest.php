<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Test\Stub\ClientExceptionStub;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Http\GraphRequest;

/**
 * Tests the Office365Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Office365RepositoryTest extends ChamiloTestCase
{
    /**
     * @var \League\OAuth2\Client\Provider\AbstractProvider | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $oauthProviderMock;

    /**
     * @var \Microsoft\Graph\Graph | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $graphMock;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\AccessTokenRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $accessTokenRepositoryMock;

    /**
     * @var Office365Repository
     */
    protected $office365Repository;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->oauthProviderMock = $this->getMockBuilder(GenericProvider::class)
            ->disableOriginalConstructor()->getMock();

        $this->graphMock = $this->getMockBuilder(Graph::class)
            ->disableOriginalConstructor()->getMock();

        $this->accessTokenRepositoryMock = $this->getMockBuilder(AccessTokenRepositoryInterface::class)
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->oauthProviderMock);
        unset($this->graphMock);
        unset($this->accessTokenRepositoryMock);
        unset($this->office365Repository);
    }

    public function testConstructorWithAccessTokenStored()
    {
        $this->constructWithStoredAccessToken();
    }

    public function testConstructorAsksNewAccessToken()
    {
        $accessToken = new AccessToken(
            ['access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA', 'expires' => time() + 1000]
        );

        $this->oauthProviderMock->expects($this->once())
            ->method('getAccessToken')
            ->with('client_credentials', ['resource' => 'https://graph.microsoft.com/'])
            ->will($this->returnValue($accessToken));

        $this->accessTokenRepositoryMock->expects($this->once())
            ->method('storeApplicationAccessToken')
            ->with($accessToken);

        $this->office365Repository =
            new Office365Repository($this->oauthProviderMock, $this->graphMock, $this->accessTokenRepositoryMock, '');
    }

    public function testConstructorAsksNewAccessTokenWhenExpired()
    {
        $accessToken = new AccessToken(
            ['access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA', 'expires' => (int) (time() - 1000)]
        );

        $this->accessTokenRepositoryMock->expects($this->once())
            ->method('getApplicationAccessToken')
            ->will($this->returnValue($accessToken));

        $this->oauthProviderMock->expects($this->once())
            ->method('getAccessToken')
            ->with('client_credentials', ['resource' => 'https://graph.microsoft.com/'])
            ->will($this->returnValue($accessToken));

        $this->accessTokenRepositoryMock->expects($this->once())
            ->method('storeApplicationAccessToken')
            ->with($accessToken);

        $this->office365Repository =
            new Office365Repository($this->oauthProviderMock, $this->graphMock, $this->accessTokenRepositoryMock, '');
    }

    public function testAuthorizeUserByAuthorizationCode()
    {
        $this->constructWithStoredAccessToken();

        $accessToken = new AccessToken(
            ['access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA', 'expires' => (int) (time() + 1000)]
        );

        $authorizationCode = 'VGhpcyBpcyBhbiBhdXRob3JpemF0aW9uIGNvZGU=';

        $this->oauthProviderMock->expects($this->once())
            ->method('getAccessToken')
            ->with('authorization_code', ['code' => $authorizationCode, 'resource' => 'https://graph.microsoft.com/'])
            ->will($this->returnValue($accessToken));

        $this->accessTokenRepositoryMock->expects($this->once())
            ->method('storeDelegatedAccessToken')
            ->with($accessToken);

        $this->office365Repository->authorizeUserByAuthorizationCode($authorizationCode);
    }

    public function testGetOffice365User()
    {
        $this->constructWithStoredAccessToken();

        $user = new User();
        $user->set_email('no-reply@example.com');

        $microsoftUser = new \Microsoft\Graph\Model\User();

        $this->mockRequest(
            'GET', '/users/' . $user->get_email(), \Microsoft\Graph\Model\User::class,
            $this->returnValue($microsoftUser)
        );

        $this->assertEquals($microsoftUser, $this->office365Repository->getOffice365User($user));
    }

    public function testGetOffice365UserWithResourceNotFoundException()
    {
        $this->constructWithStoredAccessToken();

        $user = new User();

        $this->mockRequest(
            'GET', '/users/' . $user->get_email(), \Microsoft\Graph\Model\User::class,
            $this->throwException(new ClientExceptionStub(Office365Repository::RESPONSE_CODE_RESOURCE_NOT_FOUND))
        );

        $this->assertEmpty($this->office365Repository->getOffice365User($user));
    }

    /**
     * @expectedException \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Test\Stub\ClientExceptionStub
     */
    public function testGetOffice365UserWithOtherException()
    {
        $this->constructWithStoredAccessToken();

        $user = new User();

        $this->mockRequest(
            'GET', '/users/' . $user->get_email(), \Microsoft\Graph\Model\User::class,
            $this->throwException(new ClientExceptionStub(301))
        );

        $this->assertEmpty($this->office365Repository->getOffice365User($user));
    }

    /**
     * @expectedException \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Test\Stub\ClientExceptionStub
     */
    public function testGetOffice365UserWillRetryWhenAccessTokenInvalid()
    {
        $this->constructWithStoredAccessToken();

        $user = new User();

        $this->mockRequest(
            'GET', '/users/' . $user->get_email(), \Microsoft\Graph\Model\User::class,
            $this->throwException(new ClientExceptionStub(Office365Repository::RESPONSE_CODE_ACCESS_TOKEN_EXPIRED))
        );

        $accessToken = new AccessToken(
            ['access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA', 'expires' => time() + 1000]
        );

        $this->oauthProviderMock->expects($this->once())
            ->method('getAccessToken')
            ->with('client_credentials', ['resource' => 'https://graph.microsoft.com/'])
            ->will($this->returnValue($accessToken));

        $this->accessTokenRepositoryMock->expects($this->once())
            ->method('storeApplicationAccessToken')
            ->with($accessToken);

        $this->assertEmpty($this->office365Repository->getOffice365User($user));
    }

    public function testCreateGroup()
    {
        $this->constructWithStoredAccessToken();

        $groupName = 'TestGroup 101';

        $groupData = [
            'description' => $groupName,
            'displayName' => $groupName,
            'mailEnabled' => false,
            'groupTypes' => [
                'Unified',
            ],
            'securityEnabled' => false,
            'mailNickname' => 'TestGroup_101',
            'visibility' => 'private'
        ];

        $this->mockRequest(
            'POST', '/groups', \Microsoft\Graph\Model\Group::class,
            $this->returnValue(null), $groupData
        );

        $this->office365Repository->createGroup($groupName);
    }

    public function testUpdateGroup()
    {
        $this->constructWithStoredAccessToken();

        $groupName = 'TestGroup 101';
        $groupData = [
            'description' => $groupName,
            'displayName' => $groupName
        ];

        $this->mockRequest(
            'PATCH', '/groups/5', \Microsoft\Graph\Model\Event::class,
            $this->returnValue(null), $groupData
        );

        $this->office365Repository->updateGroup(5, $groupName);
    }

    public function testSubscribeOwnerInGroup()
    {
        $this->constructWithStoredAccessToken();

        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $data = ['@odata.id' => 'https://graph.microsoft.com/v1.0/users/' . $office365UserIdentifier];

        $this->mockRequest(
            'POST', '/groups/' . $groupIdentifier . '/owners/$ref', \Microsoft\Graph\Model\Event::class,
            $this->returnValue(null), $data
        );

        $this->office365Repository->subscribeOwnerInGroup($groupIdentifier, $office365UserIdentifier);
    }

    public function testRemoveOwnerFromGroup()
    {
        $this->constructWithStoredAccessToken();

        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $this->mockRequest(
            'DELETE', '/groups/' . $groupIdentifier . '/owners/' . $office365UserIdentifier . '/$ref',
            \Microsoft\Graph\Model\Event::class, $this->returnValue(null)
        );

        $this->office365Repository->removeOwnerFromGroup($groupIdentifier, $office365UserIdentifier);
    }

    public function testGetGroupOwner()
    {
        $this->constructWithStoredAccessToken();

        $user = new \Microsoft\Graph\Model\User();

        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $this->mockRequest(
            'GET', '/groups/' . $groupIdentifier . '/owners/' . $office365UserIdentifier,
            \Microsoft\Graph\Model\User::class, $this->returnValue($user)
        );

        $this->assertEquals(
            $user, $this->office365Repository->getGroupOwner($groupIdentifier, $office365UserIdentifier)
        );
    }

    public function testGetGroupOwnerWithResourceNotFoundException()
    {
        $this->constructWithStoredAccessToken();

        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $this->mockRequest(
            'GET', '/groups/' . $groupIdentifier . '/owners/' . $office365UserIdentifier,
            \Microsoft\Graph\Model\User::class,
            $this->throwException(new ClientExceptionStub(Office365Repository::RESPONSE_CODE_RESOURCE_NOT_FOUND))
        );

        $this->office365Repository->getGroupOwner($groupIdentifier, $office365UserIdentifier);
    }

    /**
     * @expectedException \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Test\Stub\ClientExceptionStub
     */
    public function testGetGroupOwnerWithOtherException()
    {
        $this->constructWithStoredAccessToken();

        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $this->mockRequest(
            'GET', '/groups/' . $groupIdentifier . '/owners/' . $office365UserIdentifier,
            \Microsoft\Graph\Model\User::class,
            $this->throwException(new ClientExceptionStub(301))
        );

        $this->office365Repository->getGroupOwner($groupIdentifier, $office365UserIdentifier);
    }

    public function testListGroupOwners()
    {
        $this->constructWithStoredAccessToken();

        $groupIdentifier = 5;

        $users = [new \Microsoft\Graph\Model\User()];

        $this->mockRequest(
            'GET', '/groups/' . $groupIdentifier . '/owners', null,
            $this->returnValue($users), null, 'createCollectionRequest'
        );

        $this->assertEquals($users, $this->office365Repository->listGroupOwners($groupIdentifier));
    }

    public function testSubscribeMemberInGroup()
    {
        $this->constructWithStoredAccessToken();

        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $data = ['@odata.id' => 'https://graph.microsoft.com/v1.0/users/' . $office365UserIdentifier];

        $this->mockRequest(
            'POST', '/groups/' . $groupIdentifier . '/members/$ref', \Microsoft\Graph\Model\Event::class,
            $this->returnValue(null), $data
        );

        $this->office365Repository->subscribeMemberInGroup($groupIdentifier, $office365UserIdentifier);
    }

    public function testRemoveMemberFromGroup()
    {
        $this->constructWithStoredAccessToken();

        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $this->mockRequest(
            'DELETE', '/groups/' . $groupIdentifier . '/members/' . $office365UserIdentifier . '/$ref',
            \Microsoft\Graph\Model\Event::class, $this->returnValue(null)
        );

        $this->office365Repository->removeMemberFromGroup($groupIdentifier, $office365UserIdentifier);
    }

    public function testGetGroupMember()
    {
        $this->constructWithStoredAccessToken();

        $user = new \Microsoft\Graph\Model\User();

        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $this->mockRequest(
            'GET', '/groups/' . $groupIdentifier . '/members/' . $office365UserIdentifier,
            \Microsoft\Graph\Model\User::class, $this->returnValue($user)
        );

        $this->assertEquals(
            $user, $this->office365Repository->getGroupMember($groupIdentifier, $office365UserIdentifier)
        );
    }

    public function testGetGroupMemberWithResourceNotFoundException()
    {
        $this->constructWithStoredAccessToken();

        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $this->mockRequest(
            'GET', '/groups/' . $groupIdentifier . '/members/' . $office365UserIdentifier,
            \Microsoft\Graph\Model\User::class,
            $this->throwException(new ClientExceptionStub(Office365Repository::RESPONSE_CODE_RESOURCE_NOT_FOUND))
        );

        $this->office365Repository->getGroupMember($groupIdentifier, $office365UserIdentifier);
    }

    /**
     * @expectedException \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Test\Stub\ClientExceptionStub
     */
    public function testGetGroupMemberWithOtherException()
    {
        $this->constructWithStoredAccessToken();

        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $this->mockRequest(
            'GET', '/groups/' . $groupIdentifier . '/members/' . $office365UserIdentifier,
            \Microsoft\Graph\Model\User::class,
            $this->throwException(new ClientExceptionStub(301))
        );

        $this->office365Repository->getGroupMember($groupIdentifier, $office365UserIdentifier);
    }

    public function testListGroupMembers()
    {
        $this->constructWithStoredAccessToken();

        $groupIdentifier = 5;

        $users = [new \Microsoft\Graph\Model\User()];

        $this->mockRequest(
            'GET', '/groups/' . $groupIdentifier . '/members', null,
            $this->returnValue($users), null, 'createCollectionRequest'
        );

        $this->assertEquals($users, $this->office365Repository->listGroupMembers($groupIdentifier));
    }

    public function testListGroupPlans()
    {
        $accessToken = new AccessToken(
            ['access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA', 'expires' => time() + 1000]
        );

        $this->accessTokenRepositoryMock->expects($this->atLeastOnce())
            ->method('getDelegatedAccessToken')
            ->will($this->returnValue($accessToken));

        $this->constructWithStoredAccessToken();

        $groupIdentifier = 5;

        $plans = [new \Microsoft\Graph\Model\PlannerPlan()];

        $this->mockRequest(
            'GET', '/groups/' . $groupIdentifier . '/planner/plans',
            null,
            $this->returnValue($plans), null, 'createCollectionRequest'
        );

        $this->assertEquals($plans, $this->office365Repository->listGroupPlans($groupIdentifier));
    }

    public function testListGroupPlansWithoutDelegatedAccessToken()
    {
        $this->constructWithStoredAccessToken();

        $this->oauthProviderMock->expects($this->once())
            ->method('getAuthorizationUrl')
            ->will($this->returnValue('index.php'));

        $groupIdentifier = 5;

        $plans = [new \Microsoft\Graph\Model\PlannerPlan()];

        $this->mockRequest(
            'GET', '/groups/' . $groupIdentifier . '/planner/plans',
            \Microsoft\Graph\Model\PlannerPlan::class,
            $this->returnValue($plans)
        );

        ob_start();
        $this->assertEquals($plans, $this->office365Repository->listGroupPlans($groupIdentifier));
        $content = ob_get_clean();

        $this->assertContains('Redirecting to index.php', $content);
    }

    public function testListGroupPlansWithExpiredAccessToken()
    {
        $accessToken = new AccessToken(
            [
                'access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA', 'expires' => time() - 1000,
                'refresh_token' => 'cmVmcmVzaF90b2tlbg=='
            ]
        );

        $newAccessToken = new AccessToken(
            ['access_token' => 'eyJ0eXA57892ASWZO435SZSI6SnzA', 'expires' => time() - 1000]
        );

        $this->accessTokenRepositoryMock->expects($this->atLeastOnce())
            ->method('getDelegatedAccessToken')
            ->will($this->returnValue($accessToken));

        $this->oauthProviderMock->expects($this->once())
            ->method('getAccessToken')
            ->with('refresh_token', ['refresh_token' => $accessToken->getRefreshToken()])
            ->will($this->returnValue($newAccessToken));

        $this->accessTokenRepositoryMock->expects($this->once())
            ->method('storeDelegatedAccessToken')
            ->with($newAccessToken);

        $this->constructWithStoredAccessToken();

        $groupIdentifier = 5;

        $plans = [new \Microsoft\Graph\Model\PlannerPlan()];

        $this->mockRequest(
            'GET', '/groups/' . $groupIdentifier . '/planner/plans',
            \Microsoft\Graph\Model\PlannerPlan::class,
            $this->returnValue($plans)
        );

        $this->assertEquals($plans, $this->office365Repository->listGroupPlans($groupIdentifier));
    }

    /**
     * Constructs the Office365Repository with a valid and stored access token
     */
    protected function constructWithStoredAccessToken()
    {
        $accessToken = new AccessToken(
            ['access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA', 'expires' => time() + 1000]
        );

        $this->accessTokenRepositoryMock->expects($this->atLeastOnce())
            ->method('getApplicationAccessToken')
            ->will($this->returnValue($accessToken));

        $this->office365Repository =
            new Office365Repository($this->oauthProviderMock, $this->graphMock, $this->accessTokenRepositoryMock, '');
    }

    /**
     * @param string $requestMethod
     * @param string $requestUrl
     * @param string $returnType
     * @param \PHPUnit_Framework_MockObject_Stub|null $executeStub
     * @param mixed $body
     * @param string $requestMethodName
     */
    protected function mockRequest(
        $requestMethod = null, $requestUrl = null, $returnType = null,
        \PHPUnit_Framework_MockObject_Stub $executeStub = null, $body = null, $requestMethodName = 'createRequest'
    )
    {
        $graphRequest = $this->getMockBuilder(GraphRequest::class)
            ->disableOriginalConstructor()->getMock();

        $createRequestMock = $this->graphMock->expects($this->once())
            ->method($requestMethodName)
            ->will($this->returnValue($graphRequest));

        if ($requestMethod && $requestUrl)
        {
            $createRequestMock->with($requestMethod, $requestUrl);
        }

        if ($body)
        {
            $graphRequest->expects($this->once())
                ->method('attachBody')
                ->with($body)
                ->will($this->returnValue($graphRequest));
        }

        $setReturnTypeMock = $graphRequest->expects($this->once())
            ->method('setReturnType')
            ->will($this->returnValue($graphRequest));

        if ($returnType)
        {
            $setReturnTypeMock->with($returnType);
        }

        $executeMock = $graphRequest->expects($this->any())
            ->method('execute');

        if ($executeStub)
        {
            $executeMock->will($executeStub);
        }
    }
}

;