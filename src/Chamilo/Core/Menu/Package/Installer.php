<?php
namespace Chamilo\Core\Menu\Package;

use Chamilo\Core\Menu\Rights;
use Chamilo\Core\Menu\Storage\DataClass\RightsLocation;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Menu\Package
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    /**
     * Runs the install-script.
     */
    public function extra()
    {
        $rights_utilities = Rights :: get_instance();
        $location = $rights_utilities->create_subtree_root_location(
            static :: package(), 
            0, 
            Rights :: TREE_TYPE_ROOT, 
            true);
        
        if (! $location instanceof RightsLocation)
        {
            return false;
        }
        else
        {
            $this->add_message(
                self :: TYPE_NORMAL, 
                Translation :: get(
                    'ObjectCreated', 
                    array('OBJECT' => Translation :: get('RightsTree')), 
                    Utilities :: COMMON_LIBRARIES));
        }
        
        return true;
    }
}
