<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
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
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \ReflectionException
     */
    public function run()
    {
        $component = $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Metadata\Provider\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this)
        );
        $component->setEntities($this->getEntities());

        return $component->run();
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Entity\EntityInterface[]
     */
    public function getEntities()
    {
        $registrations = Configuration::registrations_by_type(
            'Chamilo\Core\Repository\ContentObject'
        );

        $entities = [];

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
