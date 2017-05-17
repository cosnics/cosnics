<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\LearningPathItem;
use Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\PortfolioItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Includeable;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns the available learning paths for the given user
 *
 * @author pjbro <pjbro@users.noreply.github.com>
 * @author Sven Vanpoucke - sven.vanpoucke@hogent.be
 */
class GetLearningPathsComponent extends GetContentObjectsComponent
{
    /**
     * Builds the condition for the retrieval of the content objects
     * 
     * @param int $categoryId
     * @param string $searchQuery
     *
     * @return Condition
     */
    protected function getCondition(int $categoryId, string $searchQuery): Condition
    {
        $conditions = array();

        $conditions[] = parent::getCondition($categoryId, $searchQuery);

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TYPE),
            new StaticConditionVariable(LearningPath::class_name())
        );

        return new AndCondition($conditions);
    }

    /**
     * Validates the given content object
     *
     * @param ContentObject $contentObject
     *
     * @return bool
     */
    protected function validateContentObject(ContentObject $contentObject)
    {
        return ($contentObject instanceOf LearningPath);
    }
}