<?php
namespace Chamilo\Core\Repository\Workspace\Table\Workspace;

use Chamilo\Core\Repository\Workspace\Favourite\Repository\FavouriteRepository;
use Chamilo\Core\Repository\Workspace\Favourite\Service\FavouriteService;
use Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite;
use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Table\Workspace
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     *
     * @return \Chamilo\Libraries\Format\Structure\Toolbar
     */
    public function getToolbar($workspace)
    {
        $toolbar = new Toolbar();

        $favouriteService = new FavouriteService(new FavouriteRepository());
        $favourite = $favouriteService->getWorkspaceUserFavouriteByUserAndWorkspaceIdentifier(
            $this->get_component()->get_user(), $workspace->getId()
        );

        if ($favourite instanceof WorkspaceUserFavourite)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('RemoveFavourite', null, Manager::context()),
                    new FontAwesomeGlyph('times', [], null, 'fas'), $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_FAVOURITE,
                        \Chamilo\Core\Repository\Workspace\Favourite\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Favourite\Manager::ACTION_DELETE,
                        Manager::PARAM_WORKSPACE_ID => $workspace->get_id(),
                        Manager::PARAM_BROWSER_SOURCE => $this->get_component()->get_action()
                    )
                ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Favourite', null, Manager::context()),
                    new FontAwesomeGlyph('star', [], null, 'fas'), $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_FAVOURITE,
                        \Chamilo\Core\Repository\Workspace\Favourite\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Favourite\Manager::ACTION_CREATE,
                        Manager::PARAM_WORKSPACE_ID => $workspace->get_id(),
                        Manager::PARAM_BROWSER_SOURCE => $this->get_component()->get_action()
                    )
                ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if (RightsService::getInstance()->canManageWorkspace($this->get_component()->get_user(), $workspace))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Edit', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_UPDATE,
                            Manager::PARAM_WORKSPACE_ID => $workspace->get_id(),
                            Manager::PARAM_BROWSER_SOURCE => $this->get_component()->get_action()
                        )
                    ), ToolbarItem::DISPLAY_ICON
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('CreateRightsComponent'), new FontAwesomeGlyph('user'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_RIGHTS,
                            Manager::PARAM_WORKSPACE_ID => $workspace->get_id(),
                            Manager::PARAM_BROWSER_SOURCE => $this->get_component()->get_action(),
                            \Chamilo\Core\Repository\Workspace\Rights\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Rights\Manager::ACTION_CREATE
                        )
                    ), ToolbarItem::DISPLAY_ICON
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('RightsComponent'), new FontAwesomeGlyph('lock'), $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_RIGHTS,
                        Manager::PARAM_WORKSPACE_ID => $workspace->get_id(),
                        Manager::PARAM_BROWSER_SOURCE => $this->get_component()->get_action(),
                        \Chamilo\Core\Repository\Workspace\Rights\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Rights\Manager::ACTION_BROWSE
                    )
                ), ToolbarItem::DISPLAY_ICON
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Delete', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                            Manager::PARAM_WORKSPACE_ID => $workspace->get_id(),
                            Manager::PARAM_BROWSER_SOURCE => $this->get_component()->get_action()
                        )
                    ), ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        return $toolbar;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     *
     * @return string
     */
    private function getWorkspaceUrl($workspace)
    {
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Manager::package(),
                \Chamilo\Core\Repository\Manager::PARAM_WORKSPACE_ID => $workspace->getId()
            )
        );

        return $redirect->getUrl();
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport::get_actions()
     */
    public function get_actions($workspace)
    {
        return $this->getToolbar($workspace)->as_html();
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer::renderCell()
     */
    public function renderCell(TableColumn $column, $workspace): string
    {
        switch ($column->get_name())
        {
            case Workspace::PROPERTY_CREATOR_ID:
                if ($workspace->getCreator() instanceof User)
                {
                    return $workspace->getCreator()->get_fullname();
                }

                return Translation::getInstance()->getTranslation('Unknown', null, StringUtilities::LIBRARIES);
            case Workspace::PROPERTY_CREATION_DATE:
                return DatetimeUtilities::getInstance()->formatLocaleDate(
                    Translation::get('DateTimeFormatLong', null, StringUtilities::LIBRARIES),
                    $workspace->getCreationDate()
                );
            case Workspace::PROPERTY_NAME:
                return '<a href="' . $this->getWorkspaceUrl($workspace) . '">' .
                    parent::renderCell($column, $workspace) . '</a>';
        }

        return parent::renderCell($column, $workspace);
    }
}
