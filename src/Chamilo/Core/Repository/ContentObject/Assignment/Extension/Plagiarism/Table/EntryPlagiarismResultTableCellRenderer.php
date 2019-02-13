<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table;

use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\ExtensionComponent;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryPlagiarismResultTableCellRenderer extends RecordTableCellRenderer
    implements TableCellRendererActionsColumnSupport
{
    public function render_cell($column, $entry)
    {
        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TITLE :
                $title = strip_tags($entry[ContentObject::PROPERTY_TITLE]);
                $title =
                    StringUtilities::getInstance()->createString($title)->safeTruncate(50, ' &hellip;')->__toString();

                $entryUrl = $this->get_component()->get_url(
                    [
                        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION =>
                        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_ENTRY,
                        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTRY_ID =>
                            $entry[Entry::PROPERTY_ID]
                    ],
                    [Manager::PARAM_ACTION, ExtensionComponent::PARAM_EXTENSION]
                );

                return '<a href="' . $entryUrl . '">' . $title . '</a>';
            case ContentObject::PROPERTY_DESCRIPTION :
                $description = strip_tags($entry[ContentObject::PROPERTY_DESCRIPTION]);

                return StringUtilities::getInstance()->createString($description)->safeTruncate(100, ' &hellip;')
                    ->__toString();
            case Entry::PROPERTY_SUBMITTED :
                if (is_null($entry[Entry::PROPERTY_SUBMITTED]))
                {
                    return '-';
                }

                return DatetimeUtilities::format_locale_date(
                    Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
                    $entry[Entry::PROPERTY_SUBMITTED]
                );
                break;
            case Score::PROPERTY_SCORE:
                $score = $entry[Score::PROPERTY_SCORE];
                if (is_null($score))
                {
                    return null;
                }

                return $score . '%';
            case EntryPlagiarismResult::PROPERTY_RESULT:
                $submissionStatus = new SubmissionStatus(
                    $entry[EntryPlagiarismResult::PROPERTY_EXTERNAL_ID], $entry[EntryPlagiarismResult::PROPERTY_STATUS],
                    $entry[EntryPlagiarismResult::PROPERTY_RESULT], $entry[EntryPlagiarismResult::PROPERTY_ERROR]
                );

                if(!$submissionStatus->isReportGenerated())
                {
                    return null;
                }

                if (empty($submissionStatus->getResult()))
                {
                    return Translation::getInstance()->getTranslation('NoPlagiarismDetected');
                }

                return $submissionStatus->getResult() . '%';
            case EntryPlagiarismResult::PROPERTY_STATUS:
                $submissionStatus = new SubmissionStatus(
                    $entry[EntryPlagiarismResult::PROPERTY_EXTERNAL_ID], $entry[EntryPlagiarismResult::PROPERTY_STATUS],
                    null, $entry[EntryPlagiarismResult::PROPERTY_ERROR]
                );

                if ($submissionStatus->isReportGenerated())
                {
                    $glyph = new FontAwesomeGlyph('check-circle', ['plagiarism-status-success']);
                }

                if ($submissionStatus->isInProgress())
                {
                    $glyph = new FontAwesomeGlyph('hourglass', ['plagiarism-status-in-progress']);
                }

                if ($submissionStatus->isFailed())
                {
                    $glyph = new FontAwesomeGlyph(
                        'minus-circle', ['plagiarism-status-failed'],
                        Translation::getInstance()->getTranslation(
                            $submissionStatus->getErrorTranslationVariable(), null, 'Chamilo\Application\Plagiarism'
                        )
                    );
                }

                return $glyph->render();
        }

        return parent::render_cell($column, $entry);
    }

    /**
     * Returns the actions toolbar
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[] $entry
     *
     * @return string
     */
    public function get_actions($entry)
    {
        $viewerUrl = $this->get_component()->get_url(
            [
                \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_EXTENSION,
                \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTRY_ID => $entry[Entry::PROPERTY_ID],
                ExtensionComponent::PARAM_EXTENSION => Manager::context(),
                Manager::PARAM_ACTION => Manager::ACTION_VIEW_PLAGIARISM_RESULT
            ]
        );

        $toolbar = new Toolbar();

        $status = $entry[EntryPlagiarismResult::PROPERTY_STATUS];
        if ($status == SubmissionStatus::STATUS_REPORT_GENERATED)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ViewPlagiarismReport'), new FontAwesomeGlyph('bar-chart'),
                    $viewerUrl, ToolbarItem::DISPLAY_ICON, false, null, '_blank'
                )
            );
        }

        return $toolbar->render();
    }
}