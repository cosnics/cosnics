<?php
namespace Chamilo\Core\User\Ajax\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Picture\UserPictureProviderFactory;
use Chamilo\Core\User\Storage\DataClass\User;
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

        $userPictureProviderFactory = new UserPictureProviderFactory(Configuration::get_instance());
        $userPictureProvider = $userPictureProviderFactory->getActivePictureProvider();

        return $userPictureProvider->downloadUserPicture($user, $this->getUser());
    }

    /**
     * @return User
     */
    protected function getUserFromRequest()
    {
        $userId = $this->getRequest()->query->get(\Chamilo\Core\User\Manager :: PARAM_USER_USER_ID);
        $user = DataManager:: retrieve_by_id(\Chamilo\Core\User\Storage\DataClass\User:: class_name(), $userId);

        return $user;
    }
}