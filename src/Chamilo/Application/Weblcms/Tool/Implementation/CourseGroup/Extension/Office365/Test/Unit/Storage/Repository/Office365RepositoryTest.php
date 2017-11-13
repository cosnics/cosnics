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

    /**
     * Constructs the Office365Repository with a valid and stored access token
     */
    protected function constructWithStoredAccessToken()
    {
        $accessToken = new AccessToken(
            ['access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA', 'expires' => time() + 1000]
        );

        $this->accessTokenRepositoryMock->expects($this->once())
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
     */
    protected function mockRequest(
        $requestMethod = null, $requestUrl = null, $returnType = null,
        \PHPUnit_Framework_MockObject_Stub $executeStub = null
    )
    {
        $graphRequest = $this->getMockBuilder(GraphRequest::class)
            ->disableOriginalConstructor()->getMock();

        $createRequestMock = $this->graphMock->expects($this->once())
            ->method('createRequest')
            ->will($this->returnValue($graphRequest));

        if ($requestMethod && $requestUrl)
        {
            $createRequestMock->with($requestMethod, $requestUrl);
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