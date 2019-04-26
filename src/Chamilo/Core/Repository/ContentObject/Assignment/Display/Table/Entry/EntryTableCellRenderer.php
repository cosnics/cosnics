<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTableCellRenderer extends RecordTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $contentObject)
    {
        if ($column instanceof ActionsTableColumn && $this instanceof TableCellRendererActionsColumnSupport)
        {
            return $this->get_actions($contentObject);
        }

        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TITLE :
                $title = strip_tags($contentObject[ContentObject::PROPERTY_TITLE]);
                $title =
                    StringUtilities::getInstance()->createString($title)->safeTruncate(50, ' &hellip;')->__toString();

                $isUser = $contentObject[Entry::PROPERTY_USER_ID] == $this->getEntryTableParameters()->getUser()->getId();
                $assignment = $this->getEntryTableParameters()->getAssignment();

                if ($isUser || $assignment->get_visibility_submissions() == 1 ||
                    $this->getEntryTableParameters()->getAssignmentServiceBridge()->canEditAssignment())
                {
                    $url = $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_ENTRY,
                            Manager::PARAM_ENTRY_ID => $contentObject[Entry::PROPERTY_ID]
                        )
                    );

                    return '<a href="' . $url . '">' . $title . '</a>';
                }
                else
                {
                    return $title;
                }
                break;
            case ContentObject::PROPERTY_DESCRIPTION :
                $description = strip_tags($contentObject[ContentObject::PROPERTY_DESCRIPTION]);

                return StringUtilities::getInstance()->createString($description)->safeTruncate(100, ' &hellip;')
                    ->__toString();
                break;
            case Entry::PROPERTY_SUBMITTED :
                if (is_null($contentObject[Entry::PROPERTY_SUBMITTED]))
                {
                    return '-';
                }

                return $this->formatDate($contentObject[Entry::PROPERTY_SUBMITTED]);
                break;
            case Score::PROPERTY_SCORE:
                $score = $contentObject[Score::PROPERTY_SCORE];
                if(is_null($score))
                {
                    return null;
                }

                return $score . '%';
            case EntryTableColumnModel::PROPERTY_FEEDBACK_COUNT :
                return $this->getEntryTableParameters()->getFeedbackServiceBridge()->countFeedbackByEntryIdentifier(
                    $contentObject[Entry::PROPERTY_ID]
                );
                break;
        }

        return parent::render_cell($column, $contentObject);
    }

    public function render_id_cell($row)
    {
        return $row[Entry::PROPERTY_ID];
    }

    /**
     * @see \Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport::get_actions()
     *
     * @param Entry $entry
     *
     * @return string
     */
    public function get_actions($entry)
    {
        $toolbar = new Toolbar();

        $isCurrentEntry = $this->getEntryTableParameters()->getCurrentEntry()->getId() == $entry[Entry::PROPERTY_ID];
        $isUser = $entry[Entry::PROPERTY_USER_ID] == $this->getEntryTableParameters()->getUser()->getId();
        $assignment = $this->getEntryTableParameters()->getAssignment();

        if (!$isCurrentEntry && ($isUser || $assignment->get_visibility_submissions() == 1 ||
            $this->getEntryTableParameters()->getAssignmentServiceBridge()->canEditAssignment()))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ViewEntry'),
                    Theme::getInstance()->getCommonImagePath('Action/Browser'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_ENTRY,
                            Manager::PARAM_ENTRY_ID => $entry[Entry::PROPERTY_ID],
                            Manager::PARAM_ENTITY_ID => $this->getEntryTableParameters()->getEntityId(),
                            Manager::PARAM_ENTITY_TYPE => $this->getEntryTableParameters()->getEntityType()
                        )
                    ),
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $usesFileStorage = is_subclass_of(
            $entry[ContentObject::PROPERTY_TYPE],
            '\Chamilo\Libraries\Architecture\Interfaces\FileStorageSupport'
        );

        if ($usesFileStorage)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Download'),
                    Theme::getInstance()->getCommonImagePath('Action/Download'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_DOWNLOAD,
                            Manager::PARAM_ENTRY_ID => $entry[Entry::PROPERTY_ID]
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
                    Translation::get('DownloadNotPossible'),
                    Theme::getInstance()->getCommonImagePath('Action/DownloadNa'),
                    null,
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }


        return $toolbar->as_html();
    }

    /**
     * Formats a date.
     *
     * @param int $date the date to be formatted.
     *
     * @return string
     */
    protected function formatDate($date)
    {
        $formatted_date = DatetimeUtilities::format_locale_date(
            Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
            $date
        );

        if ($this->getEntryTableParameters()->getAssignmentServiceBridge()->isDateAfterAssignmentEndTime($date))
        {
            return '<span style="color:red">' . $formatted_date . '</span>';
        }

        return $formatted_date;
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableParameters
     */
    protected function getEntryTableParameters()
    {
        return $this->getTable()->getEntryTableParameters();
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\Table | \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTable
     */
    protected function getTable()
    {
        return $this->get_table();
    }
}