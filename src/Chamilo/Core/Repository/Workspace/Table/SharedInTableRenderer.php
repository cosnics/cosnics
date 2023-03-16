<?php
namespace Chamilo\Core\Repository\Workspace\Table;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Workspace\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SharedInTableRenderer extends RecordListTableRenderer implements TableRowActionsSupport
{
    protected DatetimeUtilities $datetimeUtilities;

    protected UserService $userService;

    public function __construct(
        UserService $userService, DatetimeUtilities $datetimeUtilities, Translator $translator,
        UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->datetimeUtilities = $datetimeUtilities;
        $this->userService = $userService;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    protected function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
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
     * @param string[] $workspaceRelationRecord
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $workspaceRelationRecord
    ): string
    {
        $translator = $this->getTranslator();
        $datetimeUtilities = $this->getDatetimeUtilities();

        switch ($column->get_name())
        {
            case Workspace::PROPERTY_CREATOR_ID :
                return $this->getUserService()->getUserFullNameByIdentifier(
                    $workspaceRelationRecord[Workspace::PROPERTY_CREATOR_ID]
                );
            case Workspace::PROPERTY_CREATION_DATE :
                return $datetimeUtilities->formatLocaleDate(
                    $translator->trans('DateTimeFormatLong', [], StringUtilities::LIBRARIES),
                    (int) $workspaceRelationRecord[Workspace::PROPERTY_CREATION_DATE]
                );
        }

        return parent::renderCell($column, $resultPosition, $workspaceRelationRecord);
    }

    /**
     * @param string[] $workspaceRelationRecord
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $workspaceRelationRecord): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $unshareUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Manager::PARAM_ACTION => Manager::ACTION_UNSHARE,
            Manager::PARAM_SELECTED_WORKSPACE_ID => $workspaceRelationRecord[DataClass::PROPERTY_ID],
            \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID => $workspaceRelationRecord[WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID]
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Unshare', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('unlock'),
                $unshareUrl, ToolbarItem::DISPLAY_ICON, true
            )
        );

        return $toolbar->render();
    }
}
