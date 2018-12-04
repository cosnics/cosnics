<?php
namespace Chamilo\Core\Rights\Ajax\Component;

use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Ajax\Component\UsersFeedComponent;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;

/**
 * Feed to return users from the user entity
 * 
 * @package rights
 * @author Sven Vanpoucke - Hogeschool Gent
 * @deprecated Should not be needed anymore
 */
class UserEntityFeedComponent extends UsersFeedComponent
{

    /**
     * Returns the advanced element finder element for the given user
     * 
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return AdvancedElementFinderElement
     */
    protected function getElementForUser($user)
    {
        return new AdvancedElementFinderElement(
            UserEntity::ENTITY_TYPE . '_' . $user->get_id(), 
            'type type_user', 
            $user->get_fullname(), 
            $user->get_official_code());
    }
}
