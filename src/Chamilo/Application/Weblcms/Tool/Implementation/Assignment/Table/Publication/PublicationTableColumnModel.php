<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Publication;

use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTableColumnModel;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication;
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
     *
     * @param bool $addActionsColumn
     */
    public function initializeColumns($addActionsColumn = false)
    {
        parent::initializeColumns($addActionsColumn);
        
        $this->addColumn(
            new DataClassPropertyTableColumn(Assignment::class, Assignment::PROPERTY_END_TIME, null, false));

        $this->addColumn(
            new StaticTableColumn(
                Manager::PROPERTY_NUMBER_OF_SUBMISSIONS, 
                Translation::getInstance()->getTranslation('NumberOfSubmissions', null, Manager::context())));

        $this->addColumn(new StaticTableColumn(Publication::PROPERTY_ENTITY_TYPE, ''), 1);

        $this->addActionsColumn();
    }
}