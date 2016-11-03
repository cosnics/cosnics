<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Table\Publication;

use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTableColumnModel;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;

/**
 * Extension on the content object publication table column model for this tool
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationTableColumnModel extends ObjectPublicationTableColumnModel
{
    const COLUMN_PROGRESS = 'progress';

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        parent :: initialize_columns(false);
        
        $this->add_column(new StaticTableColumn(self :: COLUMN_PROGRESS));
        $this->addActionsColumn();
    }
}