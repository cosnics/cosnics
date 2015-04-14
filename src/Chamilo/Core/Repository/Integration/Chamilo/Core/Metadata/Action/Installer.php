<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Action;

use Chamilo\Core\Metadata\Manager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Core\Metadata\Schema\Storage\DataClass\Schema;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Core\Metadata\Relation\Instance\Storage\DataClass\RelationInstance;
use Chamilo\Core\Metadata\Relation\Service\RelationService;

/**
 *
 * @package Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Action
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Core\Metadata\Action\Installer
{

    public function extra()
    {
        if (! parent :: extra())
        {
            return false;
        }

        if (! $this->linkToSchemas())
        {
            return $this->failed(Translation :: get('ContentObjectSchemaLinkFailed', null, Manager :: package()));
        }

        return true;
    }

    public function getContentObjectType()
    {
        $namespace = static :: context();
        return ClassnameUtilities :: getInstance()->getNamespaceParent($namespace, 5);
    }

    /**
     *
     * @return boolean
     */
    public function linkToSchemas()
    {
        $schemaNamespaces = array('dc', 'ct');
        DataClassCache :: truncate(Schema :: class_name());

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                TemplateRegistration :: class_name(),
                TemplateRegistration :: PROPERTY_CONTENT_OBJECT_TYPE),
            new StaticConditionVariable($this->getContentObjectType()));

        $templateRegistrations = DataManager :: retrieves(
            TemplateRegistration :: class_name(),
            new DataClassRetrievesParameters($condition))->as_array();

        $relationService = new RelationService();
        $relation = $relationService->getRelationByName('isAvailableFor');

        foreach ($schemaNamespaces as $schemaNamespace)
        {
            $schema = \Chamilo\Core\Metadata\Schema\Storage\DataManager :: retrieveSchemaByNamespace($schemaNamespace);

            foreach ($templateRegistrations as $templateRegistration)
            {
                $relationInstance = new RelationInstance();
                $relationInstance->set_source_type(Schema :: class_name());
                $relationInstance->set_source_id($schema->get_id());
                $relationInstance->set_target_type(TemplateRegistration :: class_name());
                $relationInstance->set_target_id($templateRegistration->get_id());
                $relationInstance->set_relation_id($relation->get_id());
                $relationInstance->set_user_id(0);
                $relationInstance->set_creation_date(time());

                if (! $relationInstance->create())
                {
                    return false;
                }
            }
        }

        return true;
    }
}