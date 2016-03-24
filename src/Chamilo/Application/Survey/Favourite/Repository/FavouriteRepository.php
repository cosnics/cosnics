<?php
namespace Chamilo\Application\Survey\Favourite\Repository;

use Chamilo\Application\Survey\Favourite\Storage\DataClass\PublicationUserFavourite;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Application\Survey\Favourite\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FavouriteRepository
{

    /**
     *
     * @param integer $identifier
     * @return \Chamilo\Application\Survey\Favourite\Storage\DataClass\PublicationUserFavourite
     */
    public function findFavouriteByIdentifier($identifier)
    {
        return DataManager :: retrieve_by_id(PublicationUserFavourite :: class_name(), $identifier);
    }

    /**
     *
     * @param User $user
     * @param integer $publicationIdentifier
     * @return \Chamilo\Application\Survey\Favourite\Storage\DataClass\PublicationUserFavourite
     */
    public function findPublicationUserFavouriteByUserAndPublicationIdentifier(User $user, $publicationIdentifier)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                PublicationUserFavourite :: class_name(),
                PublicationUserFavourite :: PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                PublicationUserFavourite :: class_name(),
                PublicationUserFavourite :: PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($publicationIdentifier));

        $condition = new AndCondition($conditions);

        return DataManager :: retrieve(
            PublicationUserFavourite :: class_name(),
            new DataClassRetrieveParameters($condition));
    }
}