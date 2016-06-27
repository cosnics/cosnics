<?php

namespace Chamilo\Libraries\Protocol\MicrosoftClient;

use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Utilities\String\Text;

/**
 * Initializes and handles the login procedure for Microsoft REST API's
 *
 * Code is based on https://azure.microsoft.com/en-us/documentation/articles/active-directory-v2-protocols-oauth-code/
 *
 * Redirect URI: when registering a redirect URI on https://apps.dev.microsoft.com/ just use the base URL of the Chamilo site, i.e my.chamilo.com/index.php.
 *
 * @author Andras Zolnay - edufiles
 */
class MicrosoftClientService
{
    /**
     * The Azure Active Directory client with base URL https://login.microsoftonline.com/<tenant>/oauth2/v2.0/
     *
     * @var \GuzzleHttp\Client
     */
    private $azureActiveDirectoryClient;

    /**
     * The Microsoft Graph client with base URL https://graph.microsoft.com/v1.0/me/ where 'me' resolves to the logged in user.
     *
     * @var \GuzzleHttp\Client
     */
    private $graphClient;

    /**
     * The settings provider for the microsoft client
     *
     * @var MicrosoftClientSettingsProviderInterface
     */
    private $microsoftClientSettingsProvider;

    /**
     * Constructo
     *
     * @param MicrosoftClientSettingsProviderInterface $microsoftClientSettingsProvider
     */
    public function __construct(MicrosoftClientSettingsProviderInterface $microsoftClientSettingsProvider)
    {
        $this->microsoftClientSettingsProvider = $microsoftClientSettingsProvider;
    }

    /**
     * Using the microsoft authentification site, requests an access token granting access to services.
     *
     * Work flow:
     * -# This function is first called with $authenticationCode being null. In this case, the user is redirected to the Microsoft login
     *    page. Note that parameter $replyParameters should contain the URL parameters of the component calling this function. Function
     *    createAuthorizationUrl(...) stores the value of $replyParameters in redirect parameter 'state'.
     * -# User logs in and the Microsoft login page redirects to base URL of our site. The redirect contains two parameters:
     *    - code: authentification code  
     *    - state: value of array $replyParameters sent to the Microsoft login page in previous step.
     *    The function Kernel::handleOAuth2(), resolves the array stored in parameter 'state'. This array contains the URL parameters of the
     *    component which called this function in previous step.  Based on the URL parameters, Kernel::handleOAuth2() can redirect to the
     *    component calling this function in the first step. 
     *    Why do we acctually need parameter 'state' and Kernel::handleOAuth2(). The Microsoft OAuth2 does not allow reply URI's which
     *    contain query string. Thus we cannot ask the Microsoft login page to directly go back the component calling this function and we
     *    have to store the parameters of the calling component in the 'state' variable.
     * -# When called second time, the $authenticationCode should be the value of parameter 'code' sent by the Microsoft login page.
     *
     * @param array $replyParameters Parameters of the component calling this function. We store this array in parameter 'state' which is
     *                               used by Kernel::handleOAuth2() to find the calling component and call this function the second time.

     * @param string $authenticationCode: Should be null if function called the first time. When called second times, the value of 'code' sent by
     *                                    Microsoft login page.
     * @return bool
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
     *  Removes the access token stored by the setting provider.
     */
    public function logout()
    {
        return $this->microsoftClientSettingsProvider->removeAccessToken();
    }
   
    /**
     * Creates a Guzzle HTTP Request..
     *
     * @param string $method, 'POST', 'GET', etc.
     * @param string $endpoint Endpoint of Microsoft Graph REST API, e.g. drive/root/children for listing content of root directory.
     * @return \GuzzleHttp\Message\Request.
     */
    public function createGraphRequest($method, $endpoint)
    {
        return $this->getGraphClient()->createRequest($method, $endpoint);
    }
    
    /**
     * Refreshes access token and sends given request to Microsoft Graph.
     *
     * @param \GuzzleHttp\Message\Request $request
     * @return 
     * - If $shouldDecodeContent is true: \stdClass or false if fails.
     * - Else body of response.
     */
    public function sendGraphRequest(\GuzzleHttp\Message\Request $request, $shouldDecodeContent = true)
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
        $response = $this->getGraphClient()->send($request);
        
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
     * Creates on demand and returns a Guzzle HTTP client with base URL https://login.microsoftonline.com/<tenant>/oauth2/v2.0/.
     *  
     * Typical endpoints: 
     * - authorize: endpoint for requesting authentication code 
     * - token: endpoint for requesting or refreshing access tokens.
     *
     * Example URL's:
     * - Authorization endpoint for tenant 'common': https://login.microsoftonline.com/common/oauth2/v2.0/authorize
     * - Token endpoint for tenant 'organizations': https://login.microsoftonline.com/organizations/oauth2/v2.0/token
     */
    private function getAzureActiveDirectoryClient()
    {
        if (! isset($this->azureActiveDirectoryClient))
        {
            $this->azureActiveDirectoryClient = 
                new \GuzzleHttp\Client(
                    array('base_url' => 'https://login.microsoftonline.com/' . $this->microsoftClientSettingsProvider->getTenant() . '/oauth2/v2.0/'));
        }
        
        return $this->azureActiveDirectoryClient;
    }

    /**
     *  Creates on demand and returns a Guzzle HTTP client with base URL https://graph.microsoft.com/v1.0/me/.
     */
    private function getGraphClient()
    {
        if (! isset($this->graphClient))
        {
            $this->graphClient = new \GuzzleHttp\Client(array('base_url' => 'https://graph.microsoft.com/v1.0/me/'));
        }
        
        return $this->graphClient;
    }

    /**
     * Returns URL of microsoft login page.
     *
     * @return string
     */
    private function createAuthorizationUrl($replyParameters)
    {
        $params = array(
            'client_id' => $this->microsoftClientSettingsProvider->getClientId(),
            'response_type' => 'code',
            'redirect_uri' => $this->getRedirectUri(),
            'scope' => $this->getScopes(),
            'response_mode' => 'query',
            'state' => base64_encode(serialize($replyParameters)),
            'prompt' => 'login');
        /*
            'login_hint' => 'user name or email',
            'domain_hint' => 'consumers or organizations'
        */
           
        return $this->getAzureActiveDirectoryClient()->getBaseUrl() . 'authorize' . "?" . http_build_query($params, '', '&');
    }


    /**
     *  Returns the URI of Chamilo to which Microsoft login page returns after successful login.
     *
     *  @return Base URL of the Chamnilo site, e.g. my.chamilo.com/index.php. Function Kernel::handleOAuth2() will route the redirect back
     *  to component calling function login(...).
     */
    private function getRedirectUri()
    {
        $replyUri = new Redirect();
        return $replyUri->getUrl();
    }

    /**
     * Extends the scopes provided by MicrosoftClientSettingsProvider by scope 'offline_access' which enables refreshing of access tokens.
     */
    private function getScopes()
    {
        $scopes = $this->microsoftClientSettingsProvider->getScopes();
        if (! is_array($scopes))
        {
            $scopes = array($scope);
        }

        if (! in_array('offline_access', $scopes))
        {
            $scopes[] = 'offline_access';
        }

        return implode(' ', $scopes);
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
        $postBody->setField('scope', $this->getScopes());
        $postBody->setField('code', $authorizationCode);
        $postBody->setField('redirect_uri', $this->getRedirectUri());
        $postBody->setField('client_secret', $this->microsoftClientSettingsProvider->getClientSecret());
        
        $response = $this->getAzureActiveDirectoryClient()->send($request);
        $accessToken = json_decode($response->getBody()->getContents());
        
        if (array_key_exists('error', $accessToken))
        {
            throw new \RuntimeException(
                'Requesting access token failed: ' . $accessToken->error_description . '. error code =' . $accessToken->error_code . '.');
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
        $postBody->setField('scope', $this->getScopes());
        $postBody->setField('refresh_token', $this->getAccessToken()->refresh_token);
        $postBody->setField('redirect_uri', $this->getRedirectUri());
        $postBody->setField('client_secret', $this->microsoftClientSettingsProvider->getClientSecret());
        
        $response = $this->getAzureActiveDirectoryClient()->send($request);
        $accessToken = json_decode($response->getBody()->getContents());
        
        if (array_key_exists('error', $accessToken))
        {
            throw new \RuntimeException(
                'Refreshing access token failed: ' . $accessToken->error_description . '. error code =' . $accessToken->error_code . '.');
        }

        return $accessToken;
    }

    
    /**
     *  Adds 'expires_on' attribute to $accessToken and saves the modified token.
     *
     *  expires_on: derived from attibute expires_in and current time and used by function hasAccessTokenExpired().
     *  
     *  @param \stdClass $accessToken
     *  @return bool
     */
    private function saveAccessToken($accessToken)
    {
        $accessToken->expires_on = strtotime('+' . $accessToken->expires_in . 'seconds');
        
        return $this->microsoftClientSettingsProvider->saveAccessToken($accessToken);
    }

    /**
     * Returns the access token stored by the settings provider.
     * @throws \RuntimeException Thrown if no access token has been stored yet.     
     */
    private function getAccessToken()
    {
        $accessToken = $this->microsoftClientSettingsProvider->getAccessToken();

        if (empty($accessToken))
        {
            throw new \RuntimeException('No access token created yet.');   
        }

        return $accessToken;
    }

    /**
     *  Returns whether the access token has expired.
     *  @return boolean
     */
    private function hasAccessTokenExpired()
    {
       return $this->getAccessToken()->expires_on <= time();
    }
}