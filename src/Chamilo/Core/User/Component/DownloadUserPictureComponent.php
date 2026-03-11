<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
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
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function run(?User $currentUser = null): Response
    {
        return $this->getUserPictureProvider()->downloadUserPicture($this->getUserFromRequest());
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     */
    protected function getUserFromRequest(): User
    {
        $translator = $this->getTranslator();
        $userIdentifier = $this->getRequest()->query->get(Manager::PARAM_USER_ID);

        if (empty($userIdentifier)) {
            throw new NoSuchParameterException(Manager::PARAM_USER_ID);
        }

        $user = $this->getUserService()->findUserByIdentifier($userIdentifier);

        if (empty($user)) {
            throw new NoSuchObjectException(
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
