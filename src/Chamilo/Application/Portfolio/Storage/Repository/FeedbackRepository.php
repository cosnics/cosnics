<?php
namespace Chamilo\Application\Portfolio\Storage\Repository;

use Chamilo\Application\Portfolio\Storage\DataClass\Feedback;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\DataClassParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Application\Portfolio\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FeedbackRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @param int $publicationIdentifier
     * @param int $complexContentObjectIdentifier
     * @param int $userIdentifier
     *
     * @return int
     */
    public function countFeedbackForPublicationComplexContentObjectAndUserIdentifiers(
        int $publicationIdentifier, int $complexContentObjectIdentifier = null, int $userIdentifier = null
    )
    {
        return $this->getDataClassRepository()->count(
            Feedback::class, new DataClassParameters(
                condition: $this->getFeedbackConditions(
                    $publicationIdentifier, $complexContentObjectIdentifier, $userIdentifier
                )
            )
        );
    }

    /**
     *
     * @param int $identifier
     *
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Feedback
     */
    public function findFeedbackByIdentfier($identifier)
    {
        return $this->getDataClassRepository()->retrieveById(Feedback::class, $identifier);
    }

    /**
     *
     * @param int $publicationIdentifier
     * @param int $complexContentObjectIdentifier
     * @param int $userIdentifier
     * @param int $count
     * @param int $offset
     *
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Feedback[]
     */
    public function findFeedbackForPublicationComplexContentObjectUserIdentifiersCountAndOffset(
        int $publicationIdentifier, int $complexContentObjectIdentifier = null, int $userIdentifier = null,
        int $count = null, int $offset = null
    )
    {
        $parameters = new DataClassParameters(
            condition: $this->getFeedbackConditions(
                $publicationIdentifier, $complexContentObjectIdentifier, $userIdentifier
            ), orderBy: new OrderBy([
            new OrderProperty(
                new PropertyConditionVariable(Feedback::class, Feedback::PROPERTY_MODIFICATION_DATE), SORT_DESC
            )
        ]), count: $count, offset: $offset
        );

        return $this->getDataClassRepository()->retrieves(Feedback::class, $parameters);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->dataClassRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    protected function setDataClassRepository($dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @param int $publicationIdentifier
     * @param int $complexContentObjectIdentifier
     * @param int $userIdentifier
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    private function getFeedbackConditions(
        int $publicationIdentifier, int $complexContentObjectIdentifier = null, int $userIdentifier = null
    )
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Feedback::class, Feedback::PROPERTY_COMPLEX_CONTENT_OBJECT_ID),
            $complexContentObjectIdentifier ? new StaticConditionVariable($complexContentObjectIdentifier) : null
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Feedback::class, Feedback::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($publicationIdentifier)
        );

        if ($userIdentifier)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Feedback::class, Feedback::PROPERTY_USER_ID),
                new StaticConditionVariable($userIdentifier)
            );
        }

        return new AndCondition($conditions);
    }
}