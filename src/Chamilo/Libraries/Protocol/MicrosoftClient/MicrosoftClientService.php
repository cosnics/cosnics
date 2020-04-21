<?php
namespace Chamilo\Libraries\Protocol\MicrosoftClient;

use Chamilo\Libraries\File\Redirect;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use RuntimeException;

/**
 * Initializes and handles the login procedure for Microsoft REST API's
 * Code is based on
 * - OAUTH2: https://msdn.microsoft.com/en-us/library/azure/dn645542.aspx
 * - OAUTH2 V2.0: https://azure.microsoft.com/en-us/documentation/articles/active-directory-v2-protocols-oauth-code/
 * Redirect URI: when registering a redirect URI on
 * - OAUTH: https://msdn.microsoft.com/office/office365/howto/add-common-consent-manually#bk_RegisterWebApp
 * - OAUTH2 v2.0: https://apps.dev.microsoft.com/
 * use the base URL of the Chamilo site, i.e my.chamilo.com/index.php.
 *
 * @package Chamilo\Libraries\Protocol\MicrosoftClient
 * @author Andras Zolnay - edufiles
 */
class MicrosoftClientService
{

    /**
     * The Azure Active Directory client with base URL
     * - OAUTH2: https://login.microsoftonline.com/<tenant>/oauth2/
     * - OAUTH2 v2.0: https://login.microsoftonline.com/<tenant>/oauth2/v2.0/
     *
     * @var \Guzzle\Http\Client
     */
    private $azureActiveDirectoryClient;

    /**
     * The Microsoft REST API client with base URL given by Settings provider.
     * Examples:
     * - Microsoft graph: https://graph.microsoft.com/v1.0/me/ where 'me' resolves to the logged in user.
     * - Microsoft share point Video service: https://<company>.sharepoint.com/portals/hub/_api/VideoService
     *
     * @var \Guzzle\Http\Client
     */
    private $microsoftServiceClient;

    /**
     * The settings provider for the microsoft client
     *
     * @var \Chamilo\Libraries\Protocol\MicrosoftClient\MicrosoftClientSettingsProviderInterface
     */
    private $microsoftClientSettingsProvider;

    /**
     *
     * @param \Chamilo\Libraries\Protocol\MicrosoftClient\MicrosoftClientSettingsProviderInterface $microsoftClientSettingsProvider
     */
    public function __construct(MicrosoftClientSettingsProviderInterface $microsoftClientSettingsProvider)
    {
        $this->microsoftClientSettingsProvider = $microsoftClientSettingsProvider;
    }

    /**
     * Using the Microsoft authentication site, requests an access token granting access to services.
     * Work flow:
     * -# This function is first called with $authenticationCode being null. In this case, the user is redirected to the
     * Microsoft login
     * page. Note that parameter $replyParameters should contain the URL parameters of the component calling this
     * function. Function
     * createAuthorizationUrl(...) stores the value of $replyParameters in redirect parameter 'state'.
     * -# User logs in and the Microsoft login page redirects to base URL of our site. The redirect contains two
     * parameters:
     * - code: authentication code
     * - state: value of array $replyParameters sent to the Microsoft login page in previous step.
     * The function Kernel::handleOAuth2(), resolves the array stored in parameter 'state'. This array contains the URL
     * parameters of the
     * component which called this function in previous step. Based on the URL parameters, Kernel::handleOAuth2() can
     * redirect to the
     * component calling this function in the first step.
     * Why do we actually need parameter 'state' and Kernel::handleOAuth2(). The Microsoft OAuth2 does not allow reply
     * URI's which
     * contain query string. Thus we cannot ask the Microsoft login page to directly go back the component calling this
     * function and we
     * have to store the parameters of the calling component in the 'state' variable.
     * -# When called second time, the $authenticationCode should be the value of parameter 'code' sent by the Microsoft
     * login page.
     *
     * @param string[] $replyParameters Parameters of the component calling this function. We store this array in
     *            parameter
     *        'state' which is
     *        used by Kernel::handleOAuth2() to find the calling component and call this function the second time.
     * @param string $authenticationCode: Should be null if function called the first time. When called second times,
     *        the value of 'code' sent by
     *        Microsoft login page.
     * @return boolean
     */
    public function login($replyParameters = null, $authenticationCode = null)
    {
        if (isset($authenticationCode))
        {
            $token = $this->requestAccessToken($authenticationCode);
            return $this->saveAccessToken($token);
        }
        else
        {
            $redirect = new Redirect();
            $redirect->writeHeader($this->createAuthorizationUrl($replyParameters));
        }
    }

    /**
     * Returns whether user has an access token.
     *
     * @return boolean
     */
    public function isUserLoggedIn()
    {
        $accessToken = $this->microsoftClientSettingsProvider->getAccessToken();

        return (! empty($accessToken));
    }

    /**
     * Removes the access token stored by the setting provider.
     *
     * @return boolean
     */
    public function logout()
    {
        return $this->microsoftClientSettingsProvider->removeAccessToken();
    }

    /**
     * Creates a Guzzle HTTP Request..
     *
     * @param string $method, 'POST', 'GET', etc.
     * @param string $endpoint Endpoint of Microsoft REST API, e.g. drive/root/children for listing content of root
     *        directory via Microsoft
     *        graph. $endpoint is concatenated with base URL provided by the setting provider.
     * @return \Guzzle\Http\Message\Request
     */
    public function createRequest($method, $endpoint)
    {
        return $this->getMicrosoftServiceClient()->createRequest($method, $endpoint);
    }

    /**
     * Refreshes access token and sends given request.
     *
     * @param \Guzzle\Http\Message\Request $request
     * @param boolean $shouldDecodeContent
     *
     * @return boolean|\Guzzle\Http\EntityBodyInterface|string
     */
    public function sendRequest(Request $request, $shouldDecodeContent = true)
    {
        if ($this->hasAccessTokenExpired())
        {
            $token = $this->refreshAccessToken();
            if (! $this->saveAccessToken($token))
            {
                return false;
            }
        }

        $request->addHeader('Authorization', 'Bearer ' . $this->getAccessToken()->access_token);
        $response = $this->getMicrosoftServiceClient()->send($request);

        if ($shouldDecodeContent)
        {
            return json_decode($response->getBody()->getContents());
        }
        else
        {
            return $response->getBody();
        }
    }

    /**
     * Creates on demand and returns a Guzzle HTTP client with base URL
     * https://login.microsoftonline.com/<tenant>/oauth2/v2.0/.
     * Typical endpoints:
     * - authorize: endpoint for requesting authentication code
     * - token: endpoint for requesting or refreshing access tokens.
     * Example URL's:
     * - Authorization endpoint for tenant 'common': https://login.microsoftonline.com/common/oauth2/v2.0/authorize
     * - Token endpoint for tenant 'organizations': https://login.microsoftonline.com/organizations/oauth2/v2.0/token
     *
     * @return \Guzzle\Http\Client
     */
    private function getAzureActiveDirectoryClient()
    {
        if (! isset($this->azureActiveDirectoryClient))
        {
            $baseUrl = 'https://login.microsoftonline.com/' . $this->microsoftClientSettingsProvider->getTenant() .
                 '/oauth2';

            if (! empty($this->microsoftClientSettingsProvider->getOauth2Version()))
            { // OUATH2 v2.0
                $baseUrl .= '/' . $this->microsoftClientSettingsProvider->getOauth2Version();
            }

            $baseUrl .= '/';

            $this->azureActiveDirectoryClient = new Client(array('base_url' => $baseUrl));
        }

        return $this->azureActiveDirectoryClient;
    }

    /**
     * Creates on demand and returns a Guzzle HTTP client with base URL provided by the setting provider.
     *
     * @return \Guzzle\Http\Client
     */
    private function getMicrosoftServiceClient()
    {
        if (! isset($this->microsoftServiceClient))
        {
            $this->microsoftServiceClient = new Client(
                array('base_url' => $this->microsoftClientSettingsProvider->getServiceBaseUrl()));
        }

        return $this->microsoftServiceClient;
    }

    /**
     * Returns URL of microsoft login page.
     *
     * @param string[] $replyParameters
     * @return string
     */
    private function createAuthorizationUrl($replyParameters)
    {
        $parameters = array(
            'client_id' => $this->microsoftClientSettingsProvider->getClientId(),
            'response_type' => 'code',
            'redirect_uri' => $this->getRedirectUri(),
            'state' => base64_encode(serialize($replyParameters)),
            'prompt' => 'login' /*
                                 * 'login_hint' => 'user name or email',
                                 * 'domain_hint' => 'consumers or organizations'
                                 */
);

        if (empty($this->microsoftClientSettingsProvider->getOauth2Version()))
        { // OUATH2
            $parameters['resource'] = $this->getScopeOrResource();
        }
        else
        { // OUATH2 v2.0
            $parameters['scope'] = $this->getScopeOrResource();
            $parameters['response_mode'] = 'query';
        }

        return $this->getAzureActiveDirectoryClient()->getBaseUrl() . 'authorize' . "?" .
             http_build_query($parameters, '', '&');
    }

    /**
     * Returns the URI of Chamilo to which Microsoft login page returns after successful login.
     *
     * @return string
     */
    private function getRedirectUri()
    {
        $replyUri = new Redirect();
        return $replyUri->getUrl();
    }

    /**
     * - OAUTH2: Returns resource provided by MicrosoftClientSettingsProvider.
     * - OAUTH2 v2.0: Extends the scopes provided by MicrosoftClientSettingsProvider by scope 'offline_access' which
     * enables refreshing of access tokens.
     *
     * @return string
     */
    private function getScopeOrResource()
    {
        $scopeOrResource = $this->microsoftClientSettingsProvider->getScopeOrResource();

        if (empty($this->microsoftClientSettingsProvider->getOauth2Version()))
        { // OAUTH2
            return $scopeOrResource;
        }
        else
        { // OAUTH2 v2.0
            if (! is_array($scopeOrResource))
            {
                $scopeOrResource = array($scopeOrResource);
            }

            if (! in_array('offline_access', $scopeOrResource))
            {
                $scopeOrResource[] = 'offline_access';
            }

            return implode(' ', $scopeOrResource);
        }
    }

    /**
     * Request the authorization token, after the has logged in and has received an authorization code.
     *
     * @param string $authorizationCode
     * @return \stdClass
     * @throws \RuntimeException Thrown if requesting access token failed.
     */
    private function requestAccessToken($authorizationCode)
    {
        $request = $this->getAzureActiveDirectoryClient()->createRequest('POST', 'token');
        $postBody = $request->getBody();

        $postBody->setField('client_id', $this->microsoftClientSettingsProvider->getClientId());
        $postBody->setField('grant_type', 'authorization_code');

        if (empty($this->microsoftClientSettingsProvider->getOauth2Version()))
        { // OAUTH2
            $postBody->setField('resource', $this->getScopeOrResource());
        }
        else
        { // OAUTH2 v2.0
            $postBody->setField('scope', $this->getScopeOrResource());
        }

        $postBody->setField('code', $authorizationCode);
        $postBody->setField('redirect_uri', $this->getRedirectUri());
        $postBody->setField('client_secret', $this->microsoftClientSettingsProvider->getClientSecret());

        $response = $this->getAzureActiveDirectoryClient()->send($request);
        $accessToken = json_decode($response->getBody()->getContents());

        if (array_key_exists('error', $accessToken))
        {
            throw new RuntimeException(
                'Requesting access token failed: ' . $accessToken->error_description . '. error code =' .
                     $accessToken->error_code . '.');
        }

        return $accessToken;
    }

    /**
     * Refreshes the access token.
     *
     * @return \stdClass
     * @throws \RuntimeException Thrown if requesting access token failed.
     */
    private function refreshAccessToken()
    {
        $request = $this->getAzureActiveDirectoryClient()->createRequest('POST', 'token');
        $postBody = $request->getBody();

        $postBody->setField('client_id', $this->microsoftClientSettingsProvider->getClientId());
        $postBody->setField('grant_type', 'refresh_token');
        $postBody->setField('refresh_token', $this->getAccessToken()->refresh_token);
        $postBody->setField('client_secret', $this->microsoftClientSettingsProvider->getClientSecret());

        if (empty($this->microsoftClientSettingsProvider->getOauth2Version()))
        { // OUATH2
            $postBody->setField('resource', $this->getScopeOrResource());
        }
        else
        { // OUATH2 v2.0
            $postBody->setField('scope', $this->getScopeOrResource());
            $postBody->setField('redirect_uri', $this->getRedirectUri());
        }

        $response = $this->getAzureActiveDirectoryClient()->send($request);
        $accessToken = json_decode($response->getBody()->getContents());

        if (array_key_exists('error', $accessToken))
        {
            throw new RuntimeException(
                'Refreshing access token failed: ' . $accessToken->error_description . '. error code =' .
                     $accessToken->error_code . '.');
        }

        return $accessToken;
    }

    /**
     * Adds 'expires_on' attribute to $accessToken and saves the modified token.
     * expires_on: derived from attibute expires_in and current time and used by function hasAccessTokenExpired().
     *
     * @param \stdClass $accessToken
     * @return boolean
     */
    private function saveAccessToken($accessToken)
    {
        if (is_null($accessToken->expires_on))
        { // OAUTH2 v2.0 does not send the expires_on attribute.
            $accessToken->expires_on = strtotime('+' . $accessToken->expires_in . 'seconds');
        }

        return $this->microsoftClientSettingsProvider->saveAccessToken($accessToken);
    }

    /**
     * Returns the access token stored by the settings provider.
     *
     * @throws \RuntimeException Thrown if no access token has been stored yet.
     */
    private function getAccessToken()
    {
        $accessToken = $this->microsoftClientSettingsProvider->getAccessToken();

        if (empty($accessToken))
        {
            throw new RuntimeException('No access token created yet.');
        }

        return $accessToken;
    }

    /**
     * Returns whether the access token has expired.
     *
     * @return boolean
     */
    private function hasAccessTokenExpired()
    {
        return $this->getAccessToken()->expires_on <= time();
    }
}