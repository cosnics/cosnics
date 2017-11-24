<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Storage\Repository;

use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\AccessTokenRepositoryInterface;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Stub\ClientExceptionStub;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Http\GraphRequest;
use Microsoft\Graph\Http\GraphResponse;

/**
 * Tests the Office365Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GraphRepositoryTest extends ChamiloTestCase
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
     * @var AccessTokenRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $accessTokenRepositoryMock;

    /**
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository
     */
    protected $graphRepository;

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
        unset($this->graphRepository);
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

        $this->graphRepository =
            new GraphRepository($this->oauthProviderMock, $this->graphMock, $this->accessTokenRepositoryMock, '');
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

        $this->graphRepository =
            new GraphRepository($this->oauthProviderMock, $this->graphMock, $this->accessTokenRepositoryMock, '');
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

        $this->graphRepository->authorizeUserByAuthorizationCode($authorizationCode);
    }

    public function testExecuteGetWithAccessTokenExpirationRetry()
    {
        $this->constructWithStoredAccessToken();

        $microsoftUser = new \Microsoft\Graph\Model\User();

        $this->mockRequest(
            'GET', '/users/no-reply@example.com', \Microsoft\Graph\Model\User::class,
            $this->returnValue($microsoftUser)
        );

        $this->assertEquals(
            $microsoftUser,
            $this->graphRepository->executeGetWithAccessTokenExpirationRetry(
                '/users/no-reply@example.com', \Microsoft\Graph\Model\User::class
            )
        );
    }

    public function testExecuteGetWithAccessTokenExpirationRetryWithCollection()
    {
        $this->constructWithStoredAccessToken();

        $microsoftUser = new \Microsoft\Graph\Model\User();

        $this->mockCollectionRequest(
            'GET', '/users', \Microsoft\Graph\Model\User::class,
            $this->returnValue([$microsoftUser])
        );

        $this->assertEquals(
            [$microsoftUser],
            $this->graphRepository->executeGetWithAccessTokenExpirationRetry(
                '/users', \Microsoft\Graph\Model\User::class, true
            )
        );
    }

    public function testExecuteGetWithAccessTokenExpirationRetryWithCollectionNoDataCount()
    {
        $this->constructWithStoredAccessToken();

        $microsoftUser = new \Microsoft\Graph\Model\User();

        $this->mockCollectionRequest(
            'GET', '/users', \Microsoft\Graph\Model\User::class,
            $this->returnValue([$microsoftUser]), 1, false
        );

        $this->assertEquals(
            [$microsoftUser],
            $this->graphRepository->executeGetWithAccessTokenExpirationRetry(
                '/users', \Microsoft\Graph\Model\User::class, true
            )
        );
    }

    public function testExecuteGetWithAccessTokenExpirationRetryWithNoDataCount()
    {
        $this->constructWithStoredAccessToken();

        $microsoftUser = new \Microsoft\Graph\Model\User();

        $this->mockCollectionRequest(
            'GET', '/users', \Microsoft\Graph\Model\User::class,
            $this->returnValue([$microsoftUser])
        );

        $this->assertEquals(
            [$microsoftUser],
            $this->graphRepository->executeGetWithAccessTokenExpirationRetry(
                '/users', \Microsoft\Graph\Model\User::class, true
            )
        );
    }

    public function testExecuteGetWithAccessTokenExpirationRetryWithEmptyCount()
    {
        $this->constructWithStoredAccessToken();

        $microsoftUser = new \Microsoft\Graph\Model\User();

        $this->mockCollectionRequest(
            'GET', '/users', \Microsoft\Graph\Model\User::class,
            $this->returnValue([$microsoftUser]), 0
        );

        $this->assertEquals(
            [],
            $this->graphRepository->executeGetWithAccessTokenExpirationRetry(
                '/users', \Microsoft\Graph\Model\User::class, true
            )
        );
    }

    /**
     * @expectedException \Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Stub\ClientExceptionStub
     */
    public function testExecuteGetWithAccessTokenExpirationRetryWillRetryWhenAccessTokenInvalid()
    {
        $this->constructWithStoredAccessToken();

        $microsoftUser = new \Microsoft\Graph\Model\User();

        $this->mockRequest(
            'GET', '/users/no-reply@example.com', \Microsoft\Graph\Model\User::class,
            $this->throwException(new ClientExceptionStub(GraphRepository::RESPONSE_CODE_ACCESS_TOKEN_EXPIRED))
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

        $this->assertEquals(
            $microsoftUser,
            $this->graphRepository->executeGetWithAccessTokenExpirationRetry(
                '/users/no-reply@example.com', \Microsoft\Graph\Model\User::class
            )
        );
    }

    /**
     * @expectedException \Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Stub\ClientExceptionStub
     */
    public function testExecuteGetWithAccessTokenExpirationRetryWillThrowOtherException()
    {
        $this->constructWithStoredAccessToken();

        $this->mockRequest(
            'GET', '/users/no-reply@example.com', \Microsoft\Graph\Model\User::class,
            $this->throwException(new ClientExceptionStub(GraphRepository::RESPONSE_CODE_RESOURCE_NOT_FOUND))
        );

        $this->graphRepository->executeGetWithAccessTokenExpirationRetry(
            '/users/no-reply@example.com', \Microsoft\Graph\Model\User::class
        );
    }

    public function testExecuteGetWithDelegatedAccess()
    {
        $this->constructWithStoredAccessToken(true);

        $microsoftUser = new \Microsoft\Graph\Model\User();

        $this->mockRequest(
            'GET', '/users/no-reply@example.com', \Microsoft\Graph\Model\User::class,
            $this->returnValue($microsoftUser)
        );

        $this->assertEquals(
            $microsoftUser,
            $this->graphRepository->executeGetWithDelegatedAccess(
                '/users/no-reply@example.com', \Microsoft\Graph\Model\User::class
            )
        );
    }

    public function testExecuteGetWithDelegatedAccessWithCollection()
    {
        $this->constructWithStoredAccessToken(true);

        $microsoftUser = new \Microsoft\Graph\Model\User();

        $this->mockCollectionRequest(
            'GET', '/users', \Microsoft\Graph\Model\User::class,
            $this->returnValue([$microsoftUser])
        );

        $this->assertEquals(
            [$microsoftUser],
            $this->graphRepository->executeGetWithDelegatedAccess(
                '/users', \Microsoft\Graph\Model\User::class, true
            )
        );
    }

    public function testExecuteGetWithDelegatedAccessWithoutDelegatedAccessToken()
    {
        $this->constructWithStoredAccessToken();

        $this->oauthProviderMock->expects($this->once())
            ->method('getAuthorizationUrl')
            ->will($this->returnValue('index.php'));

        $this->mockRequest(
            'GET', '/users/no-reply@example.com', \Microsoft\Graph\Model\User::class,
            $this->returnValue(null)
        );

        ob_start();
        $this->graphRepository->executeGetWithDelegatedAccess(
            '/users/no-reply@example.com', \Microsoft\Graph\Model\User::class
        );
        $content = ob_get_clean();

        $this->assertContains('Redirecting to index.php', $content);
    }

    public function testExecuteGetWithDelegatedAccessWithExpiredDelegatedAccessToken()
    {
        $accessToken = new AccessToken(
            [
                'access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA', 'expires' => time() - 1000,
                'refresh_token' => 'cmVmcmVzaF90b2tlbg=='
            ]
        );

        $newAccessToken = new AccessToken(
            ['access_token' => 'eyJ0eXA57892ASWZO435SZSI6SnzA', 'expires' => time() + 1000]
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

        $microsoftUser = new \Microsoft\Graph\Model\User();

        $this->mockRequest(
            'GET', '/users/no-reply@example.com', \Microsoft\Graph\Model\User::class,
            $this->returnValue($microsoftUser)
        );

        $this->assertEquals(
            $microsoftUser,
            $this->graphRepository->executeGetWithDelegatedAccess(
                '/users/no-reply@example.com', \Microsoft\Graph\Model\User::class
            )
        );
    }

    public function testExecutePostWithAccessTokenExpirationRetry()
    {
        $this->constructWithStoredAccessToken();

        $microsoftUser = new \Microsoft\Graph\Model\User();
        $body = ['username' => 'testUser'];

        $this->mockRequest(
            'POST', '/users', \Microsoft\Graph\Model\User::class,
            $this->returnValue($microsoftUser), $body
        );

        $this->assertEquals(
            $microsoftUser,
            $this->graphRepository->executePostWithAccessTokenExpirationRetry(
                '/users', $body, \Microsoft\Graph\Model\User::class
            )
        );
    }

    public function testExecutePostWithDelegateAccess()
    {
        $this->constructWithStoredAccessToken(true);

        $microsoftUser = new \Microsoft\Graph\Model\User();
        $body = ['username' => 'testUser'];

        $this->mockRequest(
            'POST', '/users', \Microsoft\Graph\Model\User::class,
            $this->returnValue($microsoftUser), $body
        );

        $this->assertEquals(
            $microsoftUser,
            $this->graphRepository->executePostWithDelegatedAccess(
                '/users', $body, \Microsoft\Graph\Model\User::class
            )
        );
    }

    public function testExecutePatchWithAccessTokenExpirationRetry()
    {
        $this->constructWithStoredAccessToken();

        $microsoftUser = new \Microsoft\Graph\Model\User();
        $body = ['username' => 'testUser'];

        $this->mockRequest(
            'PATCH', '/users', \Microsoft\Graph\Model\User::class,
            $this->returnValue($microsoftUser), $body
        );

        $this->assertEquals(
            $microsoftUser,
            $this->graphRepository->executePatchWithAccessTokenExpirationRetry(
                '/users', $body, \Microsoft\Graph\Model\User::class
            )
        );
    }

    public function testExecutePatchWithDelegateAccess()
    {
        $this->constructWithStoredAccessToken(true);

        $microsoftUser = new \Microsoft\Graph\Model\User();
        $body = ['username' => 'testUser'];

        $this->mockRequest(
            'PATCH', '/users', \Microsoft\Graph\Model\User::class,
            $this->returnValue($microsoftUser), $body
        );

        $this->assertEquals(
            $microsoftUser,
            $this->graphRepository->executePatchWithDelegatedAccess(
                '/users', $body, \Microsoft\Graph\Model\User::class
            )
        );
    }

    public function testExecuteDeleteWithAccessTokenExpirationRetry()
    {
        $this->constructWithStoredAccessToken();

        $this->mockRequest(
            'DELETE', '/users/5', \Microsoft\Graph\Model\User::class,
            $this->returnValue(null)
        );

        $this->graphRepository->executeDeleteWithAccessTokenExpirationRetry(
            '/users/5', \Microsoft\Graph\Model\User::class
        );
    }

    public function testExecuteDeleteWithDelegateAccess()
    {
        $this->constructWithStoredAccessToken(true);

        $this->mockRequest(
            'DELETE', '/users/5', \Microsoft\Graph\Model\User::class,
            $this->returnValue(null)
        );

        $this->graphRepository->executeDeleteWithDelegatedAccess(
            '/users/5', \Microsoft\Graph\Model\User::class
        );
    }

    /**
     * Constructs the Office365Repository with a valid and stored access token
     *
     * @param bool $includeDelegatedAccessToken
     */
    protected function constructWithStoredAccessToken($includeDelegatedAccessToken = false)
    {
        $accessToken = new AccessToken(
            ['access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA', 'expires' => time() + 1000]
        );

        $this->accessTokenRepositoryMock->expects($this->atLeastOnce())
            ->method('getApplicationAccessToken')
            ->will($this->returnValue($accessToken));

        if ($includeDelegatedAccessToken)
        {
            $this->accessTokenRepositoryMock->expects($this->atLeastOnce())
                ->method('getDelegatedAccessToken')
                ->will($this->returnValue($accessToken));
        }

        $this->graphRepository =
            new GraphRepository($this->oauthProviderMock, $this->graphMock, $this->accessTokenRepositoryMock, '');
    }

    /**
     * @param string $requestMethod
     * @param string $requestUrl
     * @param string $returnType
     * @param \PHPUnit_Framework_MockObject_Stub|null $executeStub
     * @param mixed $body
     */
    protected function mockRequest(
        $requestMethod = null, $requestUrl = null, $returnType = null,
        \PHPUnit_Framework_MockObject_Stub $executeStub = null, $body = null
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

        $bodyMock = $graphRequest->expects($this->any())
            ->method('attachBody')
            ->will($this->returnValue($graphRequest));

        if ($body)
        {
            $bodyMock->with($body);
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

    /**
     * @param string $requestMethod
     * @param string $requestUrl
     * @param string $returnType
     * @param \PHPUnit_Framework_MockObject_Stub|null $executeStub
     * @param int $returnCount
     */
    protected function mockCollectionRequest(
        $requestMethod = null, $requestUrl = null, $returnType = null,
        \PHPUnit_Framework_MockObject_Stub $executeStub = null, $returnCount = 1, $useDataCount = true
    )
    {
        $graphRequest = $this->getMockBuilder(GraphRequest::class)
            ->disableOriginalConstructor()->getMock();

        $graphResponse = $this->getMockBuilder(GraphResponse::class)
            ->disableOriginalConstructor()->getMock();

        if ($useDataCount)
        {
            $graphResponse->expects($this->once())
                ->method('getBody')
                ->will($this->returnValue(['@odata.count' => $returnCount]));
        }
        else
        {
            $graphResponse->expects($this->once())
                ->method('getBody')
                ->will($this->returnValue(['value' => array_fill(0, $returnCount, 1)]));
        }

        $createRequestMock = $this->graphMock->expects($this->once())
            ->method('createCollectionRequest')
            ->will($this->returnValue($graphRequest));

        if ($requestMethod && $requestUrl)
        {
            $createRequestMock->with($requestMethod, $requestUrl);
        }

        $graphRequest->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($graphResponse));

        if ($returnType && $returnCount >= 1)
        {
            $getResponseMock = $graphResponse->expects($this->once())
                ->method('getResponseAsObject')
                ->with($returnType);

            if ($executeStub)
            {
                $getResponseMock->will($executeStub);
            }
        }
    }

}