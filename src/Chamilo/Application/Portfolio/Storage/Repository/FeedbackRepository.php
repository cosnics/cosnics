<?php
namespace Chamilo\Application\Portfolio\Storage\Repository;

use Chamilo\Application\Portfolio\Storage\DataClass\Feedback;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
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
     * @param integer $publicationIdentifier
     * @param integer $complexContentObjectIdentifier
     * @param integer $userIdentifier
     * @return integer
     */
    public function countFeedbackForPublicationComplexContentObjectAndUserIdentifiers(int $publicationIdentifier,
        int $complexContentObjectIdentifier = null, int $userIdentifier = null)
    {
        return $this->getDataClassRepository()->count(
            Feedback::class,
            new DataClassCountParameters(
                $this->getFeedbackConditions($publicationIdentifier, $complexContentObjectIdentifier, $userIdentifier)));
    }

    /**
     *
     * @param integer $identifier
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Feedback
     */
    public function findFeedbackByIdentfier($identifier)
    {
        return $this->getDataClassRepository()->retrieveById(Feedback::class, $identifier);
    }

    /**
     *
     * @param integer $publicationIdentifier
     * @param integer $complexContentObjectIdentifier
     * @param integer $userIdentifier
     * @param integer $count
     * @param integer $offset
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Feedback[]
     */
    public function findFeedbackForPublicationComplexContentObjectUserIdentifiersCountAndOffset(
        int $publicationIdentifier, int $complexContentObjectIdentifier = null, int $userIdentifier = null, int $count = null,
        int $offset = null)
    {
        $parameters = new DataClassRetrievesParameters(
            $this->getFeedbackConditions($publicationIdentifier, $complexContentObjectIdentifier, $userIdentifier),
            $count,
            $offset,
            array(
                new OrderBy(
                    new PropertyConditionVariable(Feedback::class, Feedback::PROPERTY_MODIFICATION_DATE),
                    SORT_DESC)));

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
     * @param integer $publicationIdentifier
     * @param integer $complexContentObjectIdentifier
     * @param integer $userIdentifier
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    private function getFeedbackConditions(int $publicationIdentifier, int $complexContentObjectIdentifier = null,
        int $userIdentifier = null)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Feedback::class, Feedback::PROPERTY_COMPLEX_CONTENT_OBJECT_ID),
            $complexContentObjectIdentifier ? new StaticConditionVariable($complexContentObjectIdentifier) : null);
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Feedback::class, Feedback::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($publicationIdentifier));

        if ($userIdentifier)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Feedback::class, Feedback::PROPERTY_USER_ID),
                new StaticConditionVariable($userIdentifier));
        }

        return new AndCondition($conditions);
    }
}