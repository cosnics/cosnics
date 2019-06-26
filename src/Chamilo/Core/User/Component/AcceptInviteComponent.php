<?php

namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\Handler\AcceptInviteFormHandler;
use Chamilo\Core\User\Form\Type\AcceptInviteFormType;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserInviteService;
use Chamilo\Core\User\Storage\DataClass\UserInvite;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Format\Structure\Page;

/**
 * @package Chamilo\Core\User\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AcceptInviteComponent extends Manager implements NoAuthenticationSupport
{
    const PARAM_SECURITY_KEY = 'SecurityKey';

    /**
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Exception
     */
    function run()
    {
        try
        {
            $userInvite = $this->getInviteService()->getUserInviteBySecurityKey(
                $this->getRequest()->getFromUrl(self::PARAM_SECURITY_KEY)
            );
        }
        catch(\Exception $ex)
        {
            $userInvite = null;
        }

        $form = $this->getForm()->create(AcceptInviteFormType::class);

        if($userInvite instanceof UserInvite)
        {
            $formHandler = $this->getFormHandler();
            $formHandler->setUserInvite($userInvite);
            $formHandled = $formHandler->handle($form, $this->getRequest());

            if($formHandled)
            {
                $user = $formHandler->getUser();
                $sessionUtilities = $this->getSessionUtilities();

                $sessionUtilities->clear();
                $sessionUtilities->register('_uid', $user->getId());

                $this->redirect('', false, [], [self::PARAM_ACTION, self::PARAM_SECURITY_KEY, self::PARAM_CONTEXT]);
                exit;
            }
        }

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        return $this->getTwig()->render(
            Manager::context() . ':AcceptInvite.html.twig',
            [
                'HEADER' => $this->render_header(''), 'FOOTER' => $this->render_footer(), 'FORM' => $form->createView(),
                'SITE_NAME' => $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'site_name']),
                'BRAND_IMAGE' => $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Menu', 'brand_image']),
                'INVALID_INVITE' => !($userInvite instanceof UserInvite)
            ]
        );
    }

    /**
     * @return \Chamilo\Core\User\Form\Handler\AcceptInviteFormHandler
     */
    protected function getFormHandler()
    {
        return new AcceptInviteFormHandler($this->getInviteService());
    }

    /**
     * @return UserInviteService
     */
    protected function getInviteService()
    {
        return $this->getService(UserInviteService::class);
    }
}