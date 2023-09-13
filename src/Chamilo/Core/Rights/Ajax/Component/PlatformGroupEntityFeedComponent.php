<?php
namespace Chamilo\Core\Rights\Ajax\Component;

use Chamilo\Core\Group\Ajax\Component\PlatformGroupsFeedComponent;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 * Feed to return the platform groups for the platform group entity
 *
 * @package    roup
 * @author     Sven Vanpoucke
 * @deprecated Should not be needed anymore
 */
class PlatformGroupEntityFeedComponent extends PlatformGroupsFeedComponent
{
    /**
     * The length for the filter prefix to remove
     */
    public const FILTER_PREFIX_LENGTH = 2;

    /**
     * Returns the element for a specific group
     *
     * @param \core\group\Group $group
     *
     * @return AdvancedElementFinderElement
     */
    public function get_group_element(Group $group): AdvancedElementFinderElement
    {
        $description = strip_tags($group->get_fully_qualified_name() . ' [' . $group->get_code() . ']');
        $glyph = new FontAwesomeGlyph('users', [], null, 'fas');

        return new AdvancedElementFinderElement(
            PlatformGroupEntity::ENTITY_TYPE . '_' . $group->get_id(), $glyph->getClassNamesString(),
            $group->get_name(), $description, AdvancedElementFinderElement::TYPE_SELECTABLE_AND_FILTER
        );
    }

    public function get_user_element(User $user): AdvancedElementFinderElement
    {
        $glyph = new FontAwesomeGlyph('user', [], null, 'fas');

        return new AdvancedElementFinderElement(
            UserEntity::ENTITY_TYPE . '_' . $user->get_id(), $glyph->getClassNamesString(), $user->get_fullname(),
            $user->get_official_code()
        );
    }
}
