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
     * @param string[] $extended_property_names
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_TREE_NODE_ATTEMPT_ID;
        return parent::get_default_property_names($extended_property_names);
    }

    /**
     *
     * @return int
     */
    public function getTreeNodeAttemptId()
    {
        return $this->get_default_property(self::PROPERTY_TREE_NODE_ATTEMPT_ID);
    }

    /**
     *
     * @param int $treeNodeAttemptId
     */
    public function setTreeNodeAttemptId($treeNodeAttemptId)
    {
        $this->set_default_property(self::PROPERTY_TREE_NODE_ATTEMPT_ID, $treeNodeAttemptId);
    }
}
