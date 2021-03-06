<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Table\Publication;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTableCellRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Manager;
use Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool;
use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Translation\Translation;

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
            case ContentObject::PROPERTY_TITLE :
                {
                    if ($publication[ContentObject::PROPERTY_TYPE] == ExternalTool::class)
                    {
                        $details_url = $this->get_component()->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID], 
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_DISPLAY));
                        
                        return '<a href="' . $details_url . '">' . $publication[ContentObject::PROPERTY_TITLE] . '</a>';
                    }
                }
        }
        
        return parent::render_cell($column, $publication);
    }
}