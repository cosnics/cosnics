<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Table\Item;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Portfolio item table cell renderer
 *
 * @package repository\content_object\page\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Renders a single cell
     *
     * @param RecordTableColumn $column
     * @param \core\repository\common\path\ComplexContentObjectPathNode $node
     *
     * @return String
     */
    public function render_cell($column, $node)
    {
        $content_object = $node->get_content_object();

        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                return $content_object->get_title();
            case ContentObject :: PROPERTY_CREATION_DATE :
                return DatetimeUtilities :: format_locale_date(
                    Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES),
                    $content_object->get_creation_date());
            case ContentObject :: PROPERTY_MODIFICATION_DATE :
                return DatetimeUtilities :: format_locale_date(
                    Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES),
                    $content_object->get_modification_date());
        }

        return parent :: render_cell($column, $node);
    }

    /**
     * Returns the actions toolbar
     *
     * @param \core\repository\common\path\ComplexContentObjectPathNode $node
     * @return string
     */
    public function get_actions($node)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        if ($this->get_component()->get_parent()->is_allowed_to_view_content_object($node))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('ViewerComponent'),
                    Theme :: getInstance()->getImagePath(
                        'Chamilo\Core\Repository\ContentObject\Survey\Page\Display',
                        'Action/' . Manager :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT),
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                            Manager :: PARAM_STEP => $node->get_id())),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('ViewNotAllowed'),
                    Theme :: getInstance()->getImagePath(
                        'Chamilo\Core\Repository\ContentObject\Survey\Page\Display',
                        'Action/' . Manager :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT . 'Na'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }

        if ($this->get_component()->get_parent()->is_allowed_to_edit_content_object($node->get_parent()))
        {
            $variable = $node->get_content_object() instanceof Page ? 'DeleteFolder' : 'DeleterComponent';

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get($variable),
                    Theme :: getInstance()->getImagePath(
                        'Chamilo\Core\Repository\ContentObject\Survey\Page\Display',
                        'Action/' . Manager :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM),
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                            Manager :: PARAM_STEP => $node->get_id())),
                    ToolbarItem :: DISPLAY_ICON,
                    true));
        }

        return $toolbar->as_html();
    }
}