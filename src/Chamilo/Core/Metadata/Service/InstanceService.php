<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Core\Metadata\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class InstanceService
{
    const PROPERTY_METADATA_ADD_SCHEMA = 'add_schema';

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     * @param integer[] $selectedSchemaIds
     *
     * @return string
     * @throws \ReflectionException
     */
    public function updateInstances(User $user, DataClass $entity, $selectedSchemaIds)
    {
        if (count($selectedSchemaIds) == 0)
        {
            return false;
        }

        foreach ($selectedSchemaIds as $selectedSchemaId)
        {
            $schemaInstance = new SchemaInstance();
            $schemaInstance->set_entity_type($entity->class_name());
            $schemaInstance->set_entity_id($entity->getId());
            $schemaInstance->set_schema_id($selectedSchemaId);
            $schemaInstance->set_user_id($user->getId());
            $schemaInstance->set_creation_date(time());

            $schemaInstance->create();
        }

        return 'schema-' . $schemaInstance->getId();
    }
}
