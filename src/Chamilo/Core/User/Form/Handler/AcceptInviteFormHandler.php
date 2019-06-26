<?php

namespace Chamilo\Core\User\Form\Handler;

use Chamilo\Core\User\Form\Type\AcceptInviteFormType;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserInvite;
use Chamilo\Libraries\Format\Form\FormHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @package Chamilo\Core\User\Form\Handler
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AcceptInviteFormHandler extends FormHandler
{
    /**
     * @var \Chamilo\Core\User\Storage\DataClass\UserInvite
     */
    protected $userInvite;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var \Chamilo\Core\User\Service\UserInviteService
     */
    protected $userInviteService;

    /**
     * AcceptInviteFormHandler constructor.
     *
     * @param \Chamilo\Core\User\Service\UserInviteService $userInviteService
     */
    public function __construct(\Chamilo\Core\User\Service\UserInviteService $userInviteService)
    {
        $this->userInviteService = $userInviteService;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\UserInvite $userInvite
     */
    public function setUserInvite(\Chamilo\Core\User\Storage\DataClass\UserInvite $userInvite): void
    {
        $this->userInvite = $userInvite;
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     *
     * @return bool
     * @throws \Exception
     */
    public function handle(FormInterface $form, Request $request): bool
    {
        if (!$this->userInvite instanceof UserInvite)
        {
            throw new \RuntimeException('The user has not been set so the form can not be handled');
        }

        if (!parent::handle($form, $request))
        {
            return false;
        }

        $data = $form->getData();

        $this->user = $this->userInviteService->acceptInvitation(
            $this->userInvite, $data[AcceptInviteFormType::ELEMENT_FIRST_NAME],
            $data[AcceptInviteFormType::ELEMENT_LAST_NAME], $data[AcceptInviteFormType::ELEMENT_EMAIL],
            $data[AcceptInviteFormType::ELEMENT_PASSWORD]
        );

        return true;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        if (empty($this->user))
        {
            throw new \RuntimeException(
                'Could not find the updated user. Please make sure the form is validated first'
            );
        }

        return $this->user;
    }

    protected function rollBackModel(FormInterface $form)
    {
        // TODO: Implement rollBackModel() method.
    }
}