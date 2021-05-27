<?php
namespace Chamilo\Application\Portfolio\Favourite\Table\Favourite;

use Chamilo\Application\Portfolio\Favourite\Storage\DataClass\UserFavourite;
use Chamilo\Application\Portfolio\Favourite\Storage\Repository\FavouriteRepository;
use Chamilo\Application\Portfolio\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * Cell Renderer for the Favourite Table
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FavouriteTableCellRenderer extends RecordTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Returns the actions toolbar
     *
     * @param User $result
     *
     * @return string
     */
    public function get_actions($result)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        $favouriteContext = \Chamilo\Application\Portfolio\Favourite\Manager::context();

        $toolbar->add_item(
            new ToolbarItem(
                Translation::getInstance()->getTranslation('DeleteFavourite', null, $favouriteContext),
                new FontAwesomeGlyph('star', [], null, 'far'), $this->get_component()->get_url(
                array(
                    Manager::PARAM_ACTION => Manager::ACTION_BROWSE_FAVOURITES,
                    \Chamilo\Application\Portfolio\Favourite\Manager::PARAM_ACTION => \Chamilo\Application\Portfolio\Favourite\Manager::ACTION_DELETE,
                    \Chamilo\Application\Portfolio\Favourite\Manager::PARAM_FAVOURITE_ID => $result[UserFavourite::PROPERTY_ID],
                    \Chamilo\Application\Portfolio\Favourite\Manager::PARAM_SOURCE => \Chamilo\Application\Portfolio\Favourite\Manager::SOURCE_FAVOURITES_BROWSER
                )
            ), ToolbarItem::DISPLAY_ICON, true
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                Translation::getInstance()->getTranslation(
                    'ShowPortfolio',
                    array('USER' => $result[User::PROPERTY_FIRSTNAME] . ' ' . $result[User::PROPERTY_LASTNAME]),
                    Manager::context()
                ), new FontAwesomeGlyph('folder'), $this->get_component()->get_url(
                array(
                    Manager::PARAM_ACTION => Manager::ACTION_HOME,
                    Manager::PARAM_USER_ID => $result[FavouriteRepository::PROPERTY_USER_ID]
                )
            ), ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->render();
    }
}