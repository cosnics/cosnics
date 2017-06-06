<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 *
 * @package core\repository\content_object\learning_path\display\integration\core\reporting
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ProgressDetailsBlock extends ReportingBlock
{

    /**
     *
     * @see \core\reporting\ReportingBlock::count_data()
     */
    public function count_data()
    {
        /** @var TreeNode $treeNode */
        $treeNode = $this->get_parent()->get_parent()->getCurrentTreeNode();

        /** @var LearningPathTrackingService $learningPathTrackingService */
        $learningPathTrackingService = $this->get_parent()->get_parent()->getLearningPathTrackingService();

        /** @var LearningPath $learningPath */
        $learningPath = $this->get_parent()->get_parent()->get_root_content_object();

        /** @var User $user */
        $user = $this->get_parent()->get_parent()->getUser();

        $reporting_data = new ReportingData();

        $showScore = $treeNode->getContentObject() instanceof Assessment;

        $rows = array(
            Translation::get('LastStartTime'),
            Translation::get('Status'),
            Translation::get('Time'),
            Translation::get('Action')
        );

        if($showScore)
        {
            array_splice($rows, 2, 0, Translation::get('Score'));
        }

        $reporting_data->set_rows($rows);

        $attempts = $learningPathTrackingService->getTreeNodeAttempts(
            $learningPath, $user, $treeNode
        );

        $counter = 1;

        foreach ($attempts as $learningPathChildAttempt)
        {
            $category = $counter;
            $reporting_data->add_category($category);
            $reporting_data->add_data_category_row(
                $category,
                Translation::get('LastStartTime'),
                DatetimeUtilities::format_locale_date(null, $learningPathChildAttempt->get_start_time())
            );
            $reporting_data->add_data_category_row(
                $category,
                Translation::get('Status'),
                Translation::get($learningPathChildAttempt->isFinished() ? 'Completed' : 'Incomplete')
            );

            if($showScore)
            {
                $reporting_data->add_data_category_row(
                    $category,
                    Translation::get('Score'),
                    $learningPathChildAttempt->get_score() . '%'
                );
            }

            $reporting_data->add_data_category_row(
                $category,
                Translation::get('Time'),
                DatetimeUtilities::format_seconds_to_hours($learningPathChildAttempt->get_total_time())
            );

            if ($this->get_parent()->get_parent()->is_allowed_to_edit_attempt_data())
            {
                $delete_url = $this->get_parent()->get_parent()->get_url(
                    array(
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION =>
                            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_DELETE_ATTEMPT,
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CHILD_ID =>
                            $treeNode->getId(),
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ITEM_ATTEMPT_ID =>
                            $learningPathChildAttempt->getId()
                    )
                );

                $action = Theme::getInstance()->getCommonImage(
                    'Action/Delete',
                    'png',
                    Translation::get('DeleteAttempt'),
                    $delete_url,
                    ToolbarItem::DISPLAY_ICON
                );

                $reporting_data->add_data_category_row($category, Translation::get('Action'), $action);
            }

            $counter ++;
        }

        $category = '-';
        $reporting_data->add_category($category);
        $reporting_data->add_data_category_row($category, Translation::get('LastStartTime'), '');
        $reporting_data->add_data_category_row(
            $category,
            Translation::get('Status'),
            '<span style="font-weight: bold;">' . Translation::get('TotalTime') . '</span>'
        );
        $reporting_data->add_data_category_row($category, Translation::get('Score'), '');

        $reporting_data->add_data_category_row(
            $category,
            Translation::get('Time'),
            '<span style="font-weight: bold;">' .
            DatetimeUtilities::format_seconds_to_hours(
                $learningPathTrackingService->getTotalTimeSpentInTreeNode(
                    $learningPath, $user, $treeNode
                )
            ) . '</span>'
        );

        return $reporting_data;
    }

    /**
     *
     * @see \core\reporting\ReportingBlock::retrieve_data()
     */
    public function retrieve_data()
    {
        return $this->count_data();
    }

    /**
     *
     * @see \core\reporting\ReportingBlock::get_views()
     */
    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }
}
