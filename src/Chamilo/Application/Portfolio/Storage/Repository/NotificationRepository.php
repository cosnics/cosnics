<?php
namespace Chamilo\Application\Portfolio\Storage\Repository;

use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Application\Portfolio\Storage\DataClass\Notification;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;

/**
 *
 * @package Chamilo\Application\Portfolio\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NotificationRepository
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
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Notification[]
     */
    public function findPortfolioNotificationsForPublicationIdentifierAndComplexContentObjectIdentifier(
        $publicationIdentifier, $complexContentObjectIdentifier)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Notification::class, Notification::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($publicationIdentifier));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Notification::class, Notification::PROPERTY_COMPLEX_CONTENT_OBJECT_ID),
            new StaticConditionVariable($complexContentObjectIdentifier));

        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieves(
            Notification::class,
            new DataClassRetrievesParameters($condition));
    }

    /**
     *
     * @param integer $publicationIdentifier
     * @param integer $complexContentObjectIdentifier
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Notification[]
     */
    public function findPortfolioNotificationForPublicationIdentifierUserIdentifierAndComplexContentObjectIdentifier(
        $publicationIdentifier, $userIdentifier, $complexContentObjectIdentifier)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Notification::class, Notification::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($publicationIdentifier));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Notification::class, Notification::PROPERTY_COMPLEX_CONTENT_OBJECT_ID),
            new StaticConditionVariable($complexContentObjectIdentifier));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Notification::class, Notification::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier));

        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieve(
            Notification::class,
            new DataClassRetrieveParameters($condition));
    }
}

