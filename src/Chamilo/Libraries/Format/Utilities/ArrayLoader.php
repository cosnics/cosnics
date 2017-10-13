<?php
namespace Chamilo\Libraries\Format\Utilities;

/**
 *
 * @package Chamilo\Libraries\Format\Utilities
 */
class ArrayLoader extends \Composer\Package\Loader\ArrayLoader
{

    /**
     *
     * @see \Composer\Package\Loader\ArrayLoader::load()
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
