<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathStepContext;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LearningPathStepContextRepository extends CommonDataClassRepository
{
    /**
     * @param int $id
     *
     * @return LearningPathStepContext | \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findLearningPathStepContextById(int $id)
    {
        return $this->dataClassRepository->retrieveById(LearningPathStepContext::class_name(), $id);
    }

    /**
     * @param int $stepId
     * @param string $contextClass
     * @param int $contextId
     *
     * @return LearningPathStepContext | \Chamilo\Libraries\Storage\DataClass\DataClass | false
     */
    public function findLearningPathStepContext(int $stepId, string $contextClass, int $contextId)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(LearningPathStepContext::class_name(), LearningPathStepContext::PROPERTY_LEARNING_PATH_STEP_ID),
            new StaticConditionVariable($stepId)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(LearningPathStepContext::class_name(), LearningPathStepContext::PROPERTY_CONTEXT_CLASS),
            new StaticConditionVariable($contextClass)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(LearningPathStepContext::class_name(), LearningPathStepContext::PROPERTY_CONTEXT_ID),
            new StaticConditionVariable($contextId)
        );
        $condition = new AndCondition($conditions);
        $parameters = new DataClassRetrieveParameters($condition);

        return $this->dataClassRepository->retrieve(LearningPathStepContext::class_name(), $parameters);
    }
}