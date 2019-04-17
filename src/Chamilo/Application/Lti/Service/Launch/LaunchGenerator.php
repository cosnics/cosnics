<?php

namespace Chamilo\Application\Lti\Service\Launch;

use Chamilo\Application\Lti\Domain\LaunchParameters\LaunchParameters;
use Chamilo\Application\Lti\Domain\Provider\ProviderInterface;
use Chamilo\Application\Lti\Service\Security\OAuthSecurity;

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
     * @var \Chamilo\Application\Lti\Service\Security\OAuthSecurity
     */
    protected $oauthSecurity;

    /**
     * LaunchGenerator constructor.
     *
     * @param \Twig_Environment $twigRenderer
     * @param \Chamilo\Application\Lti\Service\Security\OAuthSecurity $oauthSecurity
     */
    public function __construct(\Twig_Environment $twigRenderer, OAuthSecurity $oauthSecurity)
    {
        $this->twigRenderer = $twigRenderer;
        $this->oauthSecurity = $oauthSecurity;
    }

    /**
     * @param \Chamilo\Application\Lti\Domain\Provider\ProviderInterface $provider
     * @param \Chamilo\Application\Lti\Domain\LaunchParameters\LaunchParameters $launchParameters
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function generateLaunchHtml(ProviderInterface $provider, LaunchParameters $launchParameters)
    {
        $launchParametersAsArray = $launchParameters->toArray();
        $launchParametersAsArray['oauth_callback'] = 'about:blank';

        $launchParametersAsArray = array_merge(
            $launchParametersAsArray, $this->oauthSecurity->generateSecurityParametersForLaunch(
                $provider, $launchParametersAsArray
            )
        );

        $showInIFrame = $launchParameters->canShowInIFrame();
//
var_dump($launchParametersAsArray);

        return $this->twigRenderer->render(
            'Chamilo\Application\Lti:Launcher.html.twig', [
                'LTI_PARAMETERS' => $launchParametersAsArray,
                'LTI_URL' => $provider->getLaunchUrl(),
                'IFRAME_WIDTH' => $launchParametersAsArray['launch_presentation_width'],
                'IFRAME_HEIGHT' => $launchParametersAsArray['launch_presentation_height'],
                'SHOW_IN_IFRAME' => $showInIFrame
            ]
        );
    }
}