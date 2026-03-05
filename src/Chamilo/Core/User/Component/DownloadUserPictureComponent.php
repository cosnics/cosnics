<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exception\NoObjectSelectedException;
use Chamilo\Libraries\Storage\Architecture\Exception\ObjectNotExistException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class DownloadUserPictureComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectNotExistException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function run(?User $currentUser = null): Response
    {
        return $this->getUserPictureProvider()->downloadUserPicture($this->getUserFromRequest());
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectNotExistException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function getUserFromRequest(): User
    {
        $translator = $this->getTranslator();
        $userIdentifier = $this->getRequest()->query->get(Manager::PARAM_USER_ID);

        if (empty($userIdentifier))
        {
            throw new NoObjectSelectedException(
                $translator->trans('User', [], Manager::CONTEXT)
            );
        }

        $user = $this->getUserService()->findUserByIdentifier($userIdentifier);

        if (empty($user))
        {
            throw new ObjectNotExistException(
                $translator->trans('User', [], Manager::CONTEXT), $userIdentifier
            );
        }

        return $user;
    }

    /**
     * @param class-string<\Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface> $className
     */
    public function getUserPictureProvider(string $className = 'Chamilo\Core\User\Service\UserPictureProvider'
    ): UserPictureProviderInterface
    {
        return $this->getService($className);
    }
}
