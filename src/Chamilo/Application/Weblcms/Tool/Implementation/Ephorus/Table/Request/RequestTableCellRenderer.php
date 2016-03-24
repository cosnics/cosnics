<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Request;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * CellRenderer for ephorus requests browser table.
 *
 * @author Tom Goethals - Hogeschool Gent
 */
class RequestTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Renders the cell for a given column and row (object)
     *
     * @param NewObjectTableColumn $column
     * @param DataClass $object
     *
     * @return string
     */
    public function render_cell($column, $object)
    {
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_DESCRIPTION :
                return Utilities :: htmlentities(
                    StringUtilities :: getInstance()->truncate(
                        $object->get_default_property(ContentObject :: PROPERTY_DESCRIPTION),
                        50));
            case RequestTableColumnModel :: COLUMN_NAME_AUTHOR :
                return $object->get_optional_property(User :: PROPERTY_FIRSTNAME) . ' ' .
                     $object->get_optional_property(User :: PROPERTY_LASTNAME);
            case Request :: PROPERTY_REQUEST_TIME :
                return DatetimeUtilities :: format_locale_date(
                    null,
                    $object->get_optional_property(Request :: PROPERTY_REQUEST_TIME));
            case Request :: PROPERTY_STATUS :
                return Request :: status_as_string($object->get_optional_property(Request :: PROPERTY_STATUS));
            case Request :: PROPERTY_PERCENTAGE :
                return $object->get_optional_property(Request :: PROPERTY_PERCENTAGE) . '%';
            case Request :: PROPERTY_VISIBLE_IN_INDEX :
                return $object->get_optional_property(Request :: PROPERTY_VISIBLE_IN_INDEX) ? Translation :: get(
                    'YesVisible') : Translation :: get('NoVisible');
        }

        return parent :: render_cell($column, $object);
    }

    /**
     * Returns the actions toolbar
     *
     * @param DataClass $object
     *
     * @return String
     */
    public function get_actions($object)
    {
        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('ViewResult'),
                Theme :: getInstance()->getCommonImagePath('Action/Reporting'),
                $this->get_component()->get_ephorus_request_url($object->get_id()),
                ToolbarItem :: DISPLAY_ICON));

        if ($object->get_optional_property(Request :: PROPERTY_STATUS) != Request :: STATUS_DUPLICATE)
        {
            if (! $object->get_optional_property(Request :: PROPERTY_VISIBLE_IN_INDEX))
            {
                $icon = 'Action/Invisible';
                $translation = Translation :: get('AddDocumentToIndex');
            }
            else
            {
                $icon = 'Action/Visible';
                $translation = Translation :: get('RemoveDocumentFromIndex');
            }

            $toolbar->add_item(
                new ToolbarItem(
                    $translation,
                    Theme :: getInstance()->getCommonImagePath($icon),
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Manager :: ACTION_INDEX_VISIBILITY_CHANGER,
                            \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager :: ACTION_CHANGE_INDEX_VISIBILITY,
                            Manager :: PARAM_REQUEST_IDS => $object->get_id())),
                    ToolbarItem :: DISPLAY_ICON));
        }

        return $toolbar->as_html();
    }
}
