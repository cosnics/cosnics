<?php
namespace Chamilo\Libraries\Storage\DataManager\Interfaces;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface DataClassDatabaseInterface
{

    public function create(DataClass $dataClass, $autoAssignIdentifier = true);

    public function createRecord($dataClassName, $record);

    public function retrieve($dataClassName, $parameters);

    public function record($dataClassName, $parameters);

    public function retrieves($dataClassName, DataClassRetrievesParameters $parameters);

    public function records($dataClassName, RecordRetrievesParameters $parameters);

    public function distinct($dataClassName, DataClassDistinctParameters $parameters);

    public function update($dataClassStorageUnitName, Condition $condition, $propertiesToUpdate);

    public function updates($dataClassName, $propertiesClass, $condition);

    public function delete($dataClassName, $condition);

    public function count($dataClassName, $parameters);

    public function countGrouped($dataClassName, $parameters);

    public function retrieveMaximumValue($dataClassName, $property, $condition);

    public function getAlias($dataClassStorageUnitName);

    public function retrieveCompositeDataClassAdditionalProperties(CompositeDataClass $compositeDataClass);

    public function transactional($function);

    public function translateCondition(Condition $condition = null);

    public function quote($value, $type = null, $quote = true, $escapeWildcards = false);

    public function escapeColumnName($columnName, $tableAlias = null);
}