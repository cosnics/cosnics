<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
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
class ProgressBlock extends ReportingBlock
{

    /**
     *
     * @see \core\reporting\ReportingBlock::count_data()
     */
    public function count_data()
    {
        $reporting_data = new ReportingData();

        $reporting_data->set_rows(
            array(
                Translation::get('Type'),
                Translation::get('Title'),
                Translation::get('Status'),
                Translation::get('Score'),
                Translation::get('Time'),
                Translation::get('Action')
            )
        );

        /** @var Tree $tree */
        $tree = $this->get_parent()->get_parent()->getTree();

        /** @var LearningPathTrackingService $learningPathTrackingService */
        $learningPathTrackingService = $this->get_parent()->get_parent()->getLearningPathTrackingService();

        /** @var LearningPath $learningPath */
        $learningPath = $this->get_parent()->get_parent()->get_root_content_object();

        /** @var User $user */
        $user = $this->get_parent()->get_parent()->getUser();

        $counter = 1;
        $total_time = 0;
        $attempt_count = 0;

        foreach ($tree->getTreeNodes() as $treeNode)
        {
            $totalTimeSpent = $learningPathTrackingService->getTotalTimeSpentInTreeNode(
                $learningPath, $user, $treeNode
            );

            $content_object = $treeNode->getContentObject();
            $category = $counter;
            $reporting_data->add_category($category);

            $reporting_data->add_data_category_row(
                $category,
                Translation::get('Type'),
                $content_object->get_icon_image()
            );

            $reporting_data->add_data_category_row($category, Translation::get('Title'), $content_object->get_title());

            $status = $learningPathTrackingService->isTreeNodeCompleted(
                $learningPath, $user, $treeNode
            ) ?
                Translation::get('Completed') : Translation::get('Incomplete');

            $reporting_data->add_data_category_row($category, Translation::get('Status'), $status);

            $averageScore = $learningPathTrackingService->getAverageScoreInTreeNode(
                $learningPath, $user, $treeNode
            );

            $reporting_data->add_data_category_row(
                $category,
                Translation::get('Score'),
                !is_null($averageScore) ? $averageScore . '%' : null
            );
            $reporting_data->add_data_category_row(
                $category,
                Translation::get('Time'),
                DatetimeUtilities::format_seconds_to_hours($totalTimeSpent)
            );

            $actions = array();

            if ($learningPathTrackingService->hasTreeNodeAttempts(
                $learningPath, $user, $treeNode
            )
            )
            {
                $reporting_url = $this->get_parent()->get_parent()->get_url(
                    array(
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_REPORTING,
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CHILD_ID => $treeNode->getId(
                        )
                    )
                );

                $actions[] = Theme::getInstance()->getCommonImage(
                    'Action/Statistics',
                    'png',
                    Translation::get('Details'),
                    $reporting_url,
                    ToolbarItem::DISPLAY_ICON
                );

                if ($this->get_parent()->get_parent()->is_allowed_to_edit_attempt_data())
                {
                    $delete_url = $this->get_parent()->get_parent()->get_url(
                        array(
                            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_DELETE_ATTEMPT,
                            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CHILD_ID => $treeNode->getId(
                            )
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

            $reporting_data->add_data_category_row($category, Translation::get('Action'), implode(PHP_EOL, $actions));

            $attempt_count += $learningPathTrackingService->countTreeNodeAttempts(
                $learningPath, $user, $treeNode
            );

            $total_time += $totalTimeSpent;
            $counter ++;
        }

        $category_name = '-';
        $reporting_data->add_category($category_name);
        $reporting_data->add_data_category_row($category_name, Translation::get('Title'), '');
        $reporting_data->add_data_category_row(
            $category_name,
            Translation::get('Status'),
            '<span style="font-weight: bold;">' . Translation::get('TotalTime') . '</span>'
        );
        $reporting_data->add_data_category_row($category_name, Translation::get('Score'), '');
        $reporting_data->add_data_category_row(
            $category_name,
            Translation::get('Time'),
            '<span style="font-weight: bold;">' . DatetimeUtilities::format_seconds_to_hours($total_time) . '</span>'
        );

        if ($this->get_parent()->get_parent()->is_allowed_to_edit_attempt_data() && $attempt_count > 0)
        {
            $delete_url = $this->get_parent()->get_parent()->get_url(
                array(
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_DELETE_ATTEMPT
                )
            );

            $action = Theme::getInstance()->getCommonImage(
                'Action/Delete',
                'png',
                Translation::get('DeleteAllAttempts'),
                $delete_url,
                ToolbarItem::DISPLAY_ICON
            );

            $reporting_data->add_data_category_row($category_name, Translation::get('Action'), $action);
        }

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
