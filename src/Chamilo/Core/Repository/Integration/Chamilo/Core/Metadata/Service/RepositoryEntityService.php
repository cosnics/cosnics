<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Service;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Ehb\Core\Metadata\Relation\Service\RelationService;
use Ehb\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance;
use Ehb\Core\Metadata\Schema\Storage\DataClass\Schema;
use Ehb\Core\Metadata\Service\EntityService;

/**
 *
 * @package Ehb\Core\Metadata\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RepositoryEntityService
{
    const PROPERTY_METADATA_SCHEMA = 'metadata_schema';

    /**
     *
     * @param \Ehb\Core\Metadata\Relation\Service\RelationService $relationService
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     * @return integer[]
     */
    public function getSchemaInstancesForContentObject(EntityService $entityService, RelationService $relationService,
        ContentObject $contentObject)
    {
        $schemaIds = $entityService->getSourceRelationIdsForEntity(
            Schema :: class_name(),
            $relationService->getRelationByName('isAvailableFor'),
            $contentObject->get_template_registration());

        $conditions = array();
        $conditions[] = new InCondition(
            new PropertyConditionVariable(SchemaInstance :: class_name(), SchemaInstance :: PROPERTY_SCHEMA_ID),
            $schemaIds);
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(SchemaInstance :: class_name(), SchemaInstance :: PROPERTY_ENTITY_TYPE),
            ComparisonCondition :: EQUAL,
            new StaticConditionVariable(ContentObject :: class_name()));
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(SchemaInstance :: class_name(), SchemaInstance :: PROPERTY_ENTITY_ID),
            ComparisonCondition :: EQUAL,
            new StaticConditionVariable($contentObject->get_id()));

        return DataManager :: retrieves(
            SchemaInstance :: class_name(),
            new DataClassRetrievesParameters(new AndCondition($conditions)));
    }
}
