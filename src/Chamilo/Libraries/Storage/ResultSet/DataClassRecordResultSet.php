<?php
namespace Chamilo\Libraries\Storage\ResultSet;

use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Wrapper class for the RecordResultSet that maps the records from the resultset to dataclasses (without caching)
 *
 * @package Chamilo\Libraries\Storage\ResultSet
 * @deprecated Use DataClassIterator or ArrayIterator now
 */
class DataClassRecordResultSet extends ArrayResultSet
{

    /**
     * Constructor
     *
     * @param string $class
     * @param RecordResultSet $baseResultSet
     *
     * @throws \Exception
     */
    public function __construct($class, RecordResultSet $baseResultSet)
    {
        $data_classes = array();

        foreach($baseResultSet as $record)
        {
            $data_classes[] = $this->record_to_data_class($class, $record);
        }

        parent::__construct($data_classes);
    }

    /**
     * Maps a record to a data class
     *
     * @param string $class
     * @param string[] $record
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     * @throws \Exception
     */
    protected function record_to_data_class($class, $record = array())
    {
        /**
         * @var \Chamilo\Libraries\Storage\DataClass\DataClass|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass $base
         */
        $base = is_subclass_of($class, CompositeDataClass::class) ? CompositeDataClass::class : DataClass::class;
        $class =
            is_subclass_of($class, CompositeDataClass::class) ? $record[CompositeDataClass::PROPERTY_TYPE] : $class;

        return $base::factory($class, $record);
    }
}