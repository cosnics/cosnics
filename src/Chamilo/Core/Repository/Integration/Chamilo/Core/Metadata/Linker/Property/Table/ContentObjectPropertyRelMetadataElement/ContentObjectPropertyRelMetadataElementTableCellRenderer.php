<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property\Table\ContentObjectPropertyRelMetadataElement;

use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Schema\Storage\DataClass\Schema;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property\Manager;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property\Storage\DataClass\ContentObjectPropertyRelMetadataElement;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Table cell renderer for the ContentObjectPropertyRelMetadataElement data class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectPropertyRelMetadataElementTableCellRenderer extends DataClassTableCellRenderer implements 
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
        if ($column instanceof DataClassPropertyTableColumn)
        {
            switch ($column->get_class_name())
            {
                case ContentObjectPropertyRelMetadataElement :: class_name() :
                    switch ($column->get_name())
                    {
                        case ContentObjectPropertyRelMetadataElement :: PROPERTY_CONTENT_OBJECT_TYPE :
                            return Translation :: get('ContentObjectTypeName', null, $result->get_content_object_type());
                        case ContentObjectPropertyRelMetadataElement :: PROPERTY_PROPERTY_NAME :
                            return Translation :: get(
                                (string) StringUtilities :: getInstance()->createString($result->get_property_name())->upperCamelize(), 
                                null, 
                                $result->get_content_object_type());
                    }
                case Element :: class_name() :
                    $element = \Chamilo\Core\Metadata\Element\Storage\DataManager :: retrieve_by_id(
                        Element :: class_name(), 
                        $result->get_metadata_element_id());
                    
                    $schema = \Chamilo\Core\Metadata\Schema\Storage\DataManager :: retrieve_by_id(
                        Schema :: class_name(), 
                        $element->get_schema_id());
                    
                    return $schema->get_namespace() . ':' . $element->get_name();
            }
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
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), 
                Theme :: getInstance()->getCommonImagesPath() . 'action_edit.png', 
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_UPDATE, 
                        Manager :: PARAM_CONTENT_OBJECT_PROPERTY_REL_METADATA_ELEMENT_ID => $result->get_id())), 
                ToolbarItem :: DISPLAY_ICON));
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), 
                Theme :: getInstance()->getCommonImagesPath() . 'action_delete.png', 
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_DELETE, 
                        Manager :: PARAM_CONTENT_OBJECT_PROPERTY_REL_METADATA_ELEMENT_ID => $result->get_id())), 
                ToolbarItem :: DISPLAY_ICON));
        
        return $toolbar->as_html();
    }
}