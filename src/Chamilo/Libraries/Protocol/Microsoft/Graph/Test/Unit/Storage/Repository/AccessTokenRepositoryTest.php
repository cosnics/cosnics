<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Unit\Storage\Repository;

use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\AccessTokenRepository;
use League\OAuth2\Client\Token\AccessToken;

/**
 * Tests the AccessTokenRepository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AccessTokenRepositoryTest extends ChamiloTestCase
{
    /**
     * @var AccessTokenRepository
     */
    protected $accessTokenRepository;

    /**
     * @var \Chamilo\Libraries\Platform\Configuration\LocalSetting | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $localSettingMock;

    /**
     * @var \Chamilo\Libraries\Platform\Session\SessionUtilities | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionUtilitiesMock;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->localSettingMock = $this->getMockBuilder(LocalSetting::class)
            ->disableOriginalConstructor()->getMock();

        $this->sessionUtilitiesMock = $this->getMockBuilder(SessionUtilities::class)
            ->disableOriginalConstructor()->getMock();

        $this->accessTokenRepository = new AccessTokenRepository($this->localSettingMock, $this->sessionUtilitiesMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->localSettingMock);
        unset($this->sessionUtilitiesMock);
        unset($this->accessTokenRepository);
    }

    public function testGetApplicationAccessToken()
    {
        $accessTokenData = '{"token_type":"Bearer","ext_expires_in":"0","expires_on":"1510560404",' .
            '"not_before":"1510556504","resource":"https:\/\/graph.microsoft.com\/",' .
            '"access_token":"eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA","expires":1510330720}';

        $this->localSettingMock->expects($this->once())
            ->method('get')
            ->with('access_token', 'Chamilo\Libraries\Protocol\Microsoft\Graph')
            ->will($this->returnValue($accessTokenData));

        $this->assertInstanceOf(AccessToken::class, $this->accessTokenRepository->getApplicationAccessToken());
    }

    public function testGetApplicationAccessTokenWithEmptyData()
    {
        $this->localSettingMock->expects($this->once())
            ->method('get')
            ->with('access_token', 'Chamilo\Libraries\Protocol\Microsoft\Graph')
            ->will($this->returnValue(null));

        $this->assertEmpty($this->accessTokenRepository->getApplicationAccessToken());
    }

    public function testStoreApplicationAccessToken()
    {
        $accessToken = new AccessToken(['access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA']);
        $this->localSettingMock->expects($this->once())
            ->method('create')
            ->with(
                'access_token', json_encode($accessToken->jsonSerialize()),
                'Chamilo\Libraries\Protocol\Microsoft\Graph'
            );

        $this->accessTokenRepository->storeApplicationAccessToken($accessToken);
    }

    public function testGetDelegatedAccessToken()
    {
        $accessTokenData = '{"token_type":"Bearer","ext_expires_in":"0","expires_on":"1510560404",' .
            '"not_before":"1510556504","resource":"https:\/\/graph.microsoft.com\/",' .
            '"access_token":"eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA","expires":1510330720}';

        $this->sessionUtilitiesMock->expects($this->once())
            ->method('get')
            ->with('graph_delegated_access_token')
            ->will($this->returnValue($accessTokenData));

        $this->assertInstanceOf(AccessToken::class, $this->accessTokenRepository->getDelegatedAccessToken());
    }

    public function testGetDelegatedAccessTokenWithEmptyData()
    {
        $this->sessionUtilitiesMock->expects($this->once())
            ->method('get')
            ->with('graph_delegated_access_token')
            ->will($this->returnValue(null));

        $this->assertEmpty($this->accessTokenRepository->getDelegatedAccessToken());
    }

    public function testStoreDelegatedAccessToken()
    {
        $accessToken = new AccessToken(['access_token' => 'eyJ0eXAiOiJKV1QiLCJub25jZSI6SnzA']);
        $this->sessionUtilitiesMock->expects($this->once())
            ->method('register')
            ->with(
                'graph_delegated_access_token', json_encode($accessToken->jsonSerialize())
            );

        $this->accessTokenRepository->storeDelegatedAccessToken($accessToken);
    }
}

