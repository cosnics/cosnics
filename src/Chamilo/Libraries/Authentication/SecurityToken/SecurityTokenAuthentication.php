<?php

namespace Chamilo\Libraries\Authentication\SecurityToken;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\AuthenticationInterface;
use Chamilo\Libraries\Authentication\QueryAuthentication;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Libraries\Authentication\SecurityToken$SecurityTokenAuthentication
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SecurityTokenAuthentication implements AuthenticationInterface
{
    /**
     *
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    protected $request;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * SecurityTokenAuthentication constructor.
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(ChamiloRequest $request, UserService $userService, Translator $translator)
    {
        $this->request = $request;
        $this->userService = $userService;
        $this->translator = $translator;
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     *
     * @throws AuthenticationException
     */
    public function login()
    {
        $securityToken = $this->request->query->get(User::PROPERTY_SECURITY_TOKEN);

        if ($securityToken)
        {
            $user = $this->userService->getUserBySecurityToken($securityToken);

            if (!$user instanceof User)
            {
                throw new AuthenticationException(
                    $this->translator->trans('InvalidSecurityToken', [], 'Chamilo\Libraries')
                );
            }

            return $user;
        }

        return null;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function logout(User $user)
    {
        // TODO: Implement logout() method.
    }
}
