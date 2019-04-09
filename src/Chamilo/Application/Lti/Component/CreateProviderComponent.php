<?php

namespace Chamilo\Application\Lti\Component;

use Chamilo\Application\Lti\Form\ProviderFormType;
use Chamilo\Application\Lti\Manager;
use Chamilo\Application\Lti\Service\LtiProviderService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Application\Lti\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CreateProviderComponent extends \Chamilo\Application\Lti\Manager
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

        $form = $this->getForm()->create(ProviderFormType::class);
        $form->handleRequest($this->getRequest());

        if ($form->isValid())
        {
            try
            {
                $this->getLtiProviderService()->saveLtiProvider($form->getData());
                $message = 'LtiProviderCreated';
                $success = true;
            }
            catch (\Exception $ex)
            {
                $this->getExceptionLogger()->logException($ex);
                $message = 'LtiProviderNotCreated';
                $success = false;
            }

            $this->redirect(
                $this->getTranslator()->trans($message, [], Manager::context()), !$success,
                [self::PARAM_ACTION => self::ACTION_MANAGE_PROVIDERS]
            );
        }

        return $this->getTwig()->render(
            Manager::context() . ':Provider/CreateProvider.html.twig', [
                'HEADER' => $this->render_header(),
                'FORM' => $form->createView(),
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