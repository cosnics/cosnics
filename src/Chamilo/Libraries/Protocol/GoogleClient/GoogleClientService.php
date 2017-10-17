<?php
namespace Chamilo\Libraries\Protocol\GoogleClient;

use Chamilo\Libraries\File\Redirect;

/**
 * Initializes and handles the login procedure for the Google Client
 *
 * @package Chamilo\Libraries\Protocol\GoogleClient
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GoogleClientService
{

    /**
     * The google client
     *
     * @var \Google_Client
     */
    protected $googleClient;

    /**
     * The settings provider for the google client
     *
     * @var \Chamilo\Libraries\Protocol\GoogleClient\GoogleClientSettingsProviderInterface
     */
    protected $googleClientSettingsProvider;

    /**
     *
     * @param \Chamilo\Libraries\Protocol\GoogleClient\GoogleClientSettingsProviderInterface $googleClientSettingsProvider
     * @param \Google_Client $googleClient
     */
    public function __construct(GoogleClientSettingsProviderInterface $googleClientSettingsProvider,
        \Google_Client $googleClient = null)
    {
        if (! $googleClient)
        {
            $googleClient = new \Google_Client();
        }

        $this->googleClient = $googleClient;
        $this->googleClientSettingsProvider = $googleClientSettingsProvider;

        $this->initializeGoogleClient();
    }

    /**
     * Initializes the google client
     */
    protected function initializeGoogleClient()
    {
        $this->googleClient->setDeveloperKey($this->googleClientSettingsProvider->getDeveloperKey());
        $this->googleClient->setClientId($this->googleClientSettingsProvider->getClientId());
        $this->googleClient->setClientSecret($this->googleClientSettingsProvider->getClientSecret());
        $this->googleClient->setScopes($this->googleClientSettingsProvider->getScopes());
        $this->googleClient->setAccessType('offline');

        $accessToken = $this->googleClientSettingsProvider->getAccessToken();

        if ($accessToken)
        {
            try
            {
                $this->googleClient->setAccessToken($accessToken);

                if ($this->googleClient->isAccessTokenExpired())
                {
                    $refreshToken = $this->googleClientSettingsProvider->getRefreshToken();

                    if ($refreshToken)
                    {
                        $this->googleClient->refreshToken($refreshToken);
                        $this->googleClientSettingsProvider->saveAccessToken($this->googleClient->getAccessToken());
                    }
                    else
                    {
                        $this->removeUserTokens();
                    }
                }
            }
            catch (\Google_Auth_Exception $exception)
            {
                $this->removeUserTokens();
            }
        }
    }

    /**
     * Removes the access token
     */
    protected function removeUserTokens()
    {
        $this->googleClientSettingsProvider->removeAccessToken();
        $this->googleClientSettingsProvider->removeRefreshToken();

        $redirect = new Redirect();
        $redirect->writeHeader($redirect->getCurrentUrl());
    }

    /**
     * Returns the google client
     *
     * @return \Google_Client
     */
    public function getGoogleClient()
    {
        return $this->googleClient;
    }

    /**
     * Authenticate in the google client
     *
     * @param string $redirectUri
     * @param string $loginCode
     *
     * @throws \Exception
     */
    public function login($redirectUri, $loginCode = null)
    {
        $this->googleClient->setRedirectUri($redirectUri);

        if (! is_null($loginCode))
        {
            $this->googleClient->authenticate($loginCode);
            $this->googleClientSettingsProvider->saveAccessToken($this->googleClient->getAccessToken());
            $this->googleClientSettingsProvider->saveRefreshToken($this->googleClient->getRefreshToken());
        }
        else
        {
            $this->googleClient->setApprovalPrompt('force');
            $url = $this->googleClient->createAuthUrl();

            $redirect = new Redirect();
            $redirect->writeHeader($url);

            exit();
        }
    }
}