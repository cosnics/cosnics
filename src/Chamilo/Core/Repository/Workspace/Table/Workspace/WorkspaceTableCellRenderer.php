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
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

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
     * @see \Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer::render_cell()
     */
    public function render_cell($column, $workspace)
    {
        switch ($column->get_name())
        {
            case Workspace::PROPERTY_CREATOR_ID:
                if ($workspace->getCreator() instanceof User)
                {
                    return $workspace->getCreator()->get_fullname();
                }

                return Translation::getInstance()->getTranslation('Unknown', null, Utilities::COMMON_LIBRARIES);
            case Workspace::PROPERTY_CREATION_DATE:
                return DatetimeUtilities::format_locale_date(
                    Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
                    $workspace->getCreationDate()
                );
            case Workspace::PROPERTY_NAME:
                return '<a href="' . $this->getWorkspaceUrl($workspace) . '">' .
                parent::render_cell($column, $workspace) . '</a>';
        }

        return parent::render_cell($column, $workspace);
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
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     *
     * @return \Chamilo\Libraries\Format\Structure\Toolbar
     */
    public function getToolbar($workspace)
    {
        $toolbar = new Toolbar();

        $favouriteService = new FavouriteService(new FavouriteRepository());
        $favourite = $favouriteService->getWorkspaceUserFavouriteByUserAndWorkspaceIdentifier(
            $this->get_component()->get_user(),
            $workspace->getId()
        );

        if ($favourite instanceof WorkspaceUserFavourite)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('RemoveFavourite', null, Manager::context()),
                    Theme::getInstance()->getImagePath(
                        \Chamilo\Core\Repository\Workspace\Favourite\Manager::context(),
                        'Action/Delete'
                    ),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_FAVOURITE,
                            \Chamilo\Core\Repository\Workspace\Favourite\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Favourite\Manager::ACTION_DELETE,
                            Manager::PARAM_WORKSPACE_ID => $workspace->get_id(),
                            Manager::PARAM_BROWSER_SOURCE => $this->get_component()->get_action()
                        )
                    ),
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Favourite', null, Manager::context()),
                    Theme::getInstance()->getImagePath(Manager::context(), 'Action/Favourite'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_FAVOURITE,
                            \Chamilo\Core\Repository\Workspace\Favourite\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Favourite\Manager::ACTION_CREATE,
                            Manager::PARAM_WORKSPACE_ID => $workspace->get_id(),
                            Manager::PARAM_BROWSER_SOURCE => $this->get_component()->get_action()
                        )
                    ),
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if (RightsService::getInstance()->canManageWorkspace($this->get_component()->get_user(), $workspace))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Edit', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Edit'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_UPDATE,
                            Manager::PARAM_WORKSPACE_ID => $workspace->get_id(),
                            Manager::PARAM_BROWSER_SOURCE => $this->get_component()->get_action()
                        )
                    ),
                    ToolbarItem::DISPLAY_ICON
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('CreateRightsComponent'),
                    Theme::getInstance()->getCommonImagePath('Action/Share'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_RIGHTS,
                            Manager::PARAM_WORKSPACE_ID => $workspace->get_id(),
                            Manager::PARAM_BROWSER_SOURCE => $this->get_component()->get_action(),
                            \Chamilo\Core\Repository\Workspace\Rights\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Rights\Manager::ACTION_CREATE
                        )
                    ),
                    ToolbarItem::DISPLAY_ICON
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('RightsComponent'),
                    Theme::getInstance()->getCommonImagePath('Action/Rights'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_RIGHTS,
                            Manager::PARAM_WORKSPACE_ID => $workspace->get_id(),
                            Manager::PARAM_BROWSER_SOURCE => $this->get_component()->get_action(),
                            \Chamilo\Core\Repository\Workspace\Rights\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Rights\Manager::ACTION_BROWSE
                        )
                    ),
                    ToolbarItem::DISPLAY_ICON
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Delete', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Delete'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                            Manager::PARAM_WORKSPACE_ID => $workspace->get_id(),
                            Manager::PARAM_BROWSER_SOURCE => $this->get_component()->get_action()
                        )
                    ),
                    ToolbarItem::DISPLAY_ICON,
                    true
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
}
