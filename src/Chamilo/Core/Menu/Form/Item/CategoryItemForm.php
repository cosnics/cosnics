<?php
namespace Chamilo\Core\Menu\Form\Item;

use Chamilo\Core\Menu\Form\ItemForm;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Menu\Form\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CategoryItemForm extends ItemForm
{

    public function get_parents()
    {
        $item_options = array();
        $item_options[0] = Translation::get('Root', null, Utilities::COMMON_LIBRARIES);
        
        return $item_options;
    }
}
