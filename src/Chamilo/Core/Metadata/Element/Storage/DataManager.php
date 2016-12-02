<?php
namespace Chamilo\Core\Metadata\Element\Storage;

use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'metadata_';

    public static function get_display_order_total_for_schema($schema_id)
    {
        $condition = new ComparisonCondition(
            new PropertyConditionVariable(Element::class_name(), Element::PROPERTY_SCHEMA_ID), 
            ComparisonCondition::EQUAL, 
            new StaticConditionVariable($schema_id));
        
        return DataManager::count(Element::class_name(), $condition);
    }
}