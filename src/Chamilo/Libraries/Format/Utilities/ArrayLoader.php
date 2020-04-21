<?php
namespace Chamilo\Libraries\Format\Utilities;

/**
 *
 * @package Chamilo\Libraries\Format\Utilities
 */
class ArrayLoader extends \Composer\Package\Loader\ArrayLoader
{

    /**
     * @param array $config
     * @param string $class
     *
     * @return \Composer\Package\AliasPackage|\Composer\Package\CompletePackageInterface|\Composer\Package\PackageInterface|\Composer\Package\RootAliasPackage|mixed
     */
    public function load(array $config, $class = 'Composer\Package\CompletePackage')
    {
        $package = parent::load($config, $class);

        if (isset($config['repositories']))
        {
            $package->setRepositories($config['repositories']);
        }

        return $package;
    }
}
