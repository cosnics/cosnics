<?php
namespace Chamilo\Core\Repository\ContentObject\Link\Integration\Chamilo\Core\Metadata\PropertyProvider;

use Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass\Link;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Link\Integration\Chamilo\Core\Metadata\PropertyProvider
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ContentObjectPropertyProvider extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\PropertyProvider\ContentObjectPropertyProvider
{
    /**
     *
     * @see \Chamilo\Core\Metadata\Provider\PropertyProviderInterface::getEntityType()
     */
    public function getEntityType()
    {
        return Link :: class_name();
    }
}