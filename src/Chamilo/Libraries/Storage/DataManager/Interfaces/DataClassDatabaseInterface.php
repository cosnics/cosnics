<?php
namespace Chamilo\Libraries\Storage\DataManager\Interfaces;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface DataClassDatabaseInterface
{

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     *
     * @return integer
     */
    public function count($dataClassName, DataClassCountParameters $parameters);

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters $parameters
     *
     * @return integer[]
     */
    public function countGrouped($dataClassName, DataClassCountGroupedParameters $parameters);

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     * @param boolean $autoAssignIdentifier
     *
     * @return boolean
     */
    public function create(DataClass $dataClass, $autoAssignIdentifier = true);

    /**
     *
     * @param string $dataClassName
     * @param string[] $record
     *
     * @return boolean
     */
    public function createRecord($dataClassName, $record);

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return boolean
     */
    public function delete($dataClassName, Condition $condition = null);

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     *
     * @return string[]|string[][]
     */
    public function distinct($dataClassName, DataClassDistinctParameters $parameters);

    /**
     *
     * @param string $columnName
     * @param string $storageUnitAlias
     *
     * @return string
     */
    public function escapeColumnName($columnName, $storageUnitAlias = null);

    /**
     *
     * @param string $dataClassStorageUnitName
     *
     * @return string
     */
    public function getAlias($dataClassStorageUnitName);

    /**
     *
     * @param string $value
     * @param string $type
     *
     * @return string
     */
    public function quote($value, $type = null);

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters $parameters
     *
     * @return string[]
     */
    public function record($dataClassName, RecordRetrieveParameters $parameters);

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     *
     * @return string[][]
     */
    public function records($dataClassName, RecordRetrievesParameters $parameters);

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     *
     * @return string[]
     */
    public function retrieve($dataClassName, DataClassRetrieveParameters $parameters);

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return string[][]
     */
    public function retrieves($dataClassName, DataClassRetrievesParameters $parameters);

    /**
     *
     * @param mixed $function
     *
     * @return mixed
     * @throws \Exception
     */
    public function transactional($function);

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param boolean $enableAliasing
     *
     * @return string
     */
    public function translateCondition(Condition $condition, bool $enableAliasing = true);

    /**
     *
     * @param string $dataClassStorageUnitName
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param string[] $propertiesToUpdate
     *
     * @return boolean
     * @throws \Exception
     */
    public function update($dataClassStorageUnitName, Condition $condition, $propertiesToUpdate);

    /**
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties $properties
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return boolean
     * @throws \Exception
     */
    public function updates($dataClassName, DataClassProperties $properties, Condition $condition);
}