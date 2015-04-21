<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\ApplicationSupport;
use Chamilo\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Core\Metadata\Storage\DataClass\Relation;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Metadata\Entity\EntityFactory;

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
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Metadata\Relation\Instance\Manager :: context(),
            $this->get_user(),
            $this);

        $component = $factory->getComponent();
        $component->setTargetEntities($this->getTargetEntities());
        $component->setRelations($this->getRelation());

        return $component->run();
    }

    public function getTargetEntities()
    {
        $registrations = \Chamilo\Configuration\Configuration :: get_instance()->get_registrations_by_type(
            'Chamilo\Core\Repository\ContentObject');

        $entities = array();
        $entityFactory = EntityFactory :: getInstance();

        foreach ($registrations as $registration)
        {
            if ($registration->get_name() == 'File')

                $entities[] = $entityFactory->getEntity(
                    $registration->get_context() . '\Storage\DataClass\\' . $registration->get_name());
        }

        return $entities;
    }

    public function getRelation()
    {
        $relationService = new RelationService();
        $relation = $relationService->getRelationByName('isAvailableFor');

        if (! $relation instanceof Relation)
        {
            throw new \Exception(
                Translation :: get(
                    'RelationNotAvailable',
                    array('TYPE' => 'isAvailableFor'),
                    'Chamilo\Core\Metadata\Relation'));
        }

        return array($relation);
    }
}
