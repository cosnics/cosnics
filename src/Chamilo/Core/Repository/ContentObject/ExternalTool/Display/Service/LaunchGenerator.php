<?php

namespace Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Service;

use Chamilo\Application\Lti\Domain\LaunchParameters\Role\ContextRole;
use Chamilo\Application\Lti\Domain\Provider\ProviderInterface;
use Chamilo\Application\Lti\Service\ProviderService;
use Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Bridge\Interfaces\ExternalToolServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LaunchGenerator
{
    /**
     * @var \Chamilo\Application\Lti\Service\Launch\LaunchGenerator
     */
    protected $ltiLaunchGenerator;

    /**
     * @var \Chamilo\Application\Lti\Service\Launch\LaunchParametersGenerator
     */
    protected $launchParametersGenerator;

    /**
     * @var \Chamilo\Application\Lti\Service\ProviderService
     */
    protected $providerService;

    /**
     * LaunchGenerator constructor.
     *
     * @param \Chamilo\Application\Lti\Service\Launch\LaunchGenerator $ltiLaunchGenerator
     * @param \Chamilo\Application\Lti\Service\Launch\LaunchParametersGenerator $launchParametersGenerator
     * @param \Chamilo\Application\Lti\Service\ProviderService $providerService
     */
    public function __construct(
        \Chamilo\Application\Lti\Service\Launch\LaunchGenerator $ltiLaunchGenerator,
        \Chamilo\Application\Lti\Service\Launch\LaunchParametersGenerator $launchParametersGenerator,
        ProviderService $providerService
    )
    {
        $this->ltiLaunchGenerator = $ltiLaunchGenerator;
        $this->launchParametersGenerator = $launchParametersGenerator;
        $this->providerService = $providerService;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool $externalTool
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @param \Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Bridge\Interfaces\ExternalToolServiceBridgeInterface $externalToolServiceBridge
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function generateLaunchHtml(
        ExternalTool $externalTool, User $user, ExternalToolServiceBridgeInterface $externalToolServiceBridge
    )
    {
        if (!empty($externalTool->getLtiProviderId()))
        {
            $provider = $this->providerService->getProviderById($externalTool->getLtiProviderId());
            if (!$provider instanceof ProviderInterface)
            {
                throw new \RuntimeException(
                    sprintf('The given provider with id %s is no longer found', $provider->getId())
                );
            }
        }
        else
        {
            if (!$externalTool->isValidCustomProvider())
            {
                throw new \RuntimeException(
                    sprintf('The given external tool with id %s is not valid', $externalTool->getId())
                );
            }

            $provider = $externalTool;
        }

        $role = $externalToolServiceBridge->isCourseInstructorInTool() ? ContextRole::ROLE_INSTRUCTOR :
            ContextRole::ROLE_LEARNER;
//        $role = ContextRole::ROLE_LEARNER;

        $launchParameters = $this->launchParametersGenerator->generateLaunchParametersForUser($provider, $user);
        $launchParameters->setContextId($externalToolServiceBridge->getContextIdentifier())
            ->setContextLabel($externalToolServiceBridge->getContextLabel())
            ->setContextTitle($externalToolServiceBridge->getContextTitle())
            ->setResourceLinkId($externalToolServiceBridge->getResourceLinkIdentifier())
            ->setResourceLinkTitle($externalTool->get_title())
            ->addRole(new ContextRole($role));

        if ($externalToolServiceBridge->supportsOutcomesService())
        {
            $this->launchParametersGenerator->generateAndAddResultIdentifier(
                $provider, $launchParameters, $externalToolServiceBridge->getLTIIntegrationClass(),
                $externalToolServiceBridge->getOrCreateResultIdentifierForUser($user)
            );
        }

        return $this->ltiLaunchGenerator->generateLaunchHtml($provider, $launchParameters);
    }
}