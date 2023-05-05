<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entry;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTable extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTable
{
    /**
     * @var \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService
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
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     */
    public function __construct(
        Application $component, AssignmentDataProvider $assignmentDataProvider, $entityId,
        LearningPathAssignmentService $learningPathAssignmentService,
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData
    )
    {
        $this->learningPathAssignmentService = $learningPathAssignmentService;
        $this->treeNodeData = $treeNodeData;
        $this->contentObjectPublication = $contentObjectPublication;

        parent::__construct($component, $assignmentDataProvider, $entityId);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    public function getContentObjectPublication()
    {
        return $this->contentObjectPublication;
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