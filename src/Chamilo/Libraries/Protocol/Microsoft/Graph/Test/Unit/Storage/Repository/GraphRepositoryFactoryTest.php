<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Unit\Storage\Repository;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\AccessTokenRepositoryInterface;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepositoryFactory;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Tests the GraphRepositoryFactory
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GraphRepositoryFactoryTest extends ChamiloTestCase
{
    /**
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepositoryFactory
     */
    protected $graphRepositoryFactory;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationConsulterMock;

    /**
     * @var AccessTokenRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $accessTokenRepositoryMock;

    /**
     * @var \Chamilo\Libraries\Platform\ChamiloRequest | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $chamiloRequestMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->configurationConsulterMock = $this->getMockBuilder(ConfigurationConsulter::class)
            ->disableOriginalConstructor()->getMock();

        $this->accessTokenRepositoryMock = $this->getMockBuilder(AccessTokenRepositoryInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->chamiloRequestMock = $this->getMockBuilder(ChamiloRequest::class)
            ->disableOriginalConstructor()->getMock();

        $this->graphRepositoryFactory = new GraphRepositoryFactory(
            $this->configurationConsulterMock, $this->accessTokenRepositoryMock, $this->chamiloRequestMock
        );
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->configurationConsulterMock);
        unset($this->accessTokenRepositoryMock);
        unset($this->chamiloRequestMock);
        unset($this->graphRepositoryFactory);
    }

    public function testBuildGraphRepository()
    {
        $this->chamiloRequestMock->query = new ParameterBag();

        $accessToken = new AccessToken(
            ['access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA', 'expires' => time() + 1000]
        );

        $this->accessTokenRepositoryMock->expects($this->once())
            ->method('getApplicationAccessToken')
            ->will($this->returnValue($accessToken));

        $this->configurationConsulterMock->expects($this->at(0))
            ->method('getSetting')
            ->with(['Chamilo\Libraries\Protocol\Microsoft\Graph', 'client_id'])
            ->will($this->returnValue('testClientId'));

        $this->assertInstanceOf(
            GraphRepository::class, $this->graphRepositoryFactory->buildGraphRepository()
        );
    }

    public function testBuildGraphRepositorySetsClientId()
    {
        $this->chamiloRequestMock->query = new ParameterBag();

        $accessToken = new AccessToken(
            ['access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA', 'expires' => time() + 1000]
        );

        $this->accessTokenRepositoryMock->expects($this->once())
            ->method('getApplicationAccessToken')
            ->will($this->returnValue($accessToken));

        $this->configurationConsulterMock->expects($this->at(0))
            ->method('getSetting')
            ->with(['Chamilo\Libraries\Protocol\Microsoft\Graph', 'client_id'])
            ->will($this->returnValue('testClientId'));

        $repository = $this->graphRepositoryFactory->buildGraphRepository();

        /** @var \League\OAuth2\Client\Provider\GenericProvider $oauthClient */
        $oauthClient = $this->get_property_value($repository, 'oauthProvider');
        $clientId = $this->get_property_value($oauthClient, 'clientId');

        $this->assertEquals('testClientId', $clientId);
    }

    public function testBuildGraphRepositorySetsClientSecret()
    {
        $this->chamiloRequestMock->query = new ParameterBag();

        $accessToken = new AccessToken(
            ['access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA', 'expires' => time() + 1000]
        );

        $this->accessTokenRepositoryMock->expects($this->once())
            ->method('getApplicationAccessToken')
            ->will($this->returnValue($accessToken));

        $this->configurationConsulterMock->expects($this->at(1))
            ->method('getSetting')
            ->with(['Chamilo\Libraries\Protocol\Microsoft\Graph', 'client_secret'])
            ->will($this->returnValue('testClientSecret'));

        $repository = $this->graphRepositoryFactory->buildGraphRepository();

        /** @var \League\OAuth2\Client\Provider\GenericProvider $oauthClient */
        $oauthClient = $this->get_property_value($repository, 'oauthProvider');
        $clientSecret = $this->get_property_value($oauthClient, 'clientSecret');

        $this->assertEquals('testClientSecret', $clientSecret);
    }

    public function testBuildGraphRepositorySetsTenantId()
    {
        $this->chamiloRequestMock->query = new ParameterBag();

        $accessToken = new AccessToken(
            ['access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA', 'expires' => time() + 1000]
        );

        $this->accessTokenRepositoryMock->expects($this->once())
            ->method('getApplicationAccessToken')
            ->will($this->returnValue($accessToken));

        $this->configurationConsulterMock->expects($this->at(2))
            ->method('getSetting')
            ->with(['Chamilo\Libraries\Protocol\Microsoft\Graph', 'tenant_id'])
            ->will($this->returnValue('test.onmicrosoft.com'));

        $repository = $this->graphRepositoryFactory->buildGraphRepository();

        /** @var \League\OAuth2\Client\Provider\GenericProvider $oauthClient */
        $oauthClient = $this->get_property_value($repository, 'oauthProvider');
        $urlAuthorize = $this->get_property_value($oauthClient, 'urlAuthorize');
        $urlAccessToken = $this->get_property_value($oauthClient, 'urlAccessToken');

        $this->assertEquals('https://login.microsoftonline.com/test.onmicrosoft.com/oauth2/authorize', $urlAuthorize);
        $this->assertEquals('https://login.microsoftonline.com/test.onmicrosoft.com/oauth2/token', $urlAccessToken);
    }

    public function testBuildGraphRepositorySetsState()
    {
        $this->chamiloRequestMock->query = new ParameterBag(['application' => 'Chamilo\Core\Repository']);

        $accessToken = new AccessToken(
            ['access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA', 'expires' => time() + 1000]
        );

        $this->accessTokenRepositoryMock->expects($this->once())
            ->method('getApplicationAccessToken')
            ->will($this->returnValue($accessToken));

        $repository = $this->graphRepositoryFactory->buildGraphRepository();

        /** @var \League\OAuth2\Client\Provider\GenericProvider $oauthClient */
        $oauthClient = $this->get_property_value($repository, 'oauthProvider');
        $state = $this->get_property_value($oauthClient, 'state');

        $this->assertEquals(
            '{"landingPageParameters":{"application":"Chamilo\\\\Libraries\\\\Protocol\\\\Microsoft\\\\Graph"}' .
            ',"currentUrlParameters":{"application":"Chamilo\\\\Core\\\\Repository"}}',
            base64_decode($state)
        );
    }
}

