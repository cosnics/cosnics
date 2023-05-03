<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Table;

use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager;
use Chamilo\Core\Repository\Display\Manager as DisplayManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\Type
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport
{
    public const DEFAULT_ORDER_COLUMN_INDEX = 2;

    public const TABLE_IDENTIFIER = Manager::PARAM_VIRTUAL_USER_ID;

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromRequest(
                    [DisplayManager::PARAM_ACTION => DisplayManager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM]
                ), $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        return $actions;
    }

    protected function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_OFFICIAL_CODE));
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $user): string
    {

        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                $this->getTranslator()->trans('ViewAsUser', ['USER' => $user->get_fullname()]),
                new FontAwesomeGlyph('mask'), $this->getUrlGenerator()->fromRequest(
                [
                    DisplayManager::PARAM_ACTION => Manager::ACTION_USER,
                    Manager::PARAM_VIRTUAL_USER_ID => $user->getId()
                ]
            ), ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->render();
    }
}
