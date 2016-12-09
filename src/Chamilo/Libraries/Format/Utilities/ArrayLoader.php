<?php
namespace Chamilo\Libraries\Format\Utilities;

class ArrayLoader extends \Composer\Package\Loader\ArrayLoader
{

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
