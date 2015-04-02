<?php
namespace Chamilo\Core\Metadata\Ajax\Component;

use Chamilo\Core\Metadata\Attribute\Storage\DataClass\Attribute;
use Chamilo\Core\Metadata\Storage\DataManager;

/**
 *
 * @package Chamilo\Core\Metadata\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class GetAttributesComponent extends \Chamilo\Core\Metadata\Ajax\Manager
{

    public function run()
    {
        $attributes = DataManager :: retrieves(Attribute :: class_name());

        $types = array();

        while ($attribute = $attributes->next_result())
        {
            $types[$attribute->get_id()] = $attribute->render_name();
        }

        echo json_encode($types);
    }
}