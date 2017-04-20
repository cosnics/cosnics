<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\UserProgress;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ProgressBarRenderer;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * Shows the progress of some tree nodes for a user in the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserProgressTableCellRenderer extends RecordTableCellRenderer implements TableCellRendererActionsColumnSupport
{
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
                if(is_null($record[LearningPathAttempt::PROPERTY_PROGRESS]))
                {
                    return null;
                }

                $progressBarRenderer = new ProgressBarRenderer();
                return $progressBarRenderer->render((int) $record[LearningPathAttempt::PROPERTY_PROGRESS]);
        }

        return parent::render_cell($column, $record);
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
        if(is_null($record[LearningPathAttempt::PROPERTY_PROGRESS]))
        {
            return null;
        }

        $learningPath = $this->getLearningPath();
        $user = $this->getUser();
        $learningPathTrackingService = $this->getLearningPathTrackingService();

        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        $reportingUrl = $this->get_component()->get_url(
            array(
                Manager::PARAM_ACTION => Manager::ACTION_REPORTING,
                Manager::PARAM_REPORTING_USER_ID => $record['user_id'],
                Manager::PARAM_CHILD_ID => 0
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Details'),
                Theme::getInstance()->getCommonImagePath('Action/Statistics'),
                $reportingUrl,
                ToolbarItem::DISPLAY_ICON
            )
        );

        if ($learningPathTrackingService->hasLearningPathTreeNodeAttempts(
            $learningPath, $user, $this->getCurrentLearningPathTreeNode()
        )
        )
        {
            if ($this->get_component()->is_allowed_to_edit_attempt_data())
            {
                $delete_url = $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_ATTEMPT
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
        }

        return $toolbar->render();
    }

    /**
     * @return LearningPath
     */
    protected function getLearningPath()
    {
        return $this->get_component()->get_root_content_object();
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        return $this->get_component()->getUser();
    }

    /**
     * @return LearningPathTrackingService
     */
    protected function getLearningPathTrackingService()
    {
        return $this->get_component()->getLearningPathTrackingService();
    }

    /**
     * @return LearningPathTreeNode
     */
    protected function getCurrentLearningPathTreeNode()
    {
        return $this->get_component()->getCurrentLearningPathTreeNode();
    }
}