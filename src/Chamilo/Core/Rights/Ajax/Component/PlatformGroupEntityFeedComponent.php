<?php
namespace Chamilo\Core\Rights\Ajax\Component;

use Chamilo\Core\Group\Ajax\Component\PlatformGroupsFeedComponent;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;

/**
 * Feed to return the platform groups for the platform group entity
 * 
 * @package roup
 * @author Sven Vanpoucke
 */
class PlatformGroupEntityFeedComponent extends PlatformGroupsFeedComponent
{
    /**
     * The length for the filter prefix to remove
     */
    const FILTER_PREFIX_LENGTH = 2;

    /**
     * Returns the element for a specific group
     * 
     * @param \core\group\Group $group
     *
     * @return AdvancedElementFinderElement
     */
    public function get_group_element($group)
    {
        $description = strip_tags($this->getGroupService()->getFullyQualifiedNameForGroup($group) . ' [' . $group->get_code() . ']');
        
        return new AdvancedElementFinderElement(
            PlatformGroupEntity::ENTITY_TYPE . '_' . $group->get_id(), 
            'type type_group', 
            $group->get_name(), 
            $description, 
            AdvancedElementFinderElement::TYPE_SELECTABLE_AND_FILTER);
    }

    /**
     * Returns the element for a specific user
     * 
     * @param \core\user\storage\data_class\User $user
     *
     * @return AdvancedElementFinderElement
     */
    public function get_user_element($user)
    {
        return new AdvancedElementFinderElement(
            UserEntity::ENTITY_TYPE . '_' . $user->get_id(), 
            'type type_user', 
            $user->get_fullname(), 
            $user->get_official_code());
    }
}
