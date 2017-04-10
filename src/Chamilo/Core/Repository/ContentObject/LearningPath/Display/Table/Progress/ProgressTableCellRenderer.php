<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\Progress;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Table\TableCellRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * Shows the progress of some tree nodes for a user in the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ProgressTableCellRenderer extends TableCellRenderer implements TableCellRendererActionsColumnSupport
{
    /**
     * Renders a single cell
     *
     * @param TableColumn $column
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return String
     */
    public function render_cell($column, $learningPathTreeNode)
    {
        $translator = Translation::getInstance();

        $content_object = $learningPathTreeNode->getContentObject();

        /** @var LearningPath $learningPath */
        $learningPath = $this->get_component()->get_root_content_object();

        /** @var User $user */
        $user = $this->get_component()->getUser();

        /** @var LearningPathTrackingService $learningPathTrackingService */
        $learningPathTrackingService = $this->get_component()->getLearningPathTrackingService();

        switch ($column->get_name())
        {
            case 'type':
                return $content_object->get_icon_image();
            case 'title':
                return '<a href="' . $this->getReportingUrl($learningPathTreeNode) . '">' .
                    $content_object->get_title() . '</a>';
            case 'status':
                return $learningPathTrackingService->isLearningPathTreeNodeCompleted(
                    $learningPath, $user, $learningPathTreeNode
                ) ? $translator->getTranslation('Completed') : $translator->getTranslation('Incomplete');
            case 'score':
                $averageScore = $learningPathTrackingService->getAverageScoreInLearningPathTreeNode(
                    $learningPath, $user, $learningPathTreeNode
                );

                return !is_null($averageScore) ? $averageScore . '%' : null;
            case 'time':
                $totalTimeSpent = $learningPathTrackingService->getTotalTimeSpentInLearningPathTreeNode(
                    $learningPath, $user, $learningPathTreeNode
                );

                return DatetimeUtilities::format_seconds_to_hours($totalTimeSpent);
        }

        return parent::render_cell($column, $learningPathTreeNode);
    }

    /**
     * Define the unique identifier for the row needed for e.g.
     * checkboxes
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return int
     */
    public function render_id_cell($learningPathTreeNode)
    {
        return $learningPathTreeNode->getId();
    }

    /**
     * Returns the actions toolbar
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return String
     */
    public function get_actions($learningPathTreeNode)
    {
        /** @var LearningPath $learningPath */
        $learningPath = $this->get_component()->get_root_content_object();

        /** @var User $user */
        $user = $this->get_component()->getUser();

        /** @var LearningPathTrackingService $learningPathTrackingService */
        $learningPathTrackingService = $this->get_component()->getLearningPathTrackingService();

        $actions = array();

        if ($learningPathTrackingService->hasLearningPathTreeNodeAttempts(
            $learningPath, $user, $learningPathTreeNode
        )
        )
        {
            $reporting_url = $this->getReportingUrl($learningPathTreeNode);

            $actions[] = Theme::getInstance()->getCommonImage(
                'Action/Statistics',
                'png',
                Translation::get('Details'),
                $reporting_url,
                ToolbarItem::DISPLAY_ICON
            );

            if ($this->get_component()->is_allowed_to_edit_attempt_data())
            {
                $delete_url = $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_ATTEMPT,
                        Manager::PARAM_CHILD_ID => $learningPathTreeNode->getId()
                    )
                );

                $actions[] = Theme::getInstance()->getCommonImage(
                    'Action/Delete',
                    'png',
                    Translation::get('DeleteAttempt'),
                    $delete_url,
                    ToolbarItem::DISPLAY_ICON
                );
            }
        }

        return implode(PHP_EOL, $actions);
    }

    /**
     * Returns the reporting URL for a given node
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return string
     */
    protected function getReportingUrl(LearningPathTreeNode $learningPathTreeNode)
    {
        return $this->get_component()->get_url(
            array(
                Manager::PARAM_ACTION => Manager::ACTION_REPORTING,
                Manager::PARAM_CHILD_ID => $learningPathTreeNode->getId()
            )
        );
    }
}