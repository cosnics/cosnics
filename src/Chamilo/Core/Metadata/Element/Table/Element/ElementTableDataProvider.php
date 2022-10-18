<?php
namespace Chamilo\Core\Metadata\Element\Table\Element;

use Chamilo\Core\Metadata\Element\Storage\DataManager;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Table data provider for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ElementTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count(Element::class, new DataClassCountParameters($condition));
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        if (is_null($orderBy))
        {
            $orderBy = new OrderBy();
        }

        $orderBy->add(
            new OrderProperty(
                new PropertyConditionVariable(Element::class, Element::PROPERTY_DISPLAY_ORDER)
            )
        );
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $orderBy);

        return DataManager::retrieves(Element::class, $parameters);
    }
}