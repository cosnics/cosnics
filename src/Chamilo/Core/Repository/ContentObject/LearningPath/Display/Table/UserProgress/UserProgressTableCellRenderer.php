<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\UserProgress;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ProgressBarRenderer;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * Shows the progress of some tree nodes for a user in the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserProgressTableCellRenderer extends RecordTableCellRenderer implements TableCellRendererActionsColumnSupport
{
    /**
     * @return TreeNode
     */
    protected function getCurrentTreeNode()
    {
        return $this->get_component()->getCurrentTreeNode();
    }

    /**
     * @return LearningPath
     */
    protected function getLearningPath()
    {
        return $this->get_component()->get_root_content_object();
    }

    protected function getReportingUrl($userId)
    {
        return $this->get_component()->get_url(
            array(
                Manager::PARAM_ACTION => Manager::ACTION_REPORTING, Manager::PARAM_REPORTING_USER_ID => $userId
            )
        );
    }

    /**
     * @return TrackingService
     */
    protected function getTrackingService()
    {
        return $this->get_component()->getTrackingService();
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        return $this->get_component()->getUser();
    }

    /**
     * Returns the actions toolbar
     *
     * @param array $record
     *
     * @return String
     */
    public function get_actions($record)
    {
        $learningPath = $this->getLearningPath();
        $trackingService = $this->getTrackingService();

        $reportingUser = new User();
        $reportingUser->setId($record[TreeNodeAttempt::PROPERTY_USER_ID]);

        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        $reportingUrl = $this->getReportingUrl($record['user_id']);

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Reporting'), new FontAwesomeGlyph('chart-pie'), $reportingUrl,
                ToolbarItem::DISPLAY_ICON
            )
        );

        if ($trackingService->hasTreeNodeAttempts(
            $learningPath, $reportingUser, $this->getCurrentTreeNode()
        ))
        {
            if ($this->get_component()->is_allowed_to_edit_attempt_data() &&
                $trackingService->canDeleteLearningPathAttemptData($this->getUser(), $reportingUser))
            {
                $delete_url = $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE_ATTEMPTS_FOR_TREE_NODE,
                        Manager::PARAM_REPORTING_USER_ID => $record['user_id']
                    )
                );

                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('DeleteAttempt'), new FontAwesomeGlyph('times'), $delete_url,
                        ToolbarItem::DISPLAY_ICON, true
                    )
                );
            }
        }

        return $toolbar->render();
    }

    /**
     * Renders a single cell
     *
     * @param TableColumn $column
     * @param string[] $record
     *
     * @return String
     */
    public function render_cell($column, $record)
    {
        switch ($column->get_name())
        {
            case 'progress':
                $trackingService = $this->getTrackingService();
                $learningPath = $this->getLearningPath();
                $currentTreeNode = $this->getCurrentTreeNode();

                $user = new User();
                $user->setId($record[TreeNodeAttempt::PROPERTY_USER_ID]);

                $progress = $trackingService->getLearningPathProgress(
                    $learningPath, $user, $currentTreeNode
                );

                $progressBarRenderer = new ProgressBarRenderer();

                return $progressBarRenderer->render($progress);
            case 'completed':
                $numberOfNodes = $record['nodes_completed'];
                $currentTreeNode = $this->getCurrentTreeNode();

                if ($numberOfNodes >= count($currentTreeNode->getDescendantNodes()) + 1)
                {
                    $glyph = new FontAwesomeGlyph(
                        'check-circle', array('text-success'), null, 'fas'
                    );

                    return $glyph->render();
                }

                return null;
            case 'started':
                $numberOfNodes = $record['nodes_completed'];
                if ($numberOfNodes > 0)
                {
                    $glyph = new FontAwesomeGlyph(
                        'check-circle', array('text-success'), null, 'fas'
                    );

                    return $glyph->render();
                }

                return null;

            case User::PROPERTY_FIRSTNAME:
            case User::PROPERTY_LASTNAME:
                return '<a href="' . $this->getReportingUrl($record['user_id']) . '">' .
                    parent::render_cell($column, $record) . '</a>';
        }

        return parent::render_cell($column, $record);
    }
}