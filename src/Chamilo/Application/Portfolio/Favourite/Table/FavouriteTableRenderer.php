<?php
namespace Chamilo\Application\Portfolio\Favourite\Table;

use Chamilo\Application\Portfolio\Favourite\Manager;
use Chamilo\Application\Portfolio\Favourite\Storage\Repository\FavouriteRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Application\Portfolio\Favourite\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FavouriteTableRenderer extends RecordListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_FAVOURITE_ID;

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromRequest(
                    [
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                        Manager::PARAM_SOURCE => Manager::SOURCE_FAVOURITES_BROWSER
                    ]
                ), $translator->trans('DeleteSelected', [], StringUtilities::LIBRARIES), true
            )
        );

        return $actions;
    }

    protected function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_OFFICIAL_CODE));
    }

    public function renderTableRowActions(TableResultPosition $resultPosition, $result): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $favouriteContext = Manager::CONTEXT;

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('DeleteFavourite', [], $favouriteContext),
                new FontAwesomeGlyph('star', [], null, 'far'), $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT => \Chamilo\Application\Portfolio\Manager::CONTEXT,
                    Application::PARAM_ACTION => \Chamilo\Application\Portfolio\Manager::ACTION_BROWSE_FAVOURITES,
                    Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                    Manager::PARAM_FAVOURITE_ID => $result[DataClass::PROPERTY_ID],
                    Manager::PARAM_SOURCE => Manager::SOURCE_FAVOURITES_BROWSER
                ]
            ), ToolbarItem::DISPLAY_ICON, true
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans(
                    'ShowPortfolio',
                    ['USER' => $result[User::PROPERTY_FIRSTNAME] . ' ' . $result[User::PROPERTY_LASTNAME]],
                    \Chamilo\Application\Portfolio\Manager::CONTEXT
                ), new FontAwesomeGlyph('folder'), $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT => \Chamilo\Application\Portfolio\Manager::CONTEXT,
                    Application::PARAM_ACTION => \Chamilo\Application\Portfolio\Manager::ACTION_HOME,
                    \Chamilo\Application\Portfolio\Manager::PARAM_USER_ID => $result[FavouriteRepository::PROPERTY_USER_ID]
                ]
            ), ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->render();
    }
}
