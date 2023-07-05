<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Package\Properties\Dependencies\Dependencies;
use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency;
use Chamilo\Configuration\Package\Service\PackageFactory;

/**
 * @package Chamilo\Configuration\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PackageContextSequencer
{

    private PackageFactory $packageFactory;

    public function __construct(PackageFactory $packageFactory)
    {
        $this->packageFactory = $packageFactory;
    }

    /**
     * @param string[] $packageContexts
     * @param string $packageContext
     *
     * @throws \Exception
     */
    public function checkAdditionalPackageContexts(array &$packageContexts, string $packageContext): void
    {
        $package = $this->getPackageFactory()->getPackage($packageContext);

        foreach ($package->getAdditional() as $additionalPackageContext)
        {
            if (!in_array($additionalPackageContext, $packageContexts))
            {
                $packageContexts[] = $additionalPackageContext;
                $this->checkAdditionalPackageContexts($packageContexts, $additionalPackageContext);
                $this->checkPackageContextDependencies($packageContexts, $additionalPackageContext);
                $this->checkPackageContextIntegrations($packageContexts, $additionalPackageContext);
            }
        }
    }

    /**
     * @param string[] $packageContexts
     * @param string $packageContext
     *
     * @throws \Exception
     */
    protected function checkPackageContextDependencies(array &$packageContexts, string $packageContext): void
    {
        $package = $this->getPackageFactory()->getPackage($packageContext);
        $this->processDependencies($packageContexts, $package->get_dependencies());
    }

    /**
     * @param string[] $packageContexts
     * @param string $packageTargetContext
     *
     * @throws \Exception
     */
    protected function checkPackageContextIntegrations(array &$packageContexts, string $packageTargetContext): void
    {
        foreach ($packageContexts as $packageSourceContext)
        {
            $integrationPackageContext = $packageSourceContext . '\Integration\\' . $packageTargetContext;

            if ($this->getPackageFactory()->packageExists($integrationPackageContext))
            {
                if (!in_array($integrationPackageContext, $packageContexts))
                {
                    $packageContexts[] = $integrationPackageContext;
                    $this->checkPackageContextIntegrations($packageContexts, $integrationPackageContext);
                    $this->checkAdditionalPackageContexts($packageContexts, $integrationPackageContext);
                    $this->checkPackageContextDependencies($packageContexts, $integrationPackageContext);
                }
            }
        }
    }

    /**
     * @param string[] $packageContexts
     *
     * @throws \Exception
     */
    protected function expandPackageContexts(array &$packageContexts): void
    {
        foreach ($packageContexts as $packageContext)
        {
            $this->checkAdditionalPackageContexts($packageContexts, $packageContext);
            $this->checkPackageContextDependencies($packageContexts, $packageContext);
            $this->checkPackageContextIntegrations($packageContexts, $packageContext);
        }
    }

    /**
     * @return \Chamilo\Configuration\Package\Service\PackageFactory
     */
    public function getPackageFactory(): PackageFactory
    {
        return $this->packageFactory;
    }

    /**
     * @param string[] $packageContexts
     * @param \Chamilo\Configuration\Package\Properties\Dependencies\Dependencies|null $dependencies
     *
     * @throws \Exception
     */
    public function processDependencies(array &$packageContexts, Dependencies $dependencies = null): void
    {
        if ($dependencies instanceof Dependencies)
        {
            foreach ($dependencies->getDependencies() as $dependency)
            {
                if (!in_array($dependency->get_id(), $packageContexts))
                {
                    $packageContexts[] = $dependency->get_id();
                    $this->checkAdditionalPackageContexts($packageContexts, $dependency->get_id());
                    $this->checkPackageContextDependencies($packageContexts, $dependency->get_id());
                    $this->checkPackageContextIntegrations($packageContexts, $dependency->get_id());
                }
            }
        }
    }

    /**
     * @param string[] $packageContexts
     *
     * @return string[]
     * @throws \Exception
     */
    public function sequencePackageContexts(array $packageContexts): array
    {
        $this->expandPackageContexts($packageContexts);

        $sequence = [];

        while ($unprocessed_package_context = array_shift($packageContexts))
        {
            $package = $this->getPackageFactory()->getPackage($unprocessed_package_context);

            if ($this->verifyDependency($sequence, $package->get_dependencies()))
            {
                $sequence[] = $unprocessed_package_context;
            }
            else
            {
                $packageContexts[] = $unprocessed_package_context;
            }
        }

        return $sequence;
    }

    /**
     * @param \Chamilo\Configuration\Package\Service\PackageFactory $packageFactory
     *
     * @return PackageContextSequencer
     */
    public function setPackageFactory(PackageFactory $packageFactory): PackageContextSequencer
    {
        $this->packageFactory = $packageFactory;

        return $this;
    }

    /**
     * @param string[] $sequence
     * @param Dependencies|Dependency $dependency
     *
     * @return bool
     */
    public function verifyDependency(array &$sequence, Dependencies|Dependency $dependency): bool
    {
        if ($dependency instanceof Dependencies)
        {
            $result = true;

            foreach ($dependency->getDependencies() as $sub_dependency)
            {
                $result = $result && $this->verifyDependency($sequence, $sub_dependency);
            }

            return $result;
        }
        elseif (!in_array($dependency->get_id(), $sequence))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}