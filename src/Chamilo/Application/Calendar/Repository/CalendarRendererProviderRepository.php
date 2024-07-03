<?php
namespace Chamilo\Application\Calendar\Repository;

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
 * @package Chamilo\Application\Calendar\Repository$CalendarRendererProviderRepository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarRendererProviderRepository
{
    private DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    public function findVisibilitiesByUserIdentifier(string $userIdentifier): ArrayCollection
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

    public function findVisibilityBySourceAndUserIdentifier(string $source, string $userIdentifier): ?Visibility
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

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }
}