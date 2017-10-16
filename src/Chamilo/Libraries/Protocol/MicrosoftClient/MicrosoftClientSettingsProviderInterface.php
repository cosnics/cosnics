<?php
namespace Chamilo\Libraries\Protocol\MicrosoftClient;

/**
 * Settings provider to support the microsoft client service
 *
 * @package Chamilo\Libraries\Protocol\MicrosoftClient
 *
 * @author Andras Zolnay - edufiles
 */
interface MicrosoftClientSettingsProviderInterface
{

    /**
     * Returns the base URL of the accessed Microsoft REST API.
     * Examples:
     * - Microsoft graph: 'https://graph.microsoft.com/v1.0/me/' where 'me' resolves to the logged in user.
     * - Microsoft share point Video service: https://<company>.sharepoint.com/portals/hub/_api/VideoService
     * 
     * @return string Please ensure that the returned string contains a trailing '/'.
     */
    public function getServiceBaseUrl();

    /**
     * Returns the OAUTH2 protocol version.
     * This string concatenated with 'https://login.microsoftonline.com/<tenant>/oauth2/' makes up the Microsoft
     * authentication service URL.
     * 
     * Derived classes should return one of the following values.
     *         - OAUTH2: ''
     *         - OAUTH2 v2.0: 'v2.0'
     *
     * @return string
     */
    public function getOauth2Version();

    /**
     * Returns the client id for the microsoft client
     * 
     * @return string
     */
    public function getClientId();

    /**
     * Returns the client secret for the microsoft client
     * 
     * @return string
     */
    public function getClientSecret();

    /**
     * Array of scopes or resource that you want the user to consent to.
     * 
     * Mircosoft OAUTH2: string containing the resource. The App ID URI of the web API. To find the App ID URI
     *         of the web API, in the
     *         Azure Management Portal, click Active Directory, click the directory, click the application and then
     *         click Configure.
     *         E.g. 'https://<company>.sharepoint.com'
     *         - Mircosoft OAUTH2 v2.0: scopes in string array e.g. array('https://graph.microsoft.com/Files.Read')
     *
     * @return string[]
     */
    public function getScopeOrResource();

    /**
     * Return the tenant need to constuct Microsoft service URL's.
     * Allowed values: common, organizations, consumers, and tenant identifiers.
     * 
     * @return string
     */
    public function getTenant();

    /**
     * Stores the access token from the microsoft client into chamilo
     * 
     * @param \stdClass $accessToken
     *
     * @return bool
     */
    public function saveAccessToken($accessToken);

    /**
     * Returns the saved access token
     * 
     * @return \stdClass Returns null if no access token saved yet.
     */
    public function getAccessToken();

    /**
     * Removes the access token
     * 
     * @return bool
     */
    public function removeAccessToken();
}