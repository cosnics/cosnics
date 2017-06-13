<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TreeNodeAttempt;

use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TrackingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ProgressBarRenderer;
use Chamilo\Libraries\Format\Structure\Toolbar;
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
class TreeNodeAttemptTableCellRenderer extends TableCellRenderer implements TableCellRendererActionsColumnSupport
{
    /**
     * Renders a single cell
     *
     * @param TableColumn $column
     * @param TreeNodeAttempt $treeNodeAttempt
     *
     * @return String
     */
    public function render_cell($column, $treeNodeAttempt)
    {
        $translator = Translation::getInstance();

        switch ($column->get_name())
        {
            case 'last_start_time':
                return DatetimeUtilities::format_locale_date(null, $treeNodeAttempt->get_start_time());
            case 'status':
                return $translator->getTranslation(
                    $treeNodeAttempt->isCompleted() ? 'Completed' : 'Incomplete'
                );
            case 'score':
                $progressBarRenderer = new ProgressBarRenderer();

                return $progressBarRenderer->render(
                    (int) $treeNodeAttempt->get_score(), ProgressBarRenderer::MODE_SUCCESS
                );

            case 'time':
                return DatetimeUtilities::format_seconds_to_hours($treeNodeAttempt->get_total_time());
        }

        return parent::render_cell($column, $treeNodeAttempt);
    }

    /**
     * Define the unique identifier for the row needed for e.g.
     * checkboxes
     *
     * @param TreeNode $treeNode
     *
     * @return int
     */
    public function render_id_cell($treeNode)
    {
        return $treeNode->getId();
    }

    /**
     * Returns the actions toolbar
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     *
     * @return String
     */
    public function get_actions($treeNodeAttempt)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        if ($this->getCurrentTreeNode()->getContentObject() instanceof Assessment)
        {
            $assessmentResultViewerUrl = $this->get_component()->get_url(
                array(
                    Manager::PARAM_ACTION => Manager::ACTION_VIEW_ASSESSMENT_RESULT,
                    Manager::PARAM_CHILD_ID => $this->get_component()->getCurrentTreeNode()->getId(),
                    Manager::PARAM_ITEM_ATTEMPT_ID => $treeNodeAttempt->getId()
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ViewAssessmentResult'),
                    Theme::getInstance()->getCommonImagePath('Action/Reporting'),
                    $assessmentResultViewerUrl,
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($this->get_component()->is_allowed_to_edit_attempt_data() &&
            $this->getTrackingService()->canDeleteLearningPathAttemptData(
                $this->getUser(), $this->getReportingUser()
            )
        )
        {
            $delete_url = $this->get_component()->get_url(
                array(
                    Manager::PARAM_ACTION => Manager::ACTION_DELETE_TREE_NODE_ATTEMPT,
                    Manager::PARAM_CHILD_ID => $this->get_component()->getCurrentTreeNode()->getId(),
                    Manager::PARAM_ITEM_ATTEMPT_ID => $treeNodeAttempt->getId()
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('DeleteAttempt'),
                    Theme::getInstance()->getCommonImagePath('Action/Delete'),
                    $delete_url,
                    ToolbarItem::DISPLAY_ICON,
                    true
                )
            );
        }

        return $toolbar->render();
    }

    /**
     * @return TreeNode
     */
    protected function getCurrentTreeNode()
    {
        return $this->get_component()->getCurrentTreeNode();
    }

    /**
     * @return User
     */
    protected function getReportingUser()
    {
        return $this->get_component()->getReportingUser();
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        return $this->get_component()->getUser();
    }

    /**
     * @return TrackingService
     */
    protected function getTrackingService()
    {
        return $this->get_component()->getTrackingService();
    }
}