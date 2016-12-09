<?php
namespace Chamilo\Libraries\Storage\DataManager\Interfaces;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface StorageUnitDatabaseInterface
{

    public function create($name, $properties, $indexes);

    public function exists($name);

    public function drop($name);

    public function rename($old_name, $new_name);

    public function alter($type, $tableName, $property, $attributes);

    public function alterIndex($type, $tableName, $name, $columns);

    public function truncate($name);

    public function optimize($name);
}