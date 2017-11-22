<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Rights;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package Chamilo\Core\Menu\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RightsComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $this->check_allowed();

        $item_id = Request::get(self::PARAM_ITEM);
        $this->set_parameter(self::PARAM_ITEM, $item_id);
        if (! $item_id)
        {
            $location = array(Rights::getInstance()->get_root(self::package()));
        }
        else
        {
            $location = array(
                Rights::getInstance()->get_location_by_identifier(self::package(), Rights::TYPE_ITEM, $item_id));
        }

        $entities = array();
        $entities[UserEntity::ENTITY_TYPE] = new UserEntity();
        $entities[PlatformGroupEntity::ENTITY_TYPE] = new PlatformGroupEntity();

        $application = $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Rights\Editor\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        $application->set_context(self::package());
        $application->set_locations($location);
        $application->set_entities($entities);

        return $application->run();
    }

    public function get_available_rights($location)
    {
        return Rights::getInstance()->get_available_rights();
    }
}
