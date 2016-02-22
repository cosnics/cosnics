<?php
namespace Chamilo\Core\Home\Repository;

use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class HomeRepository
{

    /**
     *
     * @param integer $userIdentifier
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findElementsByUserIdentifier($userIdentifier)
    {
        $parameters = new DataClassRetrievesParameters(
            $this->getElementsByUserIdentifierCondition($userIdentifier),
            null,
            null,
            array(
                new OrderBy(new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_TYPE)),
                new OrderBy(new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_SORT))));

        return DataManager :: retrieves(Element :: class_name(), $parameters);
    }

    /**
     *
     * @param integer $userIdentifier
     * @return \Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    public function getElementsByUserIdentifierCondition($userIdentifier)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier));
    }

    /**
     *
     * @param integer $userIdentifier
     * @return integer
     */
    public function countElementsByUserIdentifier($userIdentifier)
    {
        $parameters = new DataClassCountParameters($this->getElementsByUserIdentifierCondition($userIdentifier));

        return DataManager :: count(Element :: class_name(), $parameters);
    }
}