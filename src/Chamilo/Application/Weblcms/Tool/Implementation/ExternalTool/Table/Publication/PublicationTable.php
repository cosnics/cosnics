<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Table\Publication;

use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;

/**
 * Extension on the content object publication table for this tool
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationTable extends ObjectPublicationTable
{

    /**
     * Returns the implemented form actions
     * 
     * @abstract
     *
     *
     *
     *
     *
     *
     *
     *
     *
     * @return TableFormActions
     */
    public function get_implemented_form_actions()
    {
        $actions = $this->get_component()->get_actions();
        
        $actions->set_namespace(__NAMESPACE__);
        
        return $actions;
    }
}