<?php

namespace Chamilo\Libraries\Console\Command\Vendor\PHPStan;

/**
 * Class PHPStanPackages
 *
 * @package Chamilo\Libraries\Console\Command\Vendor\PHPStan
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class PHPStanPackages
{
    /**
     * @var \Chamilo\Libraries\Console\Command\Vendor\PHPStan\PHPStanPackage[]
     */
    protected $packages;

    /**
     * @param array $packagesConfiguration
     */
    public function setPackagesFromConfiguration(array $packagesConfiguration = array())
    {
        foreach ($packagesConfiguration as $packageName => $packageConfiguration)
        {
            $this->packages[$packageName] =
                new PHPStanPackage($packageName, $packageConfiguration['level'], $packageConfiguration['paths']);
        }
    }

    /**
     * @return \Chamilo\Libraries\Console\Command\Vendor\PHPStan\PHPStanPackage[]
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * @return string[]
     */
    public function getPackageNames()
    {
        return array_keys($this->packages);
    }

    /**
     * @param $packageName
     *
     * @return \Chamilo\Libraries\Console\Command\Vendor\PHPStan\PHPStanPackage
     */
    public function getPackage($packageName)
    {
        if (!array_key_exists($packageName, $this->packages))
        {
            throw new \InvalidArgumentException(
                sprintf('The given package name %s could not be found in the list of packages', $packageName)
            );
        }

        return $this->packages[$packageName];
    }
}