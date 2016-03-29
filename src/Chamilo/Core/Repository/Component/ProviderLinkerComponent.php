<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\ApplicationSupport;

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
            \Chamilo\Core\Metadata\Provider\Manager :: context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));

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
        $registrations = \Chamilo\Configuration\Configuration :: registrations_by_type(
            'Chamilo\Core\Repository\ContentObject');

        $entities = array();
        $entityFactory = DataClassEntityFactory :: getInstance();

        foreach ($registrations as $registration)
        {
            $entities[] = $entityFactory->getEntity(
                $registration[Registration :: PROPERTY_CONTEXT] . '\Storage\DataClass\\' .
                     $registration[Registration :: PROPERTY_NAME],
                    DataClassEntity :: INSTANCE_IDENTIFIER);
        }

        return $entities;
    }
}
