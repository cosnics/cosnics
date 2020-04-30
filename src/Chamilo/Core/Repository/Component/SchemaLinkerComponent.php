<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Metadata\Entity\DataClassEntity;
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
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \ReflectionException
     */
    public function run()
    {
        $component = $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Metadata\Relation\Instance\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this)
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
        $relation = $this->getRelationService()->getRelationByName('isAvailableFor');

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
     * @return \Chamilo\Core\Metadata\Relation\Service\RelationService
     */
    private function getRelationService()
    {
        return $this->getService(RelationService::class);
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Entity\EntityInterface[]
     */
    public function getSourceEntities()
    {
        $entities = array();
        $entities[] = $this->getDataClassEntityFactory()->getEntity(Schema::class);

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

        foreach ($registrations as $registration)
        {
            $entities[] = $this->getDataClassEntityFactory()->getEntity(
                $registration[Registration::PROPERTY_CONTEXT] . '\Storage\DataClass\\' .
                $registration[Registration::PROPERTY_NAME], DataClassEntity::INSTANCE_IDENTIFIER
            );
        }

        return $entities;
    }
}
