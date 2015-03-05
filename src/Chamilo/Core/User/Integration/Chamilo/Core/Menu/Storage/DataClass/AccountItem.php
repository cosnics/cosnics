<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Format\Structure\Header;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AccountItem extends Item
{

    /**
     *
     * @return string
     */
    public function get_section()
    {
        return 'my_account';
    }

    public function is_selected()
    {
        $current_section = Header :: get_instance()->get_section();
        if ($current_section == $this->get_section())
        {
            return true;
        }
        return false;
    }
}
