<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\ChildAttempt;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathChildAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
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
class ChildAttemptTableCellRenderer extends TableCellRenderer implements TableCellRendererActionsColumnSupport
{
    /**
     * Renders a single cell
     *
     * @param TableColumn $column
     * @param LearningPathChildAttempt $learningPathTreeNode
     *
     * @return String
     */
    public function render_cell($column, $learningPathTreeNode)
    {
        $translator = Translation::getInstance();

        switch ($column->get_name())
        {
            case 'last_start_time':
                return DatetimeUtilities::format_locale_date(null, $learningPathTreeNode->get_start_time());
            case 'status':
                return $translator->getTranslation(
                    $learningPathTreeNode->isFinished() ? 'Completed' : 'Incomplete'
                );
            case 'score':
                return $learningPathTreeNode->get_score() . '%';
            case 'time':
                return DatetimeUtilities::format_seconds_to_hours($learningPathTreeNode->get_total_time());
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
     * @param LearningPathChildAttempt $learningPathChildAttempt
     *
     * @return String
     */
    public function get_actions($learningPathChildAttempt)
    {
        if ($this->get_component()->is_allowed_to_edit_attempt_data())
        {
            $delete_url = $this->get_component()->get_url(
                array(
                    Manager::PARAM_ACTION => Manager::ACTION_ATTEMPT,
                    Manager::PARAM_CHILD_ID => $this->get_component()->getCurrentLearningPathTreeNode()->getId(),
                    Manager::PARAM_ITEM_ATTEMPT_ID => $learningPathChildAttempt->getId()
                )
            );

            $action = Theme::getInstance()->getCommonImage(
                'Action/Delete',
                'png',
                Translation::get('DeleteAttempt'),
                $delete_url,
                ToolbarItem::DISPLAY_ICON
            );

            return $action;
        }
    }
}