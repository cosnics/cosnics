<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Service;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Core\Metadata\Service\EntityService;

/**
 *
 * @package Ehb\Core\Metadata\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RepositoryEntityService extends EntityService
{
    const PROPERTY_METADATA_SCHEMA = 'metadata_schema';

    /**
     *
     * @see \Ehb\Core\Metadata\Service\EntityService::getAvailableSchemaIdsForEntity()
     */
    public function getAvailableSchemaIdsForEntity(RelationService $relationService, ContentObject $contentObject)
    {
        return parent :: getAvailableSchemaIdsForEntity($relationService, $contentObject->get_template_registration());
    }
}
