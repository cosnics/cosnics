<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Table;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
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
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class EntryTableRenderer extends RecordListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_DESC;
    public const DEFAULT_ORDER_COLUMN_INDEX = 2;

    public const PROPERTY_FEEDBACK_COUNT = 'feedback_count';

    public const TABLE_IDENTIFIER = DataClass::PROPERTY_ID;

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     * @var ?\Chamilo\Libraries\Architecture\Application\Application
     */
    protected ?Application $application = null;

    protected AssignmentDataProvider $assignmentDataProvider;

    protected DatetimeUtilities $datetimeUtilities;

    protected StringUtilities $stringUtilities;

    protected User $user;

    public function __construct(
        AssignmentDataProvider $assignmentDataProvider, DatetimeUtilities $datetimeUtilities,
        StringUtilities $stringUtilities, User $user, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->assignmentDataProvider = $assignmentDataProvider;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->stringUtilities = $stringUtilities;
        $this->user = $user;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
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

    abstract public function getEntryClassName(): string;

    abstract public function getScoreClassName(): string;

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
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
                        Manager::PARAM_ACTION => Manager::ACTION_DOWNLOAD
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
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObject::class, ContentObject::PROPERTY_TITLE
            )
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObject::class, ContentObject::PROPERTY_DESCRIPTION
            )
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(
                $this->getEntryClassName(), Entry::PROPERTY_SUBMITTED,
                $translator->trans('Submitted', [], Manager::CONTEXT)
            )
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(
                $this->getScoreClassName(), Score::PROPERTY_SCORE, $translator->trans('Score', [], Manager::CONTEXT)
            )
        );

        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_FEEDBACK_COUNT, $translator->trans('FeedbackCount', [], Manager::CONTEXT)
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
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

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $entry): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $stringUtilities = $this->getStringUtilities();

        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TITLE :
                $title = strip_tags($entry[ContentObject::PROPERTY_TITLE]);
                $title = $stringUtilities->createString($title)->safeTruncate(50, ' &hellip;')->__toString();

                $isUser = $entry[Entry::PROPERTY_USER_ID] == $this->getUser()->getId();
                $assignment = $this->application->get_root_content_object();

                if ($isUser || $assignment->get_visibility_submissions() == 1 ||
                    $this->getAssignmentDataProvider()->canEditAssignment())
                {
                    $url = $urlGenerator->fromRequest(
                        [
                            Manager::PARAM_ACTION => Manager::ACTION_ENTRY,
                            Manager::PARAM_ENTRY_ID => $entry[DataClass::PROPERTY_ID]
                        ]
                    );

                    return '<a href="' . $url . '">' . $title . '</a>';
                }
                else
                {
                    return $title;
                }
            case ContentObject::PROPERTY_DESCRIPTION :
                $description = strip_tags($entry[ContentObject::PROPERTY_DESCRIPTION]);

                return $stringUtilities->createString($description)->safeTruncate(100, ' &hellip;')->__toString();
            case Entry::PROPERTY_SUBMITTED :
                if (is_null($entry[Entry::PROPERTY_SUBMITTED]))
                {
                    return '-';
                }

                return $this->formatDate($entry[Entry::PROPERTY_SUBMITTED]);
            case Score::PROPERTY_SCORE:
                $score = $entry[Score::PROPERTY_SCORE];
                if (is_null($score))
                {
                    return '';
                }

                return $score . '%';
            case self::PROPERTY_FEEDBACK_COUNT :
                return (string) $this->getAssignmentDataProvider()->countFeedbackByEntryIdentifier(
                    $entry[DataClass::PROPERTY_ID]
                );
        }

        return parent::renderCell($column, $resultPosition, $entry);
    }

    public function renderTableRowActions(TableResultPosition $resultPosition, $entry): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $toolbar = new Toolbar();

        $isCurrentEntry = $this->application->getEntry()->getId() == $entry[DataClass::PROPERTY_ID];
        $isUser = $entry[Entry::PROPERTY_USER_ID] == $this->getUser()->getId();
        $assignment = $this->application->get_root_content_object();

        if (!$isCurrentEntry && ($isUser || $assignment->get_visibility_submissions() == 1 ||
                $this->getAssignmentDataProvider()->canEditAssignment()))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('ViewEntry', [], Manager::CONTEXT), new FontAwesomeGlyph('folder'),
                    $urlGenerator->fromRequest(
                        [
                            Manager::PARAM_ACTION => Manager::ACTION_ENTRY,
                            Manager::PARAM_ENTRY_ID => $entry[DataClass::PROPERTY_ID],
                            Manager::PARAM_ENTITY_ID => $this->application->getEntityIdentifier(),
                            Manager::PARAM_ENTITY_TYPE => $this->application->getEntityType()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $usesFileStorage = is_subclass_of(
            $entry[ContentObject::PROPERTY_TYPE], '\Chamilo\Libraries\Architecture\Interfaces\FileStorageSupport'
        );

        if ($usesFileStorage)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Download', [], Manager::CONTEXT), new FontAwesomeGlyph('download'),
                    $urlGenerator->fromRequest(
                        [
                            Manager::PARAM_ACTION => Manager::ACTION_DOWNLOAD,
                            Manager::PARAM_ENTRY_ID => $entry[DataClass::PROPERTY_ID]
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('DownloadNotPossible', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('download', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
