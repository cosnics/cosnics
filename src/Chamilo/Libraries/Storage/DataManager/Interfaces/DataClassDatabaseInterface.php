<?php
namespace Chamilo\Libraries\Storage\DataManager\Interfaces;

use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface DataClassDatabaseInterface
{

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     * @param boolean $autoAssignIdentifier
     * @return boolean
     */
    public function create(DataClass $dataClass, $autoAssignIdentifier = true);

    /**
     *
     * @param string $dataClassName
     * @param string[] $record
     * @return boolean
     */
    public function createRecord($dataClassName, $record);

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @return string[]
     */
    public function retrieve($dataClassName, $parameters = null);

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet\DataClassResultSet
     */
    public function retrieves($dataClassName, DataClassRetrievesParameters $parameters);

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @return string[]
     */
    public function record($dataClassName, $parameters = null);

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet\RecordResultSet
     */
    public function records($dataClassName, RecordRetrievesParameters $parameters);

    /**
     *
     * @param string $dataClassStorageUnitName
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param string[] $propertiesToUpdate
     * @throws Exception
     * @return boolean
     */
    public function update($dataClassStorageUnitName, Condition $condition, $propertiesToUpdate);

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $properties
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @throws Exception
     * @return boolean
     */
    public function updates($dataClassName, DataClassProperties $properties, $condition);

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return boolean
     */
    public function delete($dataClassName, $condition);

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     * @return integer
     */
    public function count($dataClassName, $parameters);

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters $parameters
     * @return integer
     */
    public function countGrouped($dataClassName, $parameters);

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     * @return string[]
     */
    public function distinct($dataClassName, DataClassDistinctParameters $parameters);

    /**
     *
     * @param string $dataClassName
     * @param string $property
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return integer
     */
    public function retrieveMaximumValue($dataClassName, $property, $condition = null);

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\CompositeDataClass $compositeDataClass
     * @return string[]
     */
    public function retrieveCompositeDataClassAdditionalProperties(CompositeDataClass $compositeDataClass);

    /**
     *
     * @param mixed $function
     * @throws Exception
     * @return mixed
     */
    public function transactional($function);

    /**
     *
     * @param string $dataClassStorageUnitName
     * @return string
     */
    public function getAlias($dataClassStorageUnitName);

    /**
     *
     * @param Condition $condition
     * @return string
     */
    public function translateCondition(Condition $condition = null);

    /**
     *
     * @param string $value
     * @param string $type
     * @param boolean $quote
     * @param boolean $escapeWildcards
     * @return string
     */
    public function quote($value, $type = null, $quote = true, $escapeWildcards = false);

    /**
     *
     * @param string $columnName
     * @param string $storageUnitAlias
     * @return string
     */
    public function escapeColumnName($columnName, $storageUnitAlias = null);
}