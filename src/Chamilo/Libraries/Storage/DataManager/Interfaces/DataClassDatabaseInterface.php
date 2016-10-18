<?php
namespace Chamilo\Libraries\Storage\DataManager\Interfaces;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface DataClassDatabaseInterface
{

    public function create($object);

    public function createRecord($className, $record);

    public function retrieve($objectClass, $parameters);

    public function record($class, $parameters);

    public function retrieves($objectClass, $parameters);

    public function records($class, $parameters);

    public function distinct($class, $parameters);

    public function update($object, $condition);

    public function updates($class, $propertiesClass, $condition, $offset, $count, $orderBy);

    public function delete($className, $condition);

    public function count($objectClass, $parameters);

    public function countGrouped($class, $parameters);

    public function retrieveMaximumValue($class, $property, $condition);

    public function getAlias($storageUnitName);

    public function retrieveCompositeDataClassAdditionalProperties($object);

    public function transactional($function);

    public function translateCondition($condition);

    public function quote($value, $type = null, $quote = true, $escape_wildcards = false);

    public function escapeColumnName($columnName, $tableAlias = null);
}