<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component\AssessmentMerger;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class ObjectTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $content_object)
    {
        switch ($column->get_name())
        {
            case Theme::getInstance()->getCommonImage(
                'Action/Category', 
                'png', 
                Translation::get('Type'), 
                null, 
                ToolbarItem::DISPLAY_ICON) :
                return $content_object->get_icon_image(Theme::ICON_MINI);
            
            case ContentObject::PROPERTY_TITLE :
                return StringUtilities::getInstance()->truncate($content_object->get_title(), 50);
            case ContentObject::PROPERTY_DESCRIPTION :
                return Utilities::htmlentities(
                    StringUtilities::getInstance()->truncate($content_object->get_description(), 50));
        }
        
        return parent::render_cell($column, $content_object);
    }

    public function get_actions($content_object)
    {
        $toolbar = new Toolbar();
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('SelectQuestion'), 
                Theme::getInstance()->getCommonImagePath('Action/Right'), 
                $this->get_component()->get_question_selector_url($content_object->get_id()), 
                ToolbarItem::DISPLAY_ICON));
        
        return $toolbar->as_html();
    }
}
