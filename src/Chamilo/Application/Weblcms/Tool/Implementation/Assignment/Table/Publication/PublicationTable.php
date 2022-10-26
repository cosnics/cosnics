<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Publication;

use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTable;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;

/**
 * Extension on the content object publication table for this tool
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationTable extends ObjectPublicationTable
{

    public function getTableActions(): TableActions
    {
        $actions = $this->get_component()->get_actions();
        
        $actions->set_namespace(__NAMESPACE__);
        
        return $actions;
    }
}