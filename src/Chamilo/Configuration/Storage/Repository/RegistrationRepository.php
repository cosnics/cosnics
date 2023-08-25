<?php
namespace Chamilo\Configuration\Storage\Repository;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Configuration\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class RegistrationRepository
{

    private DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    public function clearRegistrationCache(): bool
    {
        return $this->getDataClassRepository()->getDataClassRepositoryCache()->truncate(Registration::class);
    }

    public function createRegistration(Registration $registration): bool
    {
        return $this->getDataClassRepository()->create($registration);
    }

    public function deleteRegistration(Registration $registration): bool
    {
        return $this->getDataClassRepository()->delete($registration);
    }

    public function findRegistrationForContext(string $context): ?Registration
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Registration::class, Registration::PROPERTY_CONTEXT),
            new StaticConditionVariable($context)
        );

        return $this->getDataClassRepository()->retrieve(
            Registration::class, new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<string[]>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRegistrationsAsRecords(): ArrayCollection
    {
        return $this->getDataClassRepository()->records(
            Registration::class, new RecordRetrievesParameters(
                new RetrieveProperties([new PropertiesConditionVariable(Registration::class)])
            )
        );
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    public function updateRegistration(Registration $registration): bool
    {
        return $this->getDataClassRepository()->update($registration);
    }
}