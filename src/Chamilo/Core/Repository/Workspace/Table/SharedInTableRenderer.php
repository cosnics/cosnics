<?php
namespace Chamilo\Core\Repository\Workspace\Table;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
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
class SharedInTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport
{
    protected DatetimeUtilities $datetimeUtilities;

    public function __construct(
        DatetimeUtilities $datetimeUtilities, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->datetimeUtilities = $datetimeUtilities;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    protected function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
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
        $datetimeUtilities = $this->getDatetimeUtilities();

        switch ($column->get_name())
        {
            case Workspace::PROPERTY_CREATOR_ID :
                return $workspace->getCreator()->get_fullname();
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

        $unshareUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Manager::PARAM_ACTION => Manager::ACTION_UNSHARE,
            Manager::PARAM_SELECTED_WORKSPACE_ID => $workspace->getId(),
            \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID => $contentObjectId
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
