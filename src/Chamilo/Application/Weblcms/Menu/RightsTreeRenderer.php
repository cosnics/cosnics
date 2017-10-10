<?php
namespace Chamilo\Application\Weblcms\Menu;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use HTML_Menu;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;

/**
 *
 * @package repository.lib
 */
class RightsTreeRenderer extends HtmlMenu
{
    const TREE_NAME = __CLASS__;

    private $groups;

    /**
     *
     * @param $extra_items array An array of extra tree items, added to the root.
     */
    public function __construct($groups)
    {
        $this->groups = $groups;
        $menu = $this->get_menu_items();
        parent::__construct($menu);
    }

    /**
     * Returns the menu items.
     *
     * @param $extra_items array An array of extra tree items, added to the root.
     * @return array An array with all menu items. The structure of this array is the structure needed by
     *         PEAR::HTML_Menu, on which this class is based.
     */
    private function get_menu_items()
    {
        $menu = array();
        $condition = new InCondition(
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID),
            $this->groups);
        $sub_groups = \Chamilo\Core\Group\Storage\DataManager::retrieves(
            Group::class_name(),
            new DataClassRetrievesParameters($condition));
        while ($group = $sub_groups->next_result())
        {
            $sub_menu_item = $this->get_group_array($group);
            $menu[] = $sub_menu_item;
        }
        return $menu;
    }

    private function get_group_array($group)
    {
        $selected_group = array();
        $selected_group['id'] = 'group_' . $group->get_id();
        $selected_group['class'] = 'type type_group';
        $selected_group['title'] = $group->get_name();
        $selected_group['description'] = $group->get_name();
        $selected_group['url'] = '#';
        return $selected_group;
    }

    /**
     * Renders the menu as a tree
     *
     * @return string The HTML formatted tree
     */
    public function render_as_tree()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        $html = array();
        $html[] = '<div class="active_elements" style="overflow: auto; height: 300px; width: 310px;">';
        $html[] = $renderer->toHTML();
        $html[] = '</div>';
        return implode(PHP_EOL, $html);
    }

    public static function get_tree_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::TREE_NAME, true);
    }
}
