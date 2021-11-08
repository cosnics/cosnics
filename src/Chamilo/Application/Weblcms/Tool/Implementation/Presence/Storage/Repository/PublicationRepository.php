<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\Repository;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\DataClass\Publication;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\FilterParameters\DataClassSearchQuery;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\FilterParametersTranslator;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationRepository extends CommonDataClassRepository
{
    protected \Chamilo\Core\User\Service\UserService $userService;
    protected FilterParametersTranslator $filterParametersTranslator;

    public function __construct(
        DataClassRepository $dataClassRepository, \Chamilo\Core\User\Service\UserService $userService,
        FilterParametersTranslator $filterParametersTranslator
    )
    {
        parent::__construct($dataClassRepository);
        $this->userService = $userService;
        $this->filterParametersTranslator = $filterParametersTranslator;
    }

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
     *
     * @return array
     */
    public function getTargetUserIds(
        ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters = null
    ): array
    {
        $ids = DataManager::getPublicationTargetUserIds(
            $contentObjectPublication->getId(), $contentObjectPublication->get_course_id()
        );

        if (count($ids) <= 0)
        {
            return [];
        }

        if (is_null($filterParameters))
        {
            return $ids;
        }

        $searchProperties = new DataClassProperties([
            new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME),
            new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME),
            new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE),
        ]);

        $dataClassParameters = new DataClassRetrievesParameters();
        $condition = new InCondition(new PropertyConditionVariable(User::class, User::PROPERTY_ID), $ids);
        $this->filterParametersTranslator->translateFilterParameters(
            $filterParameters, $searchProperties, $dataClassParameters, $condition
        );

        $users = $this->userService->findUsers(
            $dataClassParameters->getCondition(), $dataClassParameters->getOffset(), $dataClassParameters->getCount(),
            $dataClassParameters->getOrderBy()
        );

        $filteredUserIds = [];
        foreach($users as $user)
        {
            $filteredUserIds[] = $user->getId();
        }

        return $filteredUserIds;
    }
}
