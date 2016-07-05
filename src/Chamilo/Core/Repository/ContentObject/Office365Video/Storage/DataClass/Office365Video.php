<?php
namespace Chamilo\Core\Repository\ContentObject\Office365Video\Storage\DataClass;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Includeable;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class Office365Video extends ContentObject implements Includeable
{
    public static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: class_name(), true);
    }

    public function getVideoEmbedCode()
    {
        $external_object = $this->get_synchronization_data()->get_external_object();
        if (is_null($external_object))
        {
            return 	'<div class="warning-message">' . Translation :: get('NoExternalObject') .  '</div>';
        }
        else
        {
            return $external_object->getVideoEmbedCode();
        }
    }

    public static function is_type_available()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_IMPLEMENTATION), 
            new StaticConditionVariable('Chamilo\Core\Repository\Implementation\Office365Video'));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_ENABLED), 
            new StaticConditionVariable(1));
        $condition = new AndCondition($conditions);
        
        $external_repositories = \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieves(
            Instance :: class_name(), 
            new DataClassRetrievesParameters($condition));
        return $external_repositories->size() == 1;
    }
}
