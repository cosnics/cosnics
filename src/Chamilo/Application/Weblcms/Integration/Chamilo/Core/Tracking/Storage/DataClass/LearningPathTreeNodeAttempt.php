<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\StorageParameters;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class LearningPathTreeNodeAttempt extends TreeNodeAttempt
{
    public const CONTEXT = 'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking';

    public const PROPERTY_PUBLICATION_ID = 'publication_id';

    /**
     */
    public function delete(): bool
    {
        $succes = parent::delete();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathTreeNodeQuestionAttempt::class,
                LearningPathTreeNodeQuestionAttempt::PROPERTY_TREE_NODE_ATTEMPT_ID
            ), new StaticConditionVariable($this->get_id())
        );

        $trackers = DataManager::retrieves(
            LearningPathTreeNodeQuestionAttempt::class, new StorageParameters(condition: $condition)
        );

        foreach ($trackers as $tracker)
        {
            $succes &= $tracker->delete();
        }

        return $succes;
    }

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_PUBLICATION_ID;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'tracking_weblcms_learning_path_tree_node_attempt';
    }

    /**
     * @return int
     */
    public function get_publication_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLICATION_ID);
    }

    /**
     * @param int $publication_id
     */
    public function set_publication_id($publication_id)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLICATION_ID, $publication_id);
    }
}
