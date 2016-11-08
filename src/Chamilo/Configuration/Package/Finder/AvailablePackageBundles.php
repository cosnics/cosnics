<?php
namespace Chamilo\Configuration\Package\Finder;

/**
 *
 * @package Chamilo\Configuration\Package\Finder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AvailablePackageBundles extends PackageBundles
{

    /**
     *
     * @return boolean
     */
    protected function isRelevantPackage($packageNamespace)
    {
        return ! \Chamilo\Configuration\Configuration::is_registered($packageNamespace);
    }
}
