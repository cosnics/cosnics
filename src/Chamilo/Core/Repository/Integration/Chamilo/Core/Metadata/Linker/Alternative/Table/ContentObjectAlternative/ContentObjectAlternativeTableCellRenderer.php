<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Alternative\Table\ContentObjectAlternative;

use Chamilo\Core\Metadata\ControlledVocabulary\Storage\DataClass\ControlledVocabulary;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Alternative\Manager;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Alternative\Storage\DataClass\ContentObjectAlternative;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Storage\DataClass\ContentObjectMetadataElementValue;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Table cell renderer for the ContentObjectAlternative data class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectAlternativeTableCellRenderer extends RecordTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{

    /**
     * Renders a single cell
     * 
     * @param TableColumn $column
     * @param mixed $result
     *
     * @return string
     */
    public function render_cell($column, $result)
    {
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TYPE :
                $type = $result[ContentObject :: PROPERTY_TYPE];
                $context = ClassnameUtilities :: getInstance()->getNamespaceFromClassname($type);
                
                return Theme :: getInstance()->getImage(
                    'Logo/16', 
                    'png', 
                    Translation :: get('TypeName', null, $context), 
                    null, 
                    ToolbarItem :: DISPLAY_ICON, 
                    false, 
                    $context);
            case ContentObjectMetadataElementValue :: PROPERTY_VALUE :
                $value = $result[ContentObjectMetadataElementValue :: PROPERTY_VALUE];
                if ($value)
                {
                    return $value;
                }
                else
                {
                    $controlled_vocabulary_id = $result[ContentObjectMetadataElementValue :: PROPERTY_ELEMENT_VOCABULARY_ID];
                    if ($controlled_vocabulary_id)
                    {
                        $controlled_vocabulary = \Chamilo\Core\Metadata\ControlledVocabulary\Storage\DataManager :: retrieve_by_id(
                            ControlledVocabulary :: class_name(), 
                            $controlled_vocabulary_id);
                        
                        return $controlled_vocabulary->get_value();
                    }
                }
                
                return Theme :: getInstance()->getCommonImage(
                    'status_warning_mini', 
                    'png', 
                    Translation :: get('NoMetadataValue'), 
                    null, 
                    null, 
                    null, 
                    ToolbarItem :: DISPLAY_ICON);
            
            case ContentObject :: PROPERTY_DESCRIPTION :
                return Utilities :: truncate_string(strip_tags($result[ContentObject :: PROPERTY_DESCRIPTION]), 125);
        }
        
        return parent :: render_cell($column, $result);
    }

    /**
     * Returns the actions toolbar
     * 
     * @param mixed $result
     *
     * @return String
     */
    public function get_actions($result)
    {
        $id = $result[ContentObjectAlternative :: PROPERTY_ID];
        
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), 
                Theme :: getInstance()->getCommonImagePath() . 'action_edit.png', 
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_UPDATE, 
                        Manager :: PARAM_CONTENT_OBJECT_ALTERNATIVE_ID => $id)), 
                ToolbarItem :: DISPLAY_ICON));
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), 
                Theme :: getInstance()->getCommonImagePath() . 'action_delete.png', 
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_DELETE, 
                        Manager :: PARAM_CONTENT_OBJECT_ALTERNATIVE_ID => $id)), 
                ToolbarItem :: DISPLAY_ICON));
        
        return $toolbar->as_html();
    }
}