<?php
namespace Chamilo\Core\Install\DependencyInjection;

/**
 *
 * @package Chamilo\Core\Install\DependencyInjection
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DependencyInjectionContainerBuilder extends \Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder
{

    /**
     *
     * @return string[]
     */
    protected function getPackageNamespaces()
    {
        return $this->getPackageNamespacesFromFilesystem();
    }
}