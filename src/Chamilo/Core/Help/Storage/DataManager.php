<?php
namespace Chamilo\Core\Help\Storage;

use Chamilo\Core\Help\Storage\DataClass\HelpItem;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'help_';

    public static function retrieve_help_item_by_context($context, $identifier, $language)
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(HelpItem::class, HelpItem::PROPERTY_CONTEXT),
            new StaticConditionVariable($context));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(HelpItem::class, HelpItem::PROPERTY_IDENTIFIER),
            new StaticConditionVariable($identifier));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(HelpItem::class, HelpItem::PROPERTY_LANGUAGE),
            new StaticConditionVariable($language));
        
        $condition = new AndCondition($conditions);
        
        return self::retrieve(HelpItem::class, new DataClassRetrieveParameters($condition));
    }
}
