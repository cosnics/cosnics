<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\Item;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Portfolio item table cell renderer
 * 
 * @package repository\content_object\portfolio\display
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
        if ($column instanceof ActionsTableColumn && $this instanceof TableCellRendererActionsColumnSupport)
        {
            return $this->get_actions($node);
        }
        
        $content_object = $node->get_content_object();
        
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_CREATION_DATE :
                return DatetimeUtilities :: format_locale_date(
                    Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES), 
                    $content_object->get_creation_date());
            case ContentObject :: PROPERTY_MODIFICATION_DATE :
                return DatetimeUtilities :: format_locale_date(
                    Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES), 
                    $content_object->get_modification_date());
        }
        
        return $node->get_content_object()->get_default_property($column->get_name());
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
                    Theme :: getInstance()->getImagePath() . 'action/' . Manager :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT .
                         '.png', 
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
                    Theme :: getInstance()->getImagePath() . 'action/' . Manager :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT .
                         '_na.png', 
                        null, 
                        ToolbarItem :: DISPLAY_ICON));
        }
        
        if ($this->get_component()->get_parent()->is_allowed_to_edit_content_object($node->get_parent()))
        {
            $variable = $node->get_content_object() instanceof Portfolio ? 'MoveFolder' : 'MoverComponent';
            
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get($variable), 
                    Theme :: getInstance()->getImagePath() . 'action/' . Manager :: ACTION_MOVE . '.png', 
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_MOVE, 
                            Manager :: PARAM_STEP => $node->get_id())), 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        if ($this->get_component()->get_parent()->is_allowed_to_edit_content_object($node->get_parent()))
        {
            $variable = $node->get_content_object() instanceof Portfolio ? 'DeleteFolder' : 'DeleterComponent';
            
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get($variable), 
                    Theme :: getInstance()->getImagePath() . 'action/' .
                         Manager :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM . '.png', 
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