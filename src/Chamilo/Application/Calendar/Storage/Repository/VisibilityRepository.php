<?php
namespace Chamilo\Application\Calendar\Storage\Repository;

use Chamilo\Application\Calendar\Storage\DataClass\Visibility;
use Chamilo\Libraries\Calendar\Event\Visibility as GenericVisibility;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\StorageParameters;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Application\Calendar\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class VisibilityRepository
{

    private DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     */
    public function createVisibility(Visibility $visibility): bool
    {
        return $this->getDataClassRepository()->create($visibility);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteVisibility(Visibility $visibility): bool
    {
        return $this->getDataClassRepository()->delete($visibility);
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function retrieveVisibilityForUserIdentifierAndSource(string $userIdentifier, string $source): ?Visibility
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Visibility::class, GenericVisibility::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Visibility::class, GenericVisibility::PROPERTY_SOURCE),
            new StaticConditionVariable($source)
        );
        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieve(
            Visibility::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Application\Calendar\Storage\DataClass\Visibility>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveVisibilitiesByUserIdentifier(string $userIdentifier): ArrayCollection
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Visibility::class, GenericVisibility::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );
        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieves(
            Visibility::class, new StorageParameters(condition: $condition)
        );
    }
}