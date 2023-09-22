<?php
namespace Chamilo\Core\User\Ajax\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;

/**
 * @package Chamilo\Core\User\Ajax
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class UserPictureComponent extends \Chamilo\Core\User\Ajax\Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function run()
    {
        $user = $this->getUserFromRequest();

        $userPictureProvider = $this->getService('Chamilo\Core\User\Picture\UserPictureProvider');

        return $userPictureProvider->downloadUserPicture($user, $this->getUser());
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function getUserFromRequest(): User
    {
        $translator = $this->getTranslator();
        $userIdentifier = $this->getRequest()->query->get(Manager::PARAM_USER_USER_ID);

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
}
