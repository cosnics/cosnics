<?php

namespace Chamilo\Application\Lti\Component;

use Chamilo\Application\Lti\Manager;
use Chamilo\Application\Lti\Service\LtiProviderService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Application\Lti\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ManageProvidersComponent extends \Chamilo\Application\Lti\Manager
{

    /**
     * @return string
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageProviders');

        if (!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        return $this->getTwig()->render(
            Manager::context() . ':Provider/ManageProviders.html.twig', [
                'HEADER' => $this->render_header(),
                'LTI_PROVIDERS_JSON' => $this->getSerializer()->serialize(
                    $this->getLtiProviderService()->findLTIProviders(), 'json'
                ),
                'CREATE_PROVIDER_URL' => $this->get_url([self::PARAM_ACTION => self::ACTION_CREATE_PROVIDER]),
                'UPDATE_PROVIDER_URL' => $this->get_url(
                    [self::PARAM_ACTION => self::ACTION_UPDATE_PROVIDER, self::PARAM_PROVIDER_ID => '__ID__']
                ),
                'DELETE_PROVIDER_URL' => $this->get_url(
                    [self::PARAM_ACTION => self::ACTION_DELETE_PROVIDER, self::PARAM_PROVIDER_ID => '__ID__']
                ),
                'FOOTER' => $this->render_footer()
            ]
        );
    }

    /**
     * @return \Chamilo\Application\Lti\Service\LtiProviderService
     */
    protected function getLtiProviderService()
    {
        return $this->getService(LtiProviderService::class);
    }
}