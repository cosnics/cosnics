<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\Repository;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\DataClass\Publication;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationRepository extends CommonDataClassRepository
{
    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\DataClass\Publication|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findPublicationByContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($contentObjectPublication->getId())
        );

        return $this->dataClassRepository->retrieve(Publication::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * @param int[] $contentObjectPublicationIdentifiers
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator | \Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\DataClass\Publication[]
     */
    public function findPublicationsByContentObjectPublicationIdentifiers($contentObjectPublicationIdentifiers)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_PUBLICATION_ID),
            $contentObjectPublicationIdentifiers
        );

        return $this->dataClassRepository->retrieves(Publication::class, new DataClassRetrievesParameters($condition));
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return bool
     */
    public function deletePublicationForContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($contentObjectPublication->getId())
        );

        return $this->dataClassRepository->deletes(Publication::class, $condition);
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param FilterParameters|null $filterParameters
     * @return array
     */
    public function getTargetUserIds(ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters = null): array
    {
        if (is_null($filterParameters))
        {
            return DataManager::getPublicationTargetUserIds($contentObjectPublication->getId(), $contentObjectPublication->get_course_id());
        }

        $condition = null;
        $searchQuery = $filterParameters->getGlobalSearchQuery();

        if (!empty($searchQuery))
        {
            $class_name = User::class_name();
            $searchPattern = '*' . $searchQuery . '*';
            $searchPartConditions = array();
            $searchPartConditions[] = new PatternMatchCondition(new PropertyConditionVariable($class_name, User::PROPERTY_LASTNAME), $searchPattern);
            $searchPartConditions[] = new PatternMatchCondition(new PropertyConditionVariable($class_name, User::PROPERTY_FIRSTNAME), $searchPattern);
            $searchPartConditions[] = new PatternMatchCondition(new PropertyConditionVariable($class_name, User::PROPERTY_OFFICIAL_CODE), $searchPattern);
            $condition = new OrCondition($searchPartConditions);
        }

        return DataManager::getPublicationTargetUserIds($contentObjectPublication->getId(), $contentObjectPublication->get_course_id(), $filterParameters->getOffset(), $filterParameters->getCount(), $filterParameters->getOrderBy(), $condition);
    }
}