<?php
namespace Chamilo\Application\Weblcms\Request\Rights\Storage\DataClass;

use Chamilo\Application\Weblcms\Request\Rights\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Rights\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @author Hans De Bisschop
 */
class RightsLocationEntityRightGroup extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_GROUP_ID = 'group_id';
    public const PROPERTY_LOCATION_ENTITY_RIGHT_ID = 'location_entity_right_id';

    /**
     * The group of the RightsLocationEntityRightGroup
     *
     * @var Group
     */
    private $group;

    /**
     * @var RightsLocationEntityRightGroup
     */
    private $location_entity_right;

    /**
     * Get the default properties
     *
     * @param $extendedPropertyNames string[]
     *
     * @return string[] The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_LOCATION_ENTITY_RIGHT_ID;
        $extendedPropertyNames[] = self::PROPERTY_GROUP_ID;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'weblcms_request_rights_location_entity_right_group';
    }

    public function get_group()
    {
        if (!isset($this->group))
        {
            $this->group = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieve_by_id(
                Group::class, $this->get_group_id()
            );
        }

        return $this->group;
    }

    public function get_group_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_GROUP_ID);
    }

    public function get_location_entity_right()
    {
        if (!isset($this->location_entity_right))
        {
            $this->location_entity_right = DataManager::retrieve_rights_location_entity_right_by_id(
                Manager::CONTEXT, $this->get_location_entity_right_id()
            );
        }

        return $this->location_entity_right;
    }

    public function get_location_entity_right_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_LOCATION_ENTITY_RIGHT_ID);
    }

    public function set_group_id($group_id)
    {
        $this->setDefaultProperty(self::PROPERTY_GROUP_ID, $group_id);
    }

    public function set_location_entity_right_id($location_entity_right_id)
    {
        $this->setDefaultProperty(self::PROPERTY_LOCATION_ENTITY_RIGHT_ID, $location_entity_right_id);
    }
}
