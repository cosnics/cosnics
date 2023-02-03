<?php
namespace Chamilo\Core\Repository\UserView\Table;

use Chamilo\Core\Repository\UserView\Manager;
use Chamilo\Core\Repository\UserView\Storage\DataClass\UserView;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\UserView\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserViewTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_USER_VIEW_ID;

    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $actions->addAction(
            new TableAction(
                $this->getUrlGenerator()->fromParameters(
                    [Application::PARAM_CONTEXT => Manager::CONTEXT, Manager::PARAM_ACTION => Manager::ACTION_DELETE]
                ), $this->getTranslator()->trans('DeleteSelected', [], StringUtilities::LIBRARIES)
            )
        );

        return $actions;
    }

    protected function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(UserView::class, UserView::PROPERTY_NAME));
        $this->addColumn(new DataClassPropertyTableColumn(UserView::class, UserView::PROPERTY_DESCRIPTION));
    }

    /**
     * @param \Chamilo\Core\Repository\UserView\Storage\DataClass\UserView $userView
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $userView): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $updateUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Application::PARAM_ACTION => Manager::ACTION_UPDATE,
            Manager::PARAM_USER_VIEW_ID => $userView->getId()
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $updateUrl, ToolbarItem::DISPLAY_ICON
            )
        );

        $removeUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Application::PARAM_ACTION => Manager::ACTION_DELETE,
            Manager::PARAM_USER_VIEW_ID => $userView->getId()
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Remove', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'), $removeUrl,
                ToolbarItem::DISPLAY_ICON, true
            )
        );

        return $toolbar->render();
    }
}
