<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Presence\Table\Publication;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTableColumnModel;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\DataClass\Publication;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;

/**
 * Extension on the content object publication table column model for this tool
 * 
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PublicationTableColumnModel extends ObjectPublicationTableColumnModel
{
    const DEFAULT_ORDER_COLUMN_INDEX = 8;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Initializes the columns for the table
     *
     * @param bool $addActionsColumn
     */
    public function initialize_columns($addActionsColumn = false)
    {
        parent::initialize_columns($addActionsColumn);
        
        $this->addActionsColumn();

        // todo: check
        /*if(!$this->get_component()->get_tool_browser()->get_parent()->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $this->delete_column(7);
            $this->delete_column(7);
        }*/
    }
}
