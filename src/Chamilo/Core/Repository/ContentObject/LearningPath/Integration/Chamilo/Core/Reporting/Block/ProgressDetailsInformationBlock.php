<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html\PropertiesTable;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package core\repository\content_object\learning_path\display\integration\core\reporting
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ProgressDetailsInformationBlock extends ReportingBlock
{

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

        $content_object = $treeNode->getContentObject();
        $reporting_data = new ReportingData();

        $reporting_data->set_rows(
            array(
                Translation::get('Title'),
                Translation::get('Description'),
                Translation::get('AverageScore'),
                Translation::get('NumberOfAttempts')
            )
        );

        $reporting_data->add_category(0);
        $reporting_data->add_data_category_row(0, Translation::get('Title'), $content_object->get_title());
        $reporting_data->add_data_category_row(0, Translation::get('Description'), $content_object->get_description());

        $reporting_data->add_data_category_row(
            0,
            Translation::get('AverageScore'),
            $trackingService->getAverageScoreInTreeNode(
                $learningPath, $user, $treeNode
            ) . '%'
        );

        $reporting_data->add_data_category_row(
            0,
            Translation::get('NumberOfAttempts'),
            $trackingService->countTreeNodeAttempts($learningPath, $user, $treeNode)
        );

        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(PropertiesTable::VIEW);
    }
}
