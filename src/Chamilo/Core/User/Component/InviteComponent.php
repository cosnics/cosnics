<?php

namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Domain\UserInvite\Exceptions\UserAlreadyExistsException;
use Chamilo\Core\User\Form\Handler\InviteFormHandler;
use Chamilo\Core\User\Form\Type\InviteFormType;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserInviteService;
use Chamilo\Libraries\Format\Structure\BreadcrumbGenerator;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\NoPackageBreadcrumbGenerator;

/**
 * @package Chamilo\Core\User\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class InviteComponent extends Manager
{

    /**
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    function run()
    {
        $form = $this->getForm()->create(InviteFormType::class);
        $success = $invalidEmail = false;
        $formData = [];

        try
        {
            $formHandler = $this->getFormHandler();
            $formHandler->setUser($this->getUser());
            $formHandled = $formHandler->handle($form, $this->getRequest());

            if ($formHandled)
            {
                $formData = $form->getData();
                $success = true;
            }
        }
        catch (UserAlreadyExistsException $exception)
        {
            $invalidEmail = true;
            $success = false;
            $formData = $form->getData();
        }

        return $this->getTwig()->render(
            Manager::context() . ':InviteBrowser.html.twig',
            [
                'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(), 'FORM' => $form->createView(),
                'INVALID_EMAIL' => $invalidEmail, 'SUCCESS' => $success,
                'USER_EMAIL' => $formData[InviteFormType::ELEMENT_EMAIL]
            ]
        );
    }

    /**
     * @return \Chamilo\Core\User\Form\Handler\InviteFormHandler
     */
    protected function getFormHandler()
    {
        return new InviteFormHandler($this->getInviteService());
    }

    /**
     * @return UserInviteService
     */
    protected function getInviteService()
    {
        return $this->getService(UserInviteService::class);
    }

    /**
     * @return UserInviteService
     */
    protected function getUserInviteService()
    {
        return $this->getService(UserInviteService::class);
    }

    /**
     * Returns the admin breadcrumb generator
     *
     * @return \Chamilo\Libraries\Format\Structure\BreadcrumbGenerator
     */
    public function get_breadcrumb_generator()
    {
        return new NoPackageBreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }
}