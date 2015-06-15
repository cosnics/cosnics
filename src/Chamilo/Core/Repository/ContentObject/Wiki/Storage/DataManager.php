<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Storage;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Joins;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'repository_';

    public static function retrieve_complex_wiki_pages($type, $parameters = null)
    {
        $join = new Join(
            ContentObject :: class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(),
                    ComplexContentObjectItem :: PROPERTY_REF)));

        $joins = $parameters->get_joins();

        if (! $joins instanceof Joins)
        {
            $joins = new Joins();
            $parameters->set_joins($joins);
        }

        $joins->add($join);

        return self :: retrieves($type, $parameters);
    }

    public static function count_complex_wiki_pages($type, $parameters = null)
    {
        $join = new Join(
            ContentObject :: class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(),
                    ComplexContentObjectItem :: PROPERTY_REF)));

        $joins = $parameters->get_joins();

        if (! $joins instanceof Joins)
        {
            $joins = new Joins();
            $parameters->set_joins($joins);
        }

        $joins->add($join);

        return self :: count($type, $parameters);
    }
}
