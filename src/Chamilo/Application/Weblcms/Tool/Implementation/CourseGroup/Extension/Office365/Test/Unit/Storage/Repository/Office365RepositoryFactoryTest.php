<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Test\Unit\Storage\Repository;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\AccessTokenRepositoryInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\Office365Repository;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\Office365RepositoryFactory;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Platform\ChamiloRequest;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Tests the Office365RepositoryFactory
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Office365RepositoryFactoryTest extends ChamiloTestCase
{
    /**
     * @var Office365RepositoryFactory
     */
    protected $office365RepositoryFactory;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationConsulterMock;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\AccessTokenRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
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

        $this->office365RepositoryFactory = new Office365RepositoryFactory(
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
        unset($this->office365RepositoryFactory);
    }

    public function testBuildOffice365Repository()
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
            ->with(['Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365', 'client_id'])
            ->will($this->returnValue('testClientId'));

        $this->assertInstanceOf(
            Office365Repository::class, $this->office365RepositoryFactory->buildOffice365Repository()
        );
    }

    public function testBuildOffice365RepositorySetsClientId()
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
            ->with(['Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365', 'client_id'])
            ->will($this->returnValue('testClientId'));

        $repository = $this->office365RepositoryFactory->buildOffice365Repository();

        /** @var \League\OAuth2\Client\Provider\GenericProvider $oauthClient */
        $oauthClient = $this->get_property_value($repository, 'oauthProvider');
        $clientId = $this->get_property_value($oauthClient, 'clientId');

        $this->assertEquals('testClientId', $clientId);
    }

    public function testBuildOffice365RepositorySetsClientSecret()
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
            ->with(['Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365', 'client_secret'])
            ->will($this->returnValue('testClientSecret'));

        $repository = $this->office365RepositoryFactory->buildOffice365Repository();

        /** @var \League\OAuth2\Client\Provider\GenericProvider $oauthClient */
        $oauthClient = $this->get_property_value($repository, 'oauthProvider');
        $clientSecret = $this->get_property_value($oauthClient, 'clientSecret');

        $this->assertEquals('testClientSecret', $clientSecret);
    }

    public function testBuildOffice365RepositorySetsTenantId()
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
            ->with(['Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365', 'tenant_id'])
            ->will($this->returnValue('test.onmicrosoft.com'));

        $repository = $this->office365RepositoryFactory->buildOffice365Repository();

        /** @var \League\OAuth2\Client\Provider\GenericProvider $oauthClient */
        $oauthClient = $this->get_property_value($repository, 'oauthProvider');
        $urlAuthorize = $this->get_property_value($oauthClient, 'urlAuthorize');
        $urlAccessToken = $this->get_property_value($oauthClient, 'urlAccessToken');

        $this->assertEquals('https://login.microsoftonline.com/test.onmicrosoft.com/oauth2/authorize', $urlAuthorize);
        $this->assertEquals('https://login.microsoftonline.com/test.onmicrosoft.com/oauth2/token', $urlAccessToken);
    }

    public function testBuildOffice365RepositorySetsState()
    {
        $this->chamiloRequestMock->query = new ParameterBag(['application' => 'Chamilo\Core\Repository']);

        $accessToken = new AccessToken(
            ['access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA', 'expires' => time() + 1000]
        );

        $this->accessTokenRepositoryMock->expects($this->once())
            ->method('getApplicationAccessToken')
            ->will($this->returnValue($accessToken));

        $repository = $this->office365RepositoryFactory->buildOffice365Repository();

        /** @var \League\OAuth2\Client\Provider\GenericProvider $oauthClient */
        $oauthClient = $this->get_property_value($repository, 'oauthProvider');
        $state = $this->get_property_value($oauthClient, 'state');

        $this->assertEquals(
            '{"landingPageParameters":{"application":"Chamilo\\\\Application\\\\Weblcms\\\\Tool\\\\Implementation' .
            '\\\\CourseGroup\\\\Extension\\\\Office365"},"currentUrlParameters":' .
            '{"application":"Chamilo\\\\Core\\\\Repository"}}',
            base64_decode($state)
        );
    }
}

