<?php

namespace Chamilo\Application\Lti\Component;

use Chamilo\Application\Lti\Domain\LaunchParameters\CustomLaunchParameter;
use Chamilo\Application\Lti\Service\Integration\TestIntegration;
use Chamilo\Application\Lti\Service\ProviderService;
use Chamilo\Application\Lti\Domain\LaunchParameters\Role\ContextRole;
use Chamilo\Application\Lti\Manager;
use Chamilo\Application\Lti\Service\Launch\LaunchGenerator;
use Chamilo\Application\Lti\Service\Launch\LaunchParametersGenerator;

/**
 * Class LauncherComponent
 *
 * @package Chamilo\Application\Lti
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class LauncherComponent extends Manager
{
    /**
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    function run()
    {
        /** @var LaunchParametersGenerator $launchParametersGenerator */
        $launchParametersGenerator = $this->getService(LaunchParametersGenerator::class);

        /** @var LaunchGenerator $launchGenerator */
        $launchGenerator = $this->getService(LaunchGenerator::class);

        /** @var ProviderService $providerService */
        $providerService = $this->getService(ProviderService::class);
        $provider = $providerService->getProviderById(3);

        $launchParameters = $launchParametersGenerator->generateLaunchParametersForUser($provider, $this->getUser());
        $launchParameters->setContextId('9d5d6098a0763716622ebb48921d548713d1bae8')
            ->setContextLabel('ALGCUR001')
            ->setContextTitle('Demo Cursus')
            ->setResourceLinkId('9d5d6098a0763716622ebb48921d548713d1bae8')
            ->setResourceLinkTitle('BuddyCheck')
            ->addRole(new ContextRole(ContextRole::ROLE_INSTRUCTOR))
            ->addCustomLaunchParameters(new CustomLaunchParameter('opened_resource_id', 1070775));

        $launchParametersGenerator->generateAndAddResultIdentifier($launchParameters, TestIntegration::class, 5);

        $html = [];
        $html[] = $this->render_header();
        $html[] = $launchGenerator->generateLaunchHtml($provider, $launchParameters);
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}