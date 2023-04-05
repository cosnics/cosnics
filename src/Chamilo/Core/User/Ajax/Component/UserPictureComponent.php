<?php
namespace Chamilo\Core\User\Ajax\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Picture\UserPictureProviderFactory;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 *
 * @package Chamilo\Core\User\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UserPictureComponent extends \Chamilo\Core\User\Ajax\Manager
{

    /**
     * Runs this component
     *
     * @throws \Exception
     */
    public function run()
    {
        $user = $this->getUserFromRequest();

        $userPictureProviderFactory = new UserPictureProviderFactory(Configuration::getInstance());
        $userPictureProvider = $userPictureProviderFactory->getActivePictureProvider();

        return $userPictureProvider->downloadUserPicture($user, $this->getUser());
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     * @throws NoObjectSelectedException
     * @throws ObjectNotExistException
     */
    protected function getUserFromRequest()
    {
        $userId = $this->getRequest()->get(\Chamilo\Core\User\Manager::PARAM_USER_USER_ID);

        if(empty($userId)) {
            throw new NoObjectSelectedException(
                Translation::getInstance()->getTranslation('User', null, Manager::context())
            );
        }

        $user = DataManager:: retrieve_by_id(\Chamilo\Core\User\Storage\DataClass\User:: class_name(), $userId);

        if(empty($user)) {
            throw new ObjectNotExistException(
                Translation::getInstance()->getTranslation('User', null, Manager::context()),
                $userId
            );
        }

        return $user;
    }
}
