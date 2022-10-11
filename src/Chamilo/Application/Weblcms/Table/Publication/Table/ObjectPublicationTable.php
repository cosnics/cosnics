<?php
namespace Chamilo\Application\Weblcms\Table\Publication\Table;

use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;

/**
 * This class represents a table with learning objects which are candidates for publication.
 * 
 * @package application.weblcms
 * @author Original Author Unknown
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to record table
 */
class ObjectPublicationTable extends RecordTable implements TableFormActionsSupport
{
    /**
     * The identifier for the table (used for table actions)
     */
    const TABLE_IDENTIFIER = Manager::PARAM_PUBLICATION_ID;

    public function get_implemented_form_actions(): TableFormActions
    {
        return $this->get_component()->get_actions();
    }
}
