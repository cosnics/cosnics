<?php
namespace Chamilo\Core\Repository\ContentObject\GlossaryItem\Integration\Chamilo\Core\Metadata\PropertyProvider;

use Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass\GlossaryItem;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\GlossaryItem\Integration\Chamilo\Core\Metadata\PropertyProvider
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
        return GlossaryItem::class_name();
    }
}