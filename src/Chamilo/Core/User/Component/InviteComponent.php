<?php

namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Domain\UserInvite\Exceptions\UserAlreadyExistsException;
use Chamilo\Core\User\Form\Handler\InviteFormHandler;
use Chamilo\Core\User\Form\Type\InviteFormType;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserInviteService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
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
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    function run()
    {
        if(!$this->areInvitesAllowed())
        {
            throw new NotAllowedException();
        }

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
                $form = $this->getForm()->create(InviteFormType::class);
            }
        }
        catch (UserAlreadyExistsException $exception)
        {
            $invalidEmail = true;
            $success = false;
            $formData = $form->getData();
        }

        $existingInvites = $this->getInviteService()->getInvitesFromUser($this->getUser());


        return $this->getTwig()->render(
            Manager::context() . ':InviteBrowser.html.twig',
            [
                'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(), 'FORM' => $form->createView(),
                'INVALID_EMAIL' => $invalidEmail, 'SUCCESS' => $success,
                'USER_EMAIL' => $formData[InviteFormType::ELEMENT_EMAIL],
                'EXISTING_INVITES_JSON' => $this->getSerializer()->serialize($existingInvites->getArrayCopy(), 'json')
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

    protected function areInvitesAllowed()
    {
        return $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\User', 'allow_invites']) == 1;
    }
}