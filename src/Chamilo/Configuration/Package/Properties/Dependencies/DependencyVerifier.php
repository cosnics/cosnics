<?php
namespace Chamilo\Configuration\Package\Properties\Dependencies;

use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Composer\Semver\Semver;

/**
 * @package Chamilo\Configuration\Package\Properties\Dependencies
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyVerifier
{

    protected PackageFactory $packageFactory;

    protected RegistrationConsulter $registrationConsulter;

    public function __construct(PackageFactory $packageFactory, RegistrationConsulter $registrationConsulter)
    {
        $this->packageFactory = $packageFactory;
        $this->registrationConsulter = $registrationConsulter;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function dependencyHasActiveRegistration(Dependency $dependency): bool
    {
        $registration = $this->getDependencyRegistration($dependency);

        if (!$registration[Registration::PROPERTY_STATUS])
        {
            return false;
        }

        return true;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function dependencyHasRegistration(Dependency $dependency): bool
    {
        $registration = $this->getDependencyRegistration($dependency);

        if (empty($registration))
        {
            return false;
        }

        return true;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function dependencyHasValidRegisteredVersion(Dependency $dependency): bool
    {
        $registration = $this->getDependencyRegistration($dependency);

        if (!Semver::satisfies(
            $registration[Registration::PROPERTY_VERSION], $dependency->get_version()
        ))
        {
            return false;
        }

        return true;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    protected function getDependencyRegistration(Dependency $dependency): array
    {
        return $this->getRegistrationConsulter()->getRegistrationForContext($dependency->get_id());
    }

    public function getPackageFactory(): PackageFactory
    {
        return $this->packageFactory;
    }

    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function isInstallable(Package $package): bool
    {
        $dependencies = $package->get_dependencies();

        if ($dependencies instanceof Dependencies)
        {
            foreach ($dependencies as $dependency)
            {
                if (!$this->verifyDependency($dependency))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Exception
     */
    public function isNeededByAnotherRegisteredPackage(Package $package): bool
    {
        $registrations = $this->getRegistrationConsulter()->getRegistrationsMappedByContext();
        unset($registrations[$package->get_context()]);

        foreach ($registrations as $registration)
        {
            $registrationPackage =
                $this->getPackageFactory()->getPackage($registration[Registration::PROPERTY_CONTEXT]);

            $dependencies = $registrationPackage->get_dependencies();

            if (!is_null($dependencies))
            {
                foreach ($dependencies->getDependencies() as $dependency)
                {
                    if ($dependency->get_id() == $package->get_context())
                    {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function isRemovable(Package $package): bool
    {
        return !$this->isNeededByAnotherRegisteredPackage($package);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function verifyDependency(Dependency $dependency): bool
    {
        if (!$this->dependencyHasRegistration($dependency) || !$this->dependencyHasActiveRegistration($dependency) ||
            !$this->dependencyHasValidRegisteredVersion($dependency))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}
