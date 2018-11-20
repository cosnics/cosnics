<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\Repository;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FeedbackRepository  extends \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository\FeedbackRepository
{
    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countFeedbackForTreeNodeDataByEntityTypeAndEntityId(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType, $entityId
    )
    {
        return $this->countFeedbackByEntityTypeAndEntityId(
            $entityType, $entityId,
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData)
        );
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctFeedbackByTreeNodeDataAndEntityType(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType
    )
    {
        return $this->countDistinctFeedbackByEntityType(
            $entityType, $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData)
        );
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctFeedbackForTreeNodeDataEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType, $entityId
    )
    {
        return parent::countDistinctFeedbackForEntityTypeAndId(
            $entityType, $entityId,
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function getTreeNodeDataConditionByPublication(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, Condition $condition = null
    )
    {
        return $this->getTreeNodeDataConditionByIdentifierAndPublication(
            $contentObjectPublication, $treeNodeData->getId(), $condition
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param int $treeNodeDataIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function getTreeNodeDataConditionByIdentifierAndPublication(
        ContentObjectPublication $contentObjectPublication, $treeNodeDataIdentifier, Condition $condition = null
    )
    {
        $contentObjectPublicationCondition = new EqualityCondition(
            new PropertyConditionVariable(
                $this->getEntryClassName(),
                Entry::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID
            ),
            new StaticConditionVariable($contentObjectPublication->getId())
        );

        $conditions = array();

        ($condition instanceof Condition) ? $conditions[] = $condition : null;

        $conditions[] = $contentObjectPublicationCondition;

        $condition = new AndCondition($conditions);

        return $this->getTreeNodeDataConditionByIdentifier($treeNodeDataIdentifier, $condition);
    }

    /**
     * @return string
     */
    protected function getEntryClassName()
    {
        return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry::class;
    }

    /**
     * @return string
     */
    protected function getFeedbackClassName()
    {
        return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Feedback::class;
    }
}