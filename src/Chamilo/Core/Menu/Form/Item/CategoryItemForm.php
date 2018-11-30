<?php
namespace Chamilo\Core\Menu\Form\Item;

use Chamilo\Core\Menu\Form\ItemForm;

/**
 *
 * @package Chamilo\Core\Menu\Form\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CategoryItemForm extends ItemForm
{

    /**
     * @return string[]
     */
    public function getParentItems()
    {
        return $this->getRootParentItem();
    }
}
