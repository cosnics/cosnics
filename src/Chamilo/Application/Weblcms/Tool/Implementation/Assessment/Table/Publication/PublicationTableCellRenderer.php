<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Table\Publication;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTableCellRenderer;
use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Platform\Translation;

/**
 * Extension on the content object publication table cell renderer for this tool
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationTableCellRenderer extends ObjectPublicationTableCellRenderer
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Renders a cell for a given object
     * 
     * @param $column \libraries\ObjectTableColumn
     *
     * @param mixed $publication
     *
     * @return String
     */
    public function render_cell($column, $publication)
    {
        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TYPE :
                {
                    $type = Translation::get('TypeName', $publication[ContentObject::PROPERTY_TYPE]);
                    
                    if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
                    {
                        return '<span style="color: gray">' . $type . '</span>';
                    }
                    else
                    {
                        return $type;
                    }
                    
                    break;
                }
            case ContentObject::PROPERTY_TITLE :
                {
                    if ($publication[ContentObject::PROPERTY_TYPE] == Hotpotatoes::class_name())
                    {
                        $details_url = $this->get_component()->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID], 
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT));
                        
                        return '<a href="' . $details_url . '">' . $publication[ContentObject::PROPERTY_TITLE] . '</a>';
                    }
                }
        }
        
        return parent::render_cell($column, $publication);
    }
}