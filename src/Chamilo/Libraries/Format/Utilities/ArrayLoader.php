<?php
namespace Chamilo\Libraries\Format\Utilities;

use Composer\Package\BasePackage;

/**
 * @package Chamilo\Libraries\Format\Utilities
 */
class ArrayLoader extends \Composer\Package\Loader\ArrayLoader
{

    public function load(array $config, string $class = 'Composer\Package\CompletePackage'): BasePackage
    {
        $package = parent::load($config, $class);

        if (isset($config['repositories']))
        {
            $package->setRepositories($config['repositories']);
        }

        return $package;
    }
}
