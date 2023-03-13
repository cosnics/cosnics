<?php
namespace Chamilo\Core\Repository\Workspace\Table;

use Chamilo\Core\Repository\Workspace\Favourite\Service\FavouriteService;
use Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite;
use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceUserDefault;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Workspace\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WorkspaceTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_WORKSPACE_ID;

    protected FavouriteService $favouriteService;

    protected RightsService $rightsService;

    protected User $user;

    protected WorkspaceService $workspaceService;

    public function __construct(
        WorkspaceService $workspaceService, FavouriteService $favouriteService, RightsService $rightsService,
        User $user, Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer,
        Pager $pager
    )
    {
        $this->workspaceService = $workspaceService;
        $this->favouriteService = $favouriteService;
        $this->rightsService = $rightsService;
        $this->user = $user;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getFavouriteService(): FavouriteService
    {
        return $this->favouriteService;
    }

    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $favouriteUrl = $urlGenerator->fromParameters(
            [
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Workspace\Favourite\Manager::CONTEXT,
                \Chamilo\Core\Repository\Workspace\Favourite\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Favourite\Manager::ACTION_CREATE
            ]
        );

        $actions->addAction(
            new TableAction(
                $favouriteUrl, $translator->trans('FavouriteSelected', [], Manager::CONTEXT), false
            )
        );

        $deleteUrl = $urlGenerator->fromParameters(
            [
                Manager::PARAM_ACTION => Manager::ACTION_DELETE
            ]
        );

        $actions->addAction(
            new TableAction(
                $deleteUrl, $translator->trans('DeleteSelected', [], StringUtilities::LIBRARIES), true
            )
        );

        return $actions;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getWorkspaceService(): WorkspaceService
    {
        return $this->workspaceService;
    }

    protected function getWorkspaceUserDefault(): ?WorkspaceUserDefault
    {
        return $this->getWorkspaceService()->findWorkspaceUserDefaultForUserIdentifier($this->getUser()->getId());
    }

    protected function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(Workspace::class, Workspace::PROPERTY_NAME));
        $this->addColumn(
            new DataClassPropertyTableColumn(Workspace::class, Workspace::PROPERTY_CREATOR_ID, null, false)
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(Workspace::class, Workspace::PROPERTY_CREATION_DATE)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $workspace): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        switch ($column->get_name())
        {
            case Workspace::PROPERTY_CREATOR_ID:
                if ($workspace->getCreator() instanceof User)
                {
                    return $workspace->getCreator()->get_fullname();
                }

                return $translator->trans('Unknown', [], StringUtilities::LIBRARIES);
            case Workspace::PROPERTY_CREATION_DATE:
                return DatetimeUtilities::getInstance()->formatLocaleDate(
                    $translator->trans('DateTimeFormatLong', [], StringUtilities::LIBRARIES),
                    $workspace->getCreationDate()
                );
            case Workspace::PROPERTY_NAME:
                $workspaceUrl = $urlGenerator->fromParameters([
                    Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Manager::CONTEXT,
                    \Chamilo\Core\Repository\Manager::PARAM_WORKSPACE_ID => $workspace->getId()
                ]);

                $workspaceUserDefault = $this->getWorkspaceUserDefault();

                if ($workspaceUserDefault instanceof WorkspaceUserDefault &&
                    $workspaceUserDefault->getWorkspaceIdentifier() == $workspace->getId())
                {
                    $glyph = new FontAwesomeGlyph('house', ['fa-lg']);
                    $renderedGlyph = $glyph->render() . ' ';
                }
                else
                {
                    $renderedGlyph = '';
                }

                return '<a href="' . $workspaceUrl . '">' . $renderedGlyph .
                    parent::renderCell($column, $resultPosition, $workspace) . '</a>';
        }

        return parent::renderCell($column, $resultPosition, $workspace);
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $workspace): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $existingDefaultWorkspace = $this->getWorkspaceUserDefault();

        if ($this->getRightsService()->isWorkspaceCreator($this->getUser(), $workspace) &&
            (!$existingDefaultWorkspace instanceof WorkspaceUserDefault ||
                $existingDefaultWorkspace->getWorkspaceIdentifier() != $workspace->getId()))
        {
            $defaultUrl = $urlGenerator->fromParameters([
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Manager::PARAM_ACTION => Manager::ACTION_DEFAULT,
                Manager::PARAM_WORKSPACE_ID => $workspace->getId()
            ]);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('DefaultWorkspace', [], Manager::CONTEXT), new FontAwesomeGlyph('house'),
                    $defaultUrl, ToolbarItem::DISPLAY_ICON, true
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('DefaultWorkspace', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('house', ['disabled', 'text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $favouriteService = $this->getFavouriteService();
        $favourite = $favouriteService->getWorkspaceUserFavouriteByUserAndWorkspaceIdentifier(
            $this->getUser(), $workspace->getId()
        );

        if ($this->getRightsService()->canViewContentObjects($this->getUser(), $workspace))
        {
            if ($favourite instanceof WorkspaceUserFavourite)
            {
                $deleteUrl = $urlGenerator->fromParameters([
                    Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Workspace\Favourite\Manager::CONTEXT,
                    \Chamilo\Core\Repository\Workspace\Favourite\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Favourite\Manager::ACTION_DELETE,
                    Manager::PARAM_WORKSPACE_ID => $workspace->getId()
                ]);

                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('RemoveFavourite', [], Manager::CONTEXT),
                        new FontAwesomeGlyph('heart-circle-xmark', [], null, 'fas'), $deleteUrl,
                        ToolbarItem::DISPLAY_ICON
                    )
                );
            }
            else
            {
                $favouriteUrl = $urlGenerator->fromParameters([
                    Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Workspace\Favourite\Manager::CONTEXT,
                    \Chamilo\Core\Repository\Workspace\Favourite\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Favourite\Manager::ACTION_CREATE,
                    Manager::PARAM_WORKSPACE_ID => $workspace->getId()
                ]);

                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('Favourite', [], Manager::CONTEXT),
                        new FontAwesomeGlyph('heart', [], null, 'fas'), $favouriteUrl, ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }

        if ($this->getRightsService()->canManageWorkspace($this->getUser(), $workspace))
        {
            $updateUrl = $urlGenerator->fromParameters([
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Manager::PARAM_ACTION => Manager::ACTION_UPDATE,
                Manager::PARAM_WORKSPACE_ID => $workspace->getId()
            ]);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $updateUrl, ToolbarItem::DISPLAY_ICON
                )
            );

            $createRightsUrl = $urlGenerator->fromParameters([
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Workspace\Rights\Manager::CONTEXT,
                \Chamilo\Core\Repository\Workspace\Rights\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Rights\Manager::ACTION_CREATE,
                Manager::PARAM_WORKSPACE_ID => $workspace->getId()

            ]);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('CreateRightsComponent', [], Manager::CONTEXT), new FontAwesomeGlyph('user'),
                    $createRightsUrl, ToolbarItem::DISPLAY_ICON
                )
            );

            $browseRightsUrl = $urlGenerator->fromParameters([
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Workspace\Rights\Manager::CONTEXT,
                \Chamilo\Core\Repository\Workspace\Rights\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Rights\Manager::ACTION_BROWSE,
                Manager::PARAM_WORKSPACE_ID => $workspace->getId()
            ]);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('RightsComponent', [], Manager::CONTEXT), new FontAwesomeGlyph('lock'),
                    $browseRightsUrl, ToolbarItem::DISPLAY_ICON
                )
            );

            $deleteUrl = $urlGenerator->fromParameters([
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                Manager::PARAM_WORKSPACE_ID => $workspace->getId()
            ]);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $deleteUrl, ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        return $toolbar->render();
    }
}
