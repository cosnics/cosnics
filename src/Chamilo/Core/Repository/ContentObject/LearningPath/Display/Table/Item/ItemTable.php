<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\Item;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * Portfolio item table
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_STEP;

    /**
     * Returns the implemented form actions
     *
     * @return TableFormActions
     */
    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        if ($this->get_component()->canEditComplexContentObjectPathNode($this->get_component()->get_current_node()))
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(Manager::PARAM_ACTION => Manager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM),
                        array(Manager::PARAM_STEP)
                    ),
                    Translation::get('RemoveSelected')
                )
            );

            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(Manager::PARAM_ACTION => Manager::ACTION_MOVE), array(Manager::PARAM_STEP)
                    ),
                    Translation::get('MoveSelected'),
                    false
                )
            );
        }

        return $actions;
    }
}