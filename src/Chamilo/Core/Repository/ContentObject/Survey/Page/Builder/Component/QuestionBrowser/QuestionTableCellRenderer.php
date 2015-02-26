<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\QuestionBrowser;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Description\Storage\DataClass\ComplexDescription;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Storage\DataClass\ComplexOpen;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class QuestionTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    function render_cell($column, $complex_item)
    {
        $question_id = $complex_item->get_ref();
        $question = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object($question_id);
        
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                return $question->get_title();
            case ContentObject :: PROPERTY_DESCRIPTION :
                return $question->get_description();
            case ContentObject :: PROPERTY_TYPE :
                return Translation :: get($question->get_type());
            case Translation :: get('visible') :
                if ($complex_item->get_visible() == 1)
                {
                    return Translation :: get('QuestionVisible');
                }
                else
                {
                    return Translation :: get('QuestionInVisible');
                }
            case ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER :
                return $complex_item->get_display_order();
        }
        
        return parent :: render_cell($column, $complex_item);
    }

    public function get_actions($complex_item)
    {
        $toolbar = new Toolbar();
        
        if ($complex_item->get_visible() == 1)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('ToggleVisibility', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_visible.png', 
                    $this->get_component()->get_change_question_visibility_url($complex_item), 
                    ToolbarItem :: DISPLAY_ICON));
            
            $excluded_type = $complex_item instanceof ComplexDescription ||
                 $complex_item instanceof ComplexOpen;
            if (! $excluded_type)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('Configure', null, Utilities :: COMMON_LIBRARIES), 
                        Theme :: getInstance()->getCommonImagePath() . 'action_build_prerequisites.png', 
                        $this->get_component()->get_configure_question_url($complex_item), 
                        ToolbarItem :: DISPLAY_ICON));
            }
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('ToggleVisibility', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_visible_na.png', 
                    $this->get_component()->get_change_question_visibility_url($complex_item), 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        return $toolbar->as_html();
    }
}
?>