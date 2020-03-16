<?php
namespace Chamilo\Application\Weblcms\Request\Rights\Table\Entity;

use Chamilo\Application\Weblcms\Request\Rights\Manager;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class EntityTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    function get_actions($object)
    {
        $toolbar = new Toolbar();

        if ($this->get_component()->get_user()->is_platform_admin())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('times'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                            Manager::PARAM_LOCATION_ENTITY_RIGHT_GROUP_ID => $object->get_id()
                        )
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->as_html();
    }

    public function render_cell($column, $object)
    {
        switch ($column->get_name())
        {
            case Translation::get('Type') :
                $location_entity_right = $object->get_location_entity_right();

                switch ($location_entity_right->get_entity_type())
                {
                    case UserEntity::ENTITY_TYPE :
                        $context = 'Chamilo\Core\User';
                        break;
                    case PlatformGroupEntity::ENTITY_TYPE :
                        $context = 'Chamilo\Core\Group';
                        break;
                    default:
                        return '';
                }

                $glyph = new NamespaceIdentGlyph(
                    $context, true, false, false, Theme::ICON_MINI, array()
                );

                return $glyph->render();
            case Translation::get('Entity') :
                $location_entity_right = $object->get_location_entity_right();
                switch ($location_entity_right->get_entity_type())
                {
                    case UserEntity::ENTITY_TYPE :
                        return \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                            User::class_name(), (int) $location_entity_right->get_entity_id()
                        )->get_fullname();
                    case PlatformGroupEntity::ENTITY_TYPE :
                        return DataManager::getInstance()->retrieve_group(
                            (int) $location_entity_right->get_entity_id()
                        )->get_name();
                }
            case Translation::get('Group') :
                return $object->get_group()->get_name();
            case Translation::get('Path') :
                return $object->get_group()->get_fully_qualified_name();
        }

        return parent::render_cell($column, $object);
    }
}

?>