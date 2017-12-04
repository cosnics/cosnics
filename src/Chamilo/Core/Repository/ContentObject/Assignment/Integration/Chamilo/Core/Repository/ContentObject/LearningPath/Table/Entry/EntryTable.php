<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entry;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTable extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTable
{
    /**
     * @var LearningPathAssignmentService
     */
    protected $learningPathAssignmentService;

    /**
     * @var TreeNodeData
     */
    protected $treeNodeData;

    /**
     * EntityTable constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $component
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param int $entityId
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService $learningPathAssignmentService
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     */
    public function __construct(
        Application $component, AssignmentDataProvider $assignmentDataProvider, $entityId,
        LearningPathAssignmentService $learningPathAssignmentService,
        TreeNodeData $treeNodeData
    )
    {
        parent::__construct($component, $assignmentDataProvider, $entityId);
        $this->learningPathAssignmentService = $learningPathAssignmentService;
        $this->treeNodeData = $treeNodeData;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService
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
}