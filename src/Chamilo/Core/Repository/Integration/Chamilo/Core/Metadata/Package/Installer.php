<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Package;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\PropertyProvider\ContentObjectPropertyProvider;

/**
 *
 * @package Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Package
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Ehb\Core\Metadata\Action\Installer
{

    /**
     *
     * @see \Ehb\Core\Metadata\Action\Installer::getPropertyProviderTypes()
     */
    public function getPropertyProviderTypes()
    {
        return array(ContentObjectPropertyProvider :: class_name());
    }
}
