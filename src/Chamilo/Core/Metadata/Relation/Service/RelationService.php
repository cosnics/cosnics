<?php
namespace Chamilo\Core\Metadata\Relation\Service;

use Chamilo\Core\Metadata\Storage\DataClass\Relation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Metadata\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RelationService
{

    /**
     * @param string $relationName
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Relation
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function getRelationByName($relationName)
    {
        $condition = new ComparisonCondition(
            new PropertyConditionVariable(Relation::class, Relation::PROPERTY_NAME), ComparisonCondition::EQUAL,
            new StaticConditionVariable($relationName)
        );

        return DataManager::retrieve(Relation::class, new DataClassRetrieveParameters($condition));
    }
}
