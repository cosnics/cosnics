<?php
namespace Chamilo\Core\Repository\Workspace\Table;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
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
class ShareTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    protected DatetimeUtilities $datetimeUtilities;

    protected UserService $userService;

    public function __construct(
        UserService $userService, DatetimeUtilities $datetimeUtilities, Translator $translator,
        UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->datetimeUtilities = $datetimeUtilities;
        $this->userService = $userService;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    protected function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, Manager::PARAM_SELECTED_WORKSPACE_ID);

        $shareUrl = $urlGenerator->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Manager::PARAM_ACTION => Manager::ACTION_SHARE
            ]
        );

        $actions->addAction(
            new TableAction(
                $shareUrl, $translator->trans('ShareSelected', [], StringUtilities::LIBRARIES), false
            )
        );

        return $actions;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    protected function initializeColumns()
    {
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Workspace::class, Workspace::PROPERTY_NAME)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                Workspace::class, Workspace::PROPERTY_CREATOR_ID, null, false
            )
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                Workspace::class, Workspace::PROPERTY_CREATION_DATE
            )
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
        $datetimeUtilities = $this->getDatetimeUtilities();

        switch ($column->get_name())
        {
            case Workspace::PROPERTY_CREATOR_ID :
                return $this->getUserService()->getUserFullNameByIdentifier($workspace->getCreatorId());
            case Workspace::PROPERTY_CREATION_DATE :
                return $datetimeUtilities->formatLocaleDate(
                    $translator->trans('DateTimeFormatLong', [], StringUtilities::LIBRARIES),
                    $workspace->getCreationDate()
                );
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

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Share', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('lock'),
                $urlGenerator->fromParameters([
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Manager::PARAM_ACTION => Manager::ACTION_SHARE,
                    Manager::PARAM_SELECTED_WORKSPACE_ID => $workspace->getId()
                ]), ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->render();
    }
}
