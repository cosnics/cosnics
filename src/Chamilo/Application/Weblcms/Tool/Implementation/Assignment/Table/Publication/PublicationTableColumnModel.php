<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Publication;

use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTableColumnModel;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Translation\Translation;

/**
 * Extension on the content object publication table column model for this tool
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationTableColumnModel extends ObjectPublicationTableColumnModel
{

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
        parent::initialize_columns(false);
        
        $this->add_column(
            new DataClassPropertyTableColumn(Assignment::class_name(), Assignment::PROPERTY_END_TIME, null, false));
        
        $this->add_column(
            new StaticTableColumn(
                Manager::PROPERTY_NUMBER_OF_SUBMISSIONS, 
                Translation::getInstance()->getTranslation('NumberOfSubmissions', null, Manager::context())));

        $this->addActionsColumn();
    }
}