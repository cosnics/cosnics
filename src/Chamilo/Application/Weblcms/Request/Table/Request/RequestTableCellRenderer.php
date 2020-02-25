<?php
namespace Chamilo\Application\Weblcms\Request\Table\Request;

use Chamilo\Application\Weblcms\Request\Manager;
use Chamilo\Application\Weblcms\Request\Rights\Rights;
use Chamilo\Application\Weblcms\Request\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class RequestTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    function get_actions($object)
    {
        $toolbar = new Toolbar();

        if (Rights::getInstance()->request_is_allowed())
        {
            if (!$object->was_granted())
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('Grant'),
                        Theme::getInstance()->getImagePath('Chamilo\Application\Weblcms\Request', 'Action/Grant'),
                        $this->get_component()->get_url(
                            array(
                                Manager::PARAM_ACTION => Manager::ACTION_GRANT,
                                Manager::PARAM_REQUEST_ID => $object->get_id()
                            )
                        ), ToolbarItem::DISPLAY_ICON
                    )
                );
            }

            if ($object->is_pending())
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('Deny'),
                        Theme::getInstance()->getImagePath('Chamilo\Application\Weblcms\Request', 'Action/Deny'),
                        $this->get_component()->get_url(
                            array(
                                Manager::PARAM_ACTION => Manager::ACTION_DENY,
                                Manager::PARAM_REQUEST_ID => $object->get_id()
                            )
                        ), ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }

        if ($this->get_component()->get_user()->is_platform_admin() ||
            ($this->get_component()->get_user_id() == $object->get_user_id() && $object->is_pending()))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Delete', null, Utilities::COMMON_LIBRARIES),
                    new FontAwesomeGlyph('times'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                            Manager::PARAM_REQUEST_ID => $object->get_id()
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
            case Translation::get('User') :
                return $object->get_user()->get_fullname();
            case Request::PROPERTY_CREATION_DATE :
                return DatetimeUtilities::format_locale_date(null, $object->get_creation_date());
            case Request::PROPERTY_DECISION_DATE :
                return DatetimeUtilities::format_locale_date(null, $object->get_decision_date());
            case Request::PROPERTY_DECISION :
                return $object->get_decision_icon();
            case Request::PROPERTY_CATEGORY_ID :
                $category = DataManager::retrieve_by_id(
                    CourseCategory::class_name(), $object->get_category_id()
                );
                if (!$category)
                {
                    return null;
                }
                else
                {
                    return $category->get_name();
                }
        }

        return parent::render_cell($column, $object);
    }
}

?>