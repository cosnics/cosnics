<?php
namespace Chamilo\Core\Menu;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Rights\RightsUtil;

/**
 *
 * @package Chamilo\Core\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Rights extends RightsUtil
{
    const VIEW_RIGHT = 1;
    const TYPE_ITEM = 1;

    private static $instance;

    /**
     *
     * @return Rights
     */
    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get_available_rights()
    {
        return array('View' => self::VIEW_RIGHT);
    }

    public function set_location_entity_right($right_id, $entity_id, $entity_type, $location_id)
    {
        return parent::set_location_entity_right(__NAMESPACE__, $right_id, $entity_id, $entity_type, $location_id);
    }

    public function create_menu_location($item_id, $parent_id)
    {
        return parent::create_location(
            __NAMESPACE__,
            Rights::TYPE_ITEM,
            $item_id,
            0,
            $parent_id,
            0,
            0,
            self::TREE_TYPE_ROOT,
            true);
    }

    public function is_allowed($right, $context, $user_id, $entities, $identifier = 0, $type = self :: TYPE_ROOT, $tree_identifier = 0,
        $tree_type = self :: TREE_TYPE_ROOT)
    {
        $setting = Configuration::getInstance()->get_setting(array(__NAMESPACE__, 'enable_rights'));

        if ($setting == 1)
        {
            return parent::is_allowed(
                $right,
                $context,
                $user_id,
                $entities,
                $identifier,
                $type,
                $tree_identifier,
                $tree_type);
        }
        else
        {
            return true;
        }
    }
}
