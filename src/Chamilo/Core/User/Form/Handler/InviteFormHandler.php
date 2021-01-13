<?php

namespace Chamilo\Core\User\Form\Handler;

use Chamilo\Core\User\Form\Type\AcceptInviteFormType;
use Chamilo\Core\User\Form\Type\InviteFormType;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserInvite;
use Chamilo\Libraries\Format\Form\FormHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @package Chamilo\Core\User\Form\Handler
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class InviteFormHandler extends FormHandler
{
    /**
     * @var \Chamilo\Core\User\Service\UserInviteService
     */
    protected $userInviteService;

    /**
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    protected $user;

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
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function setUser(\Chamilo\Core\User\Storage\DataClass\User $user): void
    {
        $this->user = $user;
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
        if (!$this->user instanceof User)
        {
            throw new \InvalidArgumentException('The invite form handler can only work with a valid current user');
        }

        if (!parent::handle($form, $request))
        {
            return false;
        }

        $data = $form->getData();

        $this->userInviteService->inviteUser(
            $this->user, $data[InviteFormType::ELEMENT_EMAIL], $data[InviteFormType::ELEMENT_ACCOUNT_VALID_UNTIL],
            $data[InviteFormType::ELEMENT_PERSONAL_MESSAGE]
        );

        return true;
    }

    protected function rollBackModel(FormInterface $form)
    {
        // TODO: Implement rollBackModel() method.
    }
}
