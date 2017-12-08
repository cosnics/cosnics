<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPath\Assignment\LearningPathAssignmentService;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTable extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable
{
    const TABLE_IDENTIFIER = User::PROPERTY_ID;

    /**
     * @var LearningPathAssignmentService
     */
    protected $learningPathAssignmentService;

    /**
     * @var TreeNodeData
     */
    protected $treeNodeData;

    /**
     * @var int[]
     */
    protected $userIds;

    /**
     * EntityTable constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $component
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPath\Assignment\LearningPathAssignmentService $learningPathAssignmentService
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param int[] $userIds
     */
    public function __construct(
        Application $component, AssignmentDataProvider $assignmentDataProvider,
        LearningPathAssignmentService $learningPathAssignmentService,
        TreeNodeData $treeNodeData,
        $userIds = []
    )
    {
        parent::__construct($component, $assignmentDataProvider);
        $this->learningPathAssignmentService = $learningPathAssignmentService;
        $this->treeNodeData = $treeNodeData;
        $this->userIds = $userIds;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPath\Assignment\LearningPathAssignmentService
     */
    public function getLearningPathAssignmentService()
    {
        return $this->learningPathAssignmentService;
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData
     */
    public function getTreeNodeData()
    {
        return $this->treeNodeData;
    }

    /**
     * @return int[]
     */
    public function getUserIds()
    {
        return $this->userIds;
    }

}