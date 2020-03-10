<?php
namespace Chamilo\Core\Rights\Editor\Table\LocationEntity;

use Chamilo\Core\Rights\Editor\Manager;
use Chamilo\Core\Rights\RightsUtil;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use function Symfony\Component\VarDumper\Dumper\esc;

/**
 * @package Chamilo\Core\Rights\Editor\Table\LocationEntity
 *
 * @deprecated Should not be needed anymore
 */
abstract class LocationEntityTableCellRenderer extends DataClassTableCellRenderer
{

    public function render_cell($column, $entity_item)
    {
        if (LocationEntityTableColumnModel::is_rights_column($column))
        {
            return $this->get_rights_column_value($column, $entity_item);
        }

        return parent::render_cell($column, $entity_item);
    }

    /**
     * Determines the value of the rights column
     *
     * @param LocationEntityBrowserTableColumn $column
     * @param Object $entity_item
     *
     * @return String
     */
    private function get_rights_column_value($column, $entity_item)
    {
        $locations = $this->get_component()->get_locations();
        $rights = $this->get_component()->get_available_rights();

        $right_id = $rights[$column->get_name()];
        $rights_url = $this->get_component()->get_url(
            array(
                Manager::PARAM_ACTION => Manager::ACTION_SET_ENTITY_RIGHTS,
                Manager::PARAM_ENTITY_ID => $entity_item->get_id(),
                Manager::PARAM_RIGHT_ID => $right_id
            )
        );

        return $this->get_rights_icon($locations[0], $entity_item->get_id(), $right_id, $rights_url);
    }

    /**
     * Determines the icon of the rights column value
     *
     * @param Location $location
     * @param int $entity_item_id
     * @param int $right_id
     * @param String $rights_url
     *
     * @return String
     */
    private function get_rights_icon($location, $entity_item_id, $right_id, $rights_url)
    {
        $locked_parent = $location->get_locked_parent();

        $selected_entity = $this->get_component()->get_selected_entity();
        $selected_entity_type = $selected_entity->get_entity_type();

        $context = $this->get_component()->get_context();

        $rights_util = RightsUtil::getInstance();

        $html = array();

        $html[] = '<div id="r|' . $context . '|' . $right_id . '|' . $selected_entity->get_entity_type() . '|' .
            $entity_item_id . '" style="float: left; width: 24%; text-align: center;">';

        if (!empty($locked_parent))
        {
            $value = $rights_util->is_allowed_for_rights_entity_item_no_inherit(
                $context, $selected_entity_type, $entity_item_id, $right_id, $locked_parent->get_id()
            );

            if ($value == 1)
            {
                $glyph = new FontAwesomeGlyph('lock', array('text-success'), Translation::get('LockedTrue'));
            }
            else
            {
                $glyph = new FontAwesomeGlyph('lock', array('text-danger'), Translation::get('LockedFalse'));
            }

            $html[] = $glyph->render();
        }
        else
        {
            $value = $rights_util->is_allowed_for_rights_entity_item_no_inherit(
                $context, $selected_entity_type, $entity_item_id, $right_id, $location->get_id()
            );

            if (!$value)
            {
                if ($location->inherits())
                {
                    $inherited_value = $rights_util->is_allowed_for_rights_entity_item(
                        $context, $selected_entity_type, $entity_item_id, $right_id, $location
                    );

                    if ($inherited_value)
                    {
                        $html[] = '<a class="setRight" href="' . $rights_url . '">';
                        $html[] = '<div class="rightInheritTrue"></div></a>';
                    }
                    else
                    {
                        $html[] = '<a class="setRight" href="' . $rights_url . '">';
                        $html[] = '<div class="rightFalse"></div></a>';
                    }
                }
                else
                {
                    $html[] = '<a class="setRight" href="' . $rights_url . '">';
                    $html[] = '<div class="rightFalse"></div></a>';
                }
            }
            else
            {
                $html[] = '<a class="setRight" href="' . $rights_url . '">';
                $html[] = '<div class="rightTrue"></div></a>';
            }
        }
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
