<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\LearningPathItem;
use Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\PortfolioItem;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
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
     * Returns the filter data for the given category, search query and workspace
     *
     * @param int $categoryId
     * @param string $searchQuery
     * @param WorkspaceInterface $workspace
     *
     * @return FilterData
     */
    protected function getFilterData($categoryId = null, string $searchQuery, WorkspaceInterface $workspace): FilterData
    {
        $templateRegistration = \Chamilo\Core\Repository\Configuration::getInstance()->get_registration_default_by_type(
            LearningPath::package()
        );

        $filterData = parent::getFilterData($categoryId, $searchQuery, $workspace);
        $filterData->set_filter_property(FilterData::FILTER_TYPE, $templateRegistration->getId());

        return $filterData;
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