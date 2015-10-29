<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Format\Structure\Page;

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
        return \Chamilo\Core\User\Manager :: SECTION_MY_ACCOUNT;
    }

    /**
     *
     * @see \Chamilo\Core\Menu\Storage\DataClass\Item::is_selected()
     */
    public function is_selected()
    {
        $current_section = Page :: getInstance()->getSection();
        if ($current_section == $this->get_section())
        {
            return true;
        }
        return false;
    }
}
