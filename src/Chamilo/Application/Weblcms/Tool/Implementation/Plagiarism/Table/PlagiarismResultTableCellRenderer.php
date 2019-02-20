<?php

namespace Chamilo\Application\Plagiarism\Table;

use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\DataClass\ContentObjectPlagiarismResult;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Application\Plagiarism\Table
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlagiarismResultTableCellRenderer extends RecordTableCellRenderer
{
    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn $column
     * @param string[] $contentObjectPlagiarismResult
     *
     * @return string
     */
    public function render_cell($column, $contentObjectPlagiarismResult)
    {
        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TITLE :
                $title = strip_tags($contentObjectPlagiarismResult[ContentObject::PROPERTY_TITLE]);
                $title =
                    StringUtilities::getInstance()->createString($title)->safeTruncate(50, ' &hellip;')->__toString();

                return $title;
            case ContentObject::PROPERTY_DESCRIPTION :
                $description = strip_tags($contentObjectPlagiarismResult[ContentObject::PROPERTY_DESCRIPTION]);

                return StringUtilities::getInstance()->createString($description)->safeTruncate(100, ' &hellip;')
                    ->__toString();
            case ContentObjectPlagiarismResult::PROPERTY_RESULT:
                $submissionStatus = new SubmissionStatus(
                    $contentObjectPlagiarismResult[ContentObjectPlagiarismResult::PROPERTY_EXTERNAL_ID],
                    $contentObjectPlagiarismResult[ContentObjectPlagiarismResult::PROPERTY_STATUS],
                    $contentObjectPlagiarismResult[ContentObjectPlagiarismResult::PROPERTY_RESULT],
                    $contentObjectPlagiarismResult[ContentObjectPlagiarismResult::PROPERTY_ERROR]
                );

                if (!$submissionStatus->isReportGenerated())
                {
                    return null;
                }

                if (empty($submissionStatus->getResult()))
                {
                    return Translation::getInstance()->getTranslation('NoPlagiarismDetected');
                }

                return $submissionStatus->getResult() . '%';
            case ContentObjectPlagiarismResult::PROPERTY_STATUS:
                $submissionStatus = new SubmissionStatus(
                    $contentObjectPlagiarismResult[ContentObjectPlagiarismResult::PROPERTY_EXTERNAL_ID],
                    $contentObjectPlagiarismResult[ContentObjectPlagiarismResult::PROPERTY_STATUS],
                    null, $contentObjectPlagiarismResult[ContentObjectPlagiarismResult::PROPERTY_ERROR]
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

        return parent::render_cell($column, $contentObjectPlagiarismResult);
    }

    /**
     * Returns the actions toolbar
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[] $contentObjectPlagiarismResult
     *
     * @return string
     */
    public function get_actions($contentObjectPlagiarismResult)
    {
        $viewerUrl = $this->get_component()->get_url(
            [
                Manager::PARAM_ACTION => Manager::ACTION_VIEW_REPORT,
                Manager::PARAM_CONTENT_OBJECT_PLAGIARISM_RESULT_ID => $contentObjectPlagiarismResult[ContentObjectPlagiarismResult::PROPERTY_ID]
            ]
        );

        $toolbar = new Toolbar();

        $status = $contentObjectPlagiarismResult[ContentObjectPlagiarismResult::PROPERTY_STATUS];
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