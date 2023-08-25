<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\Repository\RegistrationRepository;
use Chamilo\Libraries\Cache\Traits\CacheAdapterHandlerTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Configuration\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RegistrationService
{
    use CacheAdapterHandlerTrait;

    protected AdapterInterface $registrationCacheAdapter;

    protected RegistrationRepository $registrationRepository;

    public function __construct(
        RegistrationRepository $registrationRepository, AdapterInterface $registrationCacheAdapter
    )
    {
        $this->registrationRepository = $registrationRepository;
        $this->registrationCacheAdapter = $registrationCacheAdapter;
    }

    public function activateRegistrationForContext(string $context): bool
    {
        $registration = $this->findRegistrationForContext($context);

        if (!$registration instanceof Registration)
        {
            return false;
        }

        $registration->set_status(Registration::STATUS_ACTIVE);

        return $this->updateRegistration($registration);
    }

    public function createRegistration(Registration $registration): bool
    {
        if (!$this->getRegistrationRepository()->createRegistration($registration))
        {
            return false;
        }

        return $this->clearAllCacheDataForAdapter($this->getRegistrationCacheAdapter());
    }

    public function createRegistrationFromParameters(
        string $context, string $type, string $category, string $name, string $version, int $status
    ): bool
    {
        $registration = new Registration();

        $registration->set_context($context);
        $registration->setType(($type));
        $registration->set_category($category);
        $registration->set_name($name);
        $registration->set_version($version);
        $registration->set_status($status);

        return $this->createRegistration($registration);
    }

    public function deactivateRegistrationForContext(string $context): bool
    {
        $registration = $this->findRegistrationForContext($context);

        if (!$registration instanceof Registration)
        {
            return false;
        }

        $registration->set_status(Registration::STATUS_INACTIVE);

        return $this->updateRegistration($registration);
    }

    public function deleteRegistration(Registration $registration): bool
    {
        if (!$this->getRegistrationRepository()->deleteRegistration($registration))
        {
            return false;
        }

        return $this->clearAllCacheDataForAdapter($this->getRegistrationCacheAdapter());
    }

    public function deleteRegistrationForContext(string $context): bool
    {
        $registration = $this->findRegistrationForContext($context);

        if (!$registration instanceof Registration)
        {
            return false;
        }

        return $this->deleteRegistration($registration);
    }

    public function findRegistrationForContext(string $context): ?Registration
    {
        return $this->getRegistrationRepository()->findRegistrationForContext($context);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRegistrationsAsRecords(): ArrayCollection
    {
        return $this->getRegistrationRepository()->findRegistrationsAsRecords();
    }

    public function getRegistrationCacheAdapter(): AdapterInterface
    {
        return $this->registrationCacheAdapter;
    }

    public function getRegistrationRepository(): RegistrationRepository
    {
        return $this->registrationRepository;
    }

    public function updateRegistration(Registration $registration): bool
    {
        if (!$this->getRegistrationRepository()->updateRegistration($registration))
        {
            return false;
        }

        return $this->clearAllCacheDataForAdapter($this->getRegistrationCacheAdapter());
    }
}