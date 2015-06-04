<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Storage;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'repository_';

    public static function retrieve_complex_wiki_pages($type, $parameters = null)
    {
        $parameters = \Chamilo\Core\Repository\Storage\DataManager :: prepare_parameters(
            \Chamilo\Core\Repository\Storage\DataManager :: ACTION_RETRIEVES, 
            $type, 
            $parameters);
        $join = new Join(
            ContentObject :: class_name(), 
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID), 
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(), 
                    ComplexContentObjectItem :: PROPERTY_REF)));
        
        if ($parameters->get_joins() instanceof Joins)
        {
            $joins = $parameters->get_joins();
            
            $joins->add($join);
        }
        else
        {
            $parameters->set_joins(new Joins(array($join)));
        }
        return self :: retrieves($type, $parameters);
    }

    public static function count_complex_wiki_pages($type, $parameters = null)
    {
        $parameters = \Chamilo\Core\Repository\Storage\DataManager :: prepare_parameters(
            \Chamilo\Core\Repository\Storage\DataManager :: ACTION_COUNT, 
            $type, 
            $parameters);
        $join = new Join(
            ContentObject :: class_name(), 
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID), 
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(), 
                    ComplexContentObjectItem :: PROPERTY_REF)));
        
        if ($parameters->get_joins() instanceof Joins)
        {
            $joins = $parameters->get_joins();
            
            $joins->add($join);
        }
        else
        {
            $parameters->set_joins(new Joins(array($join)));
        }
        return self :: count($type, $parameters);
    }
}
