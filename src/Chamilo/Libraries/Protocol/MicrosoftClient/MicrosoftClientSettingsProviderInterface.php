<?php

namespace Chamilo\Libraries\Protocol\MicrosoftClient;

/**
 * Settings provider to support the microsoft client service
 *
 * @author Andras Zolnay - edufiles
 */
interface MicrosoftClientSettingsProviderInterface
{
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
     * Array of scopes that you want the user to consent to.
     *
     * @return array
     */
    public function getScopes();

    /**
     * Return the tenant need to constuct Microsoft service URL's.
     *
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