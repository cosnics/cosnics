<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class LearningPathTreeNodeAttempt extends \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt
{
    const PROPERTY_PUBLICATION_ID = 'publication_id';

    /**
     *
     * @param string[] $extended_property_names
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_PUBLICATION_ID;

        return parent::get_default_property_names($extended_property_names);
    }

    /**
     *
     * @return int
     */
    public function get_publication_id()
    {
        return $this->get_default_property(self::PROPERTY_PUBLICATION_ID);
    }

    /**
     *
     * @param int $publication_id
     */
    public function set_publication_id($publication_id)
    {
        $this->set_default_property(self::PROPERTY_PUBLICATION_ID, $publication_id);
    }

    /**
     */
    public function delete()
    {
        $succes = parent::delete();
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathTreeNodeQuestionAttempt::class_name(),
                LearningPathTreeNodeQuestionAttempt::PROPERTY_TREE_NODE_ATTEMPT_ID),
            new StaticConditionVariable($this->get_id()));
        
        $trackers = DataManager::retrieves(
            LearningPathTreeNodeQuestionAttempt::class_name(),
            new DataClassRetrievesParameters($condition));
        
        while ($tracker = $trackers->next_result())
        {
            $succes &= $tracker->delete();
        }
        
        return $succes;
    }
}
