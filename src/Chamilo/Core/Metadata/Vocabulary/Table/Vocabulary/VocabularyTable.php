<?php
namespace Chamilo\Core\Metadata\Vocabulary\Table\Vocabulary;

use Chamilo\Core\Metadata\Vocabulary\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Table for the schema
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class VocabularyTable extends DataClassListTableRenderer implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_VOCABULARY_ID;

    /**
     * Returns the implemented form actions
     * 
     * @return TableActions
     */
    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        
        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DELETE)), 
                Translation::get('RemoveSelected', null, StringUtilities::LIBRARIES)));
        
        return $actions;
    }
}