<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
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

        /** @var TrackingService $trackingService */
        $trackingService = $this->get_parent()->get_parent()->getTrackingService();

        /** @var LearningPath $learningPath */
        $learningPath = $this->get_parent()->get_parent()->get_root_content_object();

        /** @var User $user */
        $user = $this->get_parent()->get_parent()->getUser();

        $reporting_data = new ReportingData();

        $showScore = $treeNode->supportsScore();

        $rows = array(
            Translation::get('LastStartTime'), Translation::get('Status'), Translation::get('Time'),
            Translation::get('Action')
        );

        if ($showScore)
        {
            array_splice($rows, 2, 0, Translation::get('Score'));
        }

        $reporting_data->set_rows($rows);

        $attempts = $trackingService->getTreeNodeAttempts(
            $learningPath, $user, $treeNode
        );

        $counter = 1;

        foreach ($attempts as $treeNodeAttempt)
        {
            $category = $counter;
            $reporting_data->add_category($category);
            $reporting_data->add_data_category_row(
                $category, Translation::get('LastStartTime'),
                DatetimeUtilities::format_locale_date(null, $treeNodeAttempt->get_start_time())
            );
            $reporting_data->add_data_category_row(
                $category, Translation::get('Status'),
                Translation::get($treeNodeAttempt->isCompleted() ? 'Completed' : 'Incomplete')
            );

            if ($showScore)
            {
                $reporting_data->add_data_category_row(
                    $category, Translation::get('Score'), $treeNodeAttempt->get_score() . '%'
                );
            }

            $reporting_data->add_data_category_row(
                $category, Translation::get('Time'),
                DatetimeUtilities::format_seconds_to_hours($treeNodeAttempt->get_total_time())
            );

            if ($this->get_parent()->get_parent()->is_allowed_to_edit_attempt_data())
            {
                $delete_url = $this->get_parent()->get_parent()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE_TREE_NODE_ATTEMPT,
                        Manager::PARAM_CHILD_ID => $treeNode->getId(),
                        Manager::PARAM_ITEM_ATTEMPT_ID => $treeNodeAttempt->getId()
                    )
                );

                $glyph = new FontAwesomeGlyph('times', array(), Translation::get('DeleteAttempt'));
                $action = '<a href="' . $delete_url . '">' . $glyph->render() . '</a>';

                $reporting_data->add_data_category_row($category, Translation::get('Action'), $action);
            }

            $counter ++;
        }

        $category = '-';
        $reporting_data->add_category($category);
        $reporting_data->add_data_category_row($category, Translation::get('LastStartTime'), '');
        $reporting_data->add_data_category_row(
            $category, Translation::get('Status'),
            '<span style="font-weight: bold;">' . Translation::get('TotalTime') . '</span>'
        );
        $reporting_data->add_data_category_row($category, Translation::get('Score'), '');

        $reporting_data->add_data_category_row(
            $category, Translation::get('Time'),
            '<span style="font-weight: bold;">' . DatetimeUtilities::format_seconds_to_hours(
                $trackingService->getTotalTimeSpentInTreeNode(
                    $learningPath, $user, $treeNode
                )
            ) . '</span>'
        );

        return $reporting_data;
    }

    /**
     *
     * @see \core\reporting\ReportingBlock::get_views()
     */
    public function get_views()
    {
        return array(Html::VIEW_TABLE);
    }

    /**
     *
     * @see \core\reporting\ReportingBlock::retrieve_data()
     */
    public function retrieve_data()
    {
        return $this->count_data();
    }
}
