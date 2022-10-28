<?php
namespace Chamilo\Core\Metadata\Vocabulary\Table\Select;

use Chamilo\Core\Metadata\Element\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * Table for the schema
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SelectTable extends DataClassListTableRenderer implements TableActionsSupport
{
    const TABLE_IDENTIFIER = \Chamilo\Core\Metadata\Vocabulary\Manager::PARAM_VOCABULARY_ID;

    /**
     * Returns the implemented form actions
     * 
     * @return TableActions
     */
    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        
        if ($this->get_component()->getSelectedElement()->get_value_limit() != 1)
        {
            $actions->addAction(
                new TableAction(
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ELEMENT_ID => $this->get_component()->getSelectedElementId())),
                    Translation::get('UseSelected')));
        }
        
        return $actions;
    }
}