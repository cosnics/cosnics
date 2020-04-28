<?php
namespace Chamilo\Configuration\Package\Finder;

use Chamilo\Configuration\Configuration;

/**
 *
 * @package Chamilo\Configuration\Package\Finder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class InstalledPackageBundles extends PackageBundles
{

    /**
     *
     * @return boolean
     */
    protected function isRelevantPackage($packageNamespace)
    {
        return Configuration::is_registered($packageNamespace);
    }
}
