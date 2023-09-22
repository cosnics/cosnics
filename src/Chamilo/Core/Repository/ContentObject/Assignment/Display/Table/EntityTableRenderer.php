<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Table;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\RightsService;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableParameterValues;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class EntityTableRenderer extends RecordListTableRenderer
    implements TableRowActionsSupport, TableActionsSupport
{
    public const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_DESC;
    public const DEFAULT_ORDER_COLUMN_INDEX = 4;

    public const PROPERTY_ENTRY_COUNT = 'entry_count';
    public const PROPERTY_FEEDBACK_COUNT = 'feedback_count';
    public const PROPERTY_FIRST_ENTRY_DATE = 'first_entry_date';
    public const PROPERTY_LAST_ENTRY_DATE = 'last_entry_date';
    public const PROPERTY_LAST_SCORE = 'last_score';
    public const PROPERTY_NAME = 'name';

    public const TABLE_IDENTIFIER = Entry::PROPERTY_ENTITY_ID;

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     * @var ?\Chamilo\Libraries\Architecture\Application\Application
     */
    protected ?Application $application = null;

    protected AssignmentDataProvider $assignmentDataProvider;

    protected DatetimeUtilities $datetimeUtilities;

    protected RightsService $rightsService;

    protected User $user;

    public function __construct(
        AssignmentDataProvider $assignmentDataProvider, DatetimeUtilities $datetimeUtilities,
        RightsService $rightsService, User $user, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->assignmentDataProvider = $assignmentDataProvider;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->rightsService = $rightsService;
        $this->user = $user;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    protected function canViewEntity($entity): bool
    {
        /** @var Assignment $assignment */
        $hasEntries = $entity[self::PROPERTY_ENTRY_COUNT] > 0;

        return $this->getRightsService()->canUserViewEntity(
                $this->getUser(), $this->application->getAssignment(), $entity[Entry::PROPERTY_ENTITY_TYPE],
                $entity[Entry::PROPERTY_ENTITY_ID]
            ) && $hasEntries;
    }

    protected function formatDate($date): string
    {
        $formatted_date = $this->getDatetimeUtilities()->formatLocaleDate(
            $this->getTranslator()->trans('DateTimeFormatLong', [], StringUtilities::LIBRARIES), $date
        );

        if ($this->getAssignmentDataProvider()->isDateAfterAssignmentEndTime($date))
        {
            return '<span style="color:red">' . $formatted_date . '</span>';
        }

        return $formatted_date;
    }

    public function getAssignmentDataProvider(): AssignmentDataProvider
    {
        return $this->assignmentDataProvider;
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    protected function getEntityUrl($entity): string
    {
        return $this->getUrlGenerator()->fromRequest(
            [
                Manager::PARAM_ACTION => Manager::ACTION_ENTRY,
                Manager::PARAM_ENTITY_TYPE => $entity[Entry::PROPERTY_ENTITY_TYPE],
                Manager::PARAM_ENTITY_ID => $entity[Entry::PROPERTY_ENTITY_ID]
            ]
        );
    }

    protected function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromRequest(
                    [
                        Manager::PARAM_ACTION => Manager::ACTION_DOWNLOAD,
                        Manager::PARAM_ENTITY_TYPE => $this->getAssignmentDataProvider()->getCurrentEntityType()
                    ]
                ), $translator->trans('DownloadSelected', [], Manager::CONTEXT), false
            )
        );

        return $actions;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    protected function initializeColumns(): void
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            new SortableStaticTableColumn(
                self::PROPERTY_FIRST_ENTRY_DATE, $translator->trans('FirstEntryDate', [], Manager::CONTEXT)
            )
        );
        $this->addColumn(
            new SortableStaticTableColumn(
                self::PROPERTY_LAST_ENTRY_DATE, $translator->trans('LastEntryDate', [], Manager::CONTEXT)
            )
        );
        $this->addColumn(
            new SortableStaticTableColumn(
                self::PROPERTY_ENTRY_COUNT, $translator->trans('EntryCount', [], Manager::CONTEXT)
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_FEEDBACK_COUNT, $translator->trans('FeedbackCount', [], Manager::CONTEXT)
            )
        );
        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_LAST_SCORE, $translator->trans('LastScore', [], Manager::CONTEXT))
        );
    }

    abstract protected function isEntity($entityId, $userId): bool;

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    public function legacyRender(
        Application $application, TableParameterValues $parameterValues, ArrayCollection $tableData,
        ?string $tableName = null
    ): string
    {
        $this->application = $application;

        return parent::render($parameterValues, $tableData, $tableName); // TODO: Change the autogenerated stub
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $entity): string
    {
        switch ($column->get_name())
        {
            case self::PROPERTY_FIRST_ENTRY_DATE :
                if (is_null($entity[self::PROPERTY_FIRST_ENTRY_DATE]))
                {
                    return '-';
                }

                return $this->formatDate($entity[self::PROPERTY_FIRST_ENTRY_DATE]);
            case self::PROPERTY_LAST_ENTRY_DATE :
                if (is_null($entity[self::PROPERTY_LAST_ENTRY_DATE]))
                {
                    return '-';
                }

                return $this->formatDate($entity[self::PROPERTY_LAST_ENTRY_DATE]);
            case self::PROPERTY_FEEDBACK_COUNT :
                return $this->getAssignmentDataProvider()->countFeedbackByEntityTypeAndEntityId(
                    $this->getAssignmentDataProvider()->getCurrentEntityType(), $entity[Entry::PROPERTY_ENTITY_ID]
                );
            case self::PROPERTY_LAST_SCORE:
                $lastScore = $this->getAssignmentDataProvider()->getLastScoreForEntityTypeAndId(
                    $entity[Entry::PROPERTY_ENTITY_TYPE], $entity[Entry::PROPERTY_ENTITY_ID]
                );

                if (is_null($lastScore))
                {
                    return '';
                }

                return '<div class="text-right">' . $lastScore . '%</div>';
        }

        return parent::renderCell($column, $resultPosition, $entity);
    }

    public function renderTableRowActions(TableResultPosition $resultPosition, $entity): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $toolbar = new Toolbar();

        $entityId = $entity[Entry::PROPERTY_ENTITY_ID];
        $isEntity = $this->isEntity($entityId, $this->getUser()->getId());

        if ($this->canViewEntity($entity))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('ViewLastEntry', [], Manager::CONTEXT), new FontAwesomeGlyph('folder'),
                    $urlGenerator->fromRequest(
                        [
                            Manager::PARAM_ACTION => Manager::ACTION_ENTRY,
                            Manager::PARAM_ENTITY_TYPE => $entity[Entry::PROPERTY_ENTITY_TYPE],
                            Manager::PARAM_ENTITY_ID => $entity[Entry::PROPERTY_ENTITY_ID]
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $hasEntries = $entity[self::PROPERTY_ENTRY_COUNT] > 0;

        if ($this->getRightsService()->canUserDownloadEntriesFromEntity(
                $this->getUser(), $this->application->getAssignment(), $entity[Entry::PROPERTY_ENTITY_TYPE],
                $entity[Entry::PROPERTY_ENTITY_ID]
            ) && $hasEntries)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('DownloadAll', [], Manager::CONTEXT), new FontAwesomeGlyph('download'),
                    $urlGenerator->fromRequest(
                        [
                            Manager::PARAM_ACTION => Manager::ACTION_DOWNLOAD,
                            Manager::PARAM_ENTITY_TYPE => $entity[Entry::PROPERTY_ENTITY_TYPE],
                            Manager::PARAM_ENTITY_ID => $entity[Entry::PROPERTY_ENTITY_ID]
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($isEntity)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('AddNewEntry', [], Manager::CONTEXT), new FontAwesomeGlyph('plus'),
                    $urlGenerator->fromRequest(
                        [
                            Manager::PARAM_ACTION => Manager::ACTION_CREATE,
                            Manager::PARAM_ENTITY_TYPE => $entity[Entry::PROPERTY_ENTITY_TYPE],
                            Manager::PARAM_ENTITY_ID => $entity[Entry::PROPERTY_ENTITY_ID]
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
