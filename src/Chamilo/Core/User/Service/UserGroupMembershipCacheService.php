<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrinePhpFileCacheService;
use Chamilo\Libraries\Cache\Interfaces\UserBasedCacheInterface;
use Chamilo\Libraries\Cache\ParameterBag;

/**
 *
 * @package Chamilo\Core\Menu\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UserGroupMembershipCacheService extends DoctrinePhpFileCacheService implements UserBasedCacheInterface
{
    const PARAM_TYPE = 'type';

    const PARAM_USER_IDENTIFIER = 'user_identifier';

    /**
     *
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCachePathNamespace()
     */
    public function getCachePathNamespace()
    {
        return __NAMESPACE__;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::getIdentifiers()
     */
    public function getIdentifiers()
    {
        return [];
    }

    /**
     *
     * @param User $user
     * @param boolean $type
     *
     * @return mixed
     */
    public function getMembershipsForUserAndType(User $user, $type)
    {
        $parameterBag = new ParameterBag(
            array(self::PARAM_USER_IDENTIFIER => $user->getId(), self::PARAM_TYPE => $type)
        );

        return $this->getForIdentifier($parameterBag);
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::warmUpForIdentifier()
     */
    public function warmUpForIdentifier($identifier)
    {
        $groupMemberships = DataManager::retrieve_all_subscribed_groups_array(
            $identifier->get(self::PARAM_USER_IDENTIFIER), $identifier->get(self::PARAM_TYPE)
        );

        return $this->getCacheProvider()->save((string) $identifier, $groupMemberships);
    }
}