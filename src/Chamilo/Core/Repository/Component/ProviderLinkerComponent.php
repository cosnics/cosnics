<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\ApplicationSupport;
use Chamilo\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Core\Metadata\Storage\DataClass\Relation;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;

/**
 *
 * @package Chamilo\Core\Repository\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ProviderLinkerComponent extends Manager implements ApplicationSupport
{

    /**
     * Executes this component
     */
    public function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Metadata\Provider\Manager :: context(),
            $this->get_user(),
            $this);

        $component = $factory->getComponent();
        $component->setEntities($this->getEntities());

        return $component->run();
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Entity\EntityInterface[]
     */
    public function getEntities()
    {
        $registrations = \Chamilo\Configuration\Configuration :: get_instance()->get_registrations_by_type(
            'Chamilo\Core\Repository\ContentObject');

        $entities = array();
        $entityFactory = DataClassEntityFactory :: getInstance();

        foreach ($registrations as $registration)
        {
            $entities[] = $entityFactory->getEntity(
                $registration->get_context() . '\Storage\DataClass\\' . $registration->get_name(),
                DataClassEntity :: INSTANCE_IDENTIFIER);
        }

        return $entities;
    }
}
