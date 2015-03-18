<?php
namespace Chamilo\Core\Metadata\Ajax\Component;

use Chamilo\Core\Metadata\Storage\DataManager;
use Chamilo\Core\Metadata\Value\Element\Storage\DataClass\DefaultElementValue;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Metadata\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class GetMetadataDefaultsComponent extends \Chamilo\Core\Metadata\Ajax\Manager
{

    public function run()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                DefaultElementValue :: class_name(),
                DefaultElementValue :: PROPERTY_ELEMENT_ID),
            new StaticConditionVariable(Request :: post('element_id')));

        $default_values = DataManager :: retrieves(DefaultElementValue :: class_name(), $condition);

        $i = 0;
        $defaults = array();
        while ($default_value = $default_values->next_result())
        {
            $defaults[] = $default_value->get_value();
            $i ++;
        }

        echo json_encode($defaults);
    }
}