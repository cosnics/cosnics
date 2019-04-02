<?php

namespace Chamilo\Application\Lti\Service;

use Chamilo\Application\Lti\Domain\Application;
use Chamilo\Application\Lti\Domain\LaunchParameters;
use IMSGlobal\LTI\OAuth\OAuthConsumer;
use IMSGlobal\LTI\OAuth\OAuthRequest;
use IMSGlobal\LTI\OAuth\OAuthSignatureMethod_HMAC_SHA1;

/**
 * Use this class to launch an LTI application.
 * The parameters for the LTI launch are defined by the LaunchParameters domain object.
 * LTI can only be launched by adding a form to the html page. Therefor this launcher generates a
 *
 *
 * Class Launcher
 * @package Chamilo\Application\Lti\Service
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class LaunchGenerator
{
    /**
     * @var \Twig_Environment
     */
    protected $twigRenderer;

    /**
     * LaunchGenerator constructor.
     *
     * @param \Twig_Environment $twigRenderer
     */
    public function __construct(\Twig_Environment $twigRenderer)
    {
        $this->twigRenderer = $twigRenderer;
    }

    /**
     * @param \Chamilo\Application\Lti\Domain\Application $application
     * @param \Chamilo\Application\Lti\Domain\LaunchParameters $launchParameters
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function generateLaunchHtml(Application $application, LaunchParameters $launchParameters)
    {
        $launchParametersAsArray = $launchParameters->toArray();
        $launchParametersAsArray['oauth_callback'] = 'about:blank';

        $launchParametersAsArray = array_merge(
            $launchParametersAsArray, $this->generateSecurityParameters($application, $launchParametersAsArray)
        );

        $showInIFrame = $launchParameters->canShowInIFrame();
var_dump($launchParametersAsArray);
        return $this->twigRenderer->render(
            'Chamilo\Application\Lti:Launcher.html.twig', [
                'LTI_PARAMETERS' => $launchParametersAsArray,
                'LTI_URL' => $application->getLtiUrl(),
                'IFRAME_WIDTH' => $launchParametersAsArray['launch_presentation_width'],
                'IFRAME_HEIGHT' => $launchParametersAsArray['launch_presentation_height'],
                'SHOW_IN_IFRAME' => $showInIFrame
            ]
        );
    }

    /**
     * @param \Chamilo\Application\Lti\Domain\Application $application
     * @param array $launchParametersAsArray
     *
     * @return array
     */
    protected function generateSecurityParameters(Application $application, array $launchParametersAsArray)
    {
        $hmacMethod = new OAuthSignatureMethod_HMAC_SHA1();
        $consumer = new OAuthConsumer($application->getKey(), $application->getSecret());

        $request = OAuthRequest::from_consumer_and_token(
            $consumer, null, 'POST', $application->getLtiUrl(), $launchParametersAsArray
        );

        $request->sign_request($hmacMethod, $consumer, null);

        return $request->get_parameters();
    }
}