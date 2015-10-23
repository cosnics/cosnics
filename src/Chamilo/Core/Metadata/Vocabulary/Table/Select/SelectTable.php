<?php
namespace Chamilo\Core\Metadata\Vocabulary\Table\Select;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * Table for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SelectTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = \Chamilo\Core\Metadata\Vocabulary\Manager :: PARAM_VOCABULARY_ID;

    /**
     * Returns the implemented form actions
     *
     * @return TableFormActions
     */
    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self :: TABLE_IDENTIFIER);

        if ($this->get_component()->getSelectedElement()->get_value_limit() != 1)
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Core\Metadata\Element\Manager :: PARAM_ELEMENT_ID => $this->get_component()->getSelectedElementId())),
                    Translation :: get('UseSelected')));
        }

        return $actions;
    }
}