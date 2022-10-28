<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Version;

use Chamilo\Core\Repository\Component\ComparerComponent;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;

class VersionTable extends DataClassListTableRenderer implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_CONTENT_OBJECT_ID;

    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_COMPARE_CONTENT_OBJECTS,
                        ComparerComponent::PARAM_BASE_CONTENT_OBJECT_ID => $this->get_component()->getRequest()->get(
                            Manager::PARAM_CONTENT_OBJECT_ID
                        )
                    ),
                    array(Manager::PARAM_CONTENT_OBJECT_ID)
                ),
                Translation::get('CompareSelected'),
                false
            )
        );

        return $actions;
    }
}
