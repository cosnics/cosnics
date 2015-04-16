<?php
namespace Chamilo\Core\Metadata\Relation\Service;

use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Core\Metadata\Relation\Storage\DataClass\Relation;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;

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

    public function getRelationByName($relationName)
    {
        $condition = new ComparisonCondition(
            new PropertyConditionVariable(Relation :: class_name(), Relation :: PROPERTY_NAME), 
            ComparisonCondition :: EQUAL, 
            new StaticConditionVariable($relationName));
        
        return DataManager :: retrieve(Relation :: class_name(), new DataClassRetrieveParameters($condition));
    }
}
