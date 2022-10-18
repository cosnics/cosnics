<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Table\Publication;

use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTableColumnModel;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

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
     * @param bool $addActionsColumn
     */
    public function initializeColumns($addActionsColumn = true)
    {
        parent::initializeColumns(false);
        
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TYPE));
        
        $this->addActionsColumn();
    }
}