<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component\Browser;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class ComplexTable extends DataClassListTableRenderer implements TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID;

    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $action = [
            Manager::PARAM_ACTION => Manager::ACTION_COPY_COMPLEX_CONTENT_OBJECT_ITEM
        ];

        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url($action),
                Translation::get('CopySelected', null, StringUtilities::LIBRARIES)
            ), true
        );

        $action = [
            Manager::PARAM_ACTION => Manager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM
        ];

        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url($action),
                Translation::get('RemoveSelected', null, StringUtilities::LIBRARIES)
            ), true
        );

        $action = [
            Manager::PARAM_ACTION => Manager::ACTION_CHANGE_PARENT
        ];

        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url($action),
                Translation::get('MoveSelected', null, StringUtilities::LIBRARIES), false
            )
        );

        return $actions;
    }
}
