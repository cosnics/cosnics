<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Core\Metadata\Storage\DataClass\Relation;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\ApplicationSupport;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 *
 * @package Chamilo\Core\Repository\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SchemaLinkerComponent extends Manager implements ApplicationSupport
{

    /**
     * Executes this component
     */
    public function run()
    {
        $component = $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Metadata\Relation\Instance\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        );
        $component->setTargetEntities($this->getTargetEntities());
        $component->setRelations($this->getRelation());
        $component->setSourceEntities($this->getSourceEntities());

        return $component->run();
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Relation[]
     * @throws \Exception
     */
    public function getRelation()
    {
        $relation = $this->getService(RelationService::class)->getRelationByName('isAvailableFor');

        if (!$relation instanceof Relation)
        {
            throw new Exception(
                Translation::get(
                    'RelationNotAvailable', array('TYPE' => 'isAvailableFor'), 'Chamilo\Core\Metadata\Relation'
                )
            );
        }

        return array($relation);
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Entity\EntityInterface[]
     */
    public function getSourceEntities()
    {
        $entities = array();
        $entities[] = $this->getService(DataClassEntityFactory::class)->getEntity(Schema::class_name());

        return $entities;
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Entity\EntityInterface[]
     */
    public function getTargetEntities()
    {
        $registrations = Configuration::registrations_by_type(
            'Chamilo\Core\Repository\ContentObject'
        );

        $entities = array();
        $entityFactory = $this->getService(DataClassEntityFactory::class);

        foreach ($registrations as $registration)
        {
            $entities[] = $entityFactory->getEntity(
                $registration[Registration::PROPERTY_CONTEXT] . '\Storage\DataClass\\' .
                $registration[Registration::PROPERTY_NAME], DataClassEntity::INSTANCE_IDENTIFIER
            );
        }

        return $entities;
    }
}
