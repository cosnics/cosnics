<?php
namespace Chamilo\Core\Repository\Instance\Component;

use Chamilo\Core\Repository\Instance\Manager;
use Chamilo\Core\Repository\Instance\Rights;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * Repository manager component to edit the rights for the objects in the repository.
 */
class RightsComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $identifiers = Request :: get(self :: PARAM_INSTANCE_ID);
        $this->set_parameter(self :: PARAM_INSTANCE_ID, $identifiers);

        $locations = array();

        if (! $identifiers)
        {
            $locations[] = Rights :: getInstance()->get_external_instances_subtree_root();
        }

        if ($identifiers && ! is_array($identifiers))
        {
            $identifiers = array($identifiers);
        }

        foreach ($identifiers as $identifier)
        {
            $locations[] = Rights :: getInstance()->get_location_by_identifier_from_external_instances_subtree(
                $identifier);
        }

        $entities = array();
        $entities[UserEntity :: ENTITY_TYPE] = new UserEntity();
        $entities[PlatformGroupEntity :: ENTITY_TYPE] = new PlatformGroupEntity();

        $factory = new ApplicationFactory(
            \Chamilo\Core\Rights\Editor\Manager :: context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        $component = $factory->getComponent();
        $component->set_locations($locations);
        $component->set_entities($entities);
        return $component->run();
    }

    public function get_available_rights()
    {
        return Rights :: getInstance()->get_available_rights_for_external_instances_substree();
    }
}
