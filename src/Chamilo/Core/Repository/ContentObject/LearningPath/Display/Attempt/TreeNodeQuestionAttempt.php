<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Attempt\AbstractQuestionAttempt;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class TreeNodeQuestionAttempt extends AbstractQuestionAttempt
{
    const PROPERTY_TREE_NODE_ATTEMPT_ID = 'tree_node_attempt_id';

    /**
     *
     * @param string[] $extendedPropertyNames
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_TREE_NODE_ATTEMPT_ID;
        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     *
     * @return int
     */
    public function getTreeNodeAttemptId()
    {
        return $this->getDefaultProperty(self::PROPERTY_TREE_NODE_ATTEMPT_ID);
    }

    /**
     *
     * @param int $treeNodeAttemptId
     */
    public function setTreeNodeAttemptId($treeNodeAttemptId)
    {
        $this->setDefaultProperty(self::PROPERTY_TREE_NODE_ATTEMPT_ID, $treeNodeAttemptId);
    }
}
