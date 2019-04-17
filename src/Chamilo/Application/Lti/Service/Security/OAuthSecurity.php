<?php

namespace Chamilo\Application\Lti\Service\Security;

use Chamilo\Application\Lti\Domain\Provider\ProviderInterface;
use IMSGlobal\LTI\OAuth\OAuthConsumer;
use IMSGlobal\LTI\OAuth\OAuthException;
use IMSGlobal\LTI\OAuth\OAuthRequest;
use IMSGlobal\LTI\OAuth\OAuthServer;
use IMSGlobal\LTI\OAuth\OAuthSignatureMethod_HMAC_SHA1;
use IMSGlobal\LTI\OAuth\OAuthUtil;
use Symfony\Component\HttpFoundation\Request;

/**
 * Calculates and verifies OAuth security signatures for LTI
 *
 * Class OAuthSecurity
 * @package Chamilo\Application\Lti\Service\Security
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class OAuthSecurity
{
    /**
     * @param \Chamilo\Application\Lti\Domain\Provider\ProviderInterface $provider
     * @param array $launchParametersAsArray
     *
     * @return array
     */
    public function generateSecurityParametersForLaunch(ProviderInterface $provider, array $launchParametersAsArray)
    {
        $hmacMethod = new OAuthSignatureMethod_HMAC_SHA1();
        $consumer = $this->getOAuthConsumerForProvider($provider);

        $request = OAuthRequest::from_consumer_and_token(
            $consumer, null, 'POST', $provider->getLaunchUrl(), $launchParametersAsArray
        );

        $request->sign_request($hmacMethod, $consumer, null);

        return $request->get_parameters();
    }

    /**
     * @param \Chamilo\Application\Lti\Domain\Provider\ProviderInterface $provider
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \IMSGlobal\LTI\OAuth\OAuthException
     */
    public function verifyRequest(ProviderInterface $provider, Request $request)
    {
        $bodyContent = $request->getContent();
        $contentHash = base64_encode(sha1($bodyContent, true));

        $parameters = $request->query->all();
        $parameters = array_merge($parameters, $this->getAuthorizationParametersFromHeader($request));
        $parameters['oauth_body_hash'] = $contentHash;

        $request = new OAuthRequest(
            $request->getMethod(),
            $request->getSchemeAndHttpHost() . ':' . $request->getPort() . $request->getRequestUri(), $parameters
        );

        $store = new OAuthDataStore($this->getOAuthConsumerForProvider($provider));
        $server = new OAuthServer($store);
        $method = new OAuthSignatureMethod_HMAC_SHA1();
        $server->add_signature_method($method);
        //var_dump($request->build_signature($method, $application->toOAuthConsumer(), null));
        $server->verify_request($request);
    }

    /**
     * @param \Chamilo\Application\Lti\Domain\Provider\ProviderInterface $provider
     *
     * @return \IMSGlobal\LTI\OAuth\OAuthConsumer
     */
    protected function getOAuthConsumerForProvider(ProviderInterface $provider)
    {
        return new OAuthConsumer($provider->getKey(), $provider->getSecret());
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     * @throws \IMSGlobal\LTI\OAuth\OAuthException
     */
    protected function getAuthorizationParametersFromHeader(Request $request)
    {
        $authorizationString = $request->headers->get('Authorization');
        if (empty($authorizationString))
        {
            $requestHeaders = OAuthUtil::get_headers();
            $authorizationString = $requestHeaders['Authorization'];
        }

        if (empty($authorizationString) || substr($authorizationString, 0, 6) != 'OAuth ')
        {
            throw new OAuthException(
                'The OAuth authorization parameters could not be found in the Authorization header'
            );
        }

        return OAuthUtil::split_header($authorizationString);
    }
}