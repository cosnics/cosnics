<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathStepContext;

/**
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LearningPathStepContextRepository extends CommonDataClassRepository
{
    /**
     * @param int $learingPathStepId
     * @return LearningPathStepContext | \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findLearningPathStepContext(int $learingPathStepId)
    {
        return $this->dataClassRepository->retrieveById(LearningPathStepContext::class_name(), $learingPathStepId);
    }
}