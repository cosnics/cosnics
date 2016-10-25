<?php
namespace Chamilo\Libraries\Storage\ResultSet;

use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\ResultSet\RecordResultSet;

/**
 * Wrapper class for the RecordResultSet that maps the records from the resultset to dataclasses (without caching)
 *
 * @package Chamilo\Libraries\Storage\ResultSet
 */
class DataClassRecordResultSet extends ArrayResultSet
{

    /**
     * Constructor
     *
     * @param string $class
     * @param RecordResultSet $base_result_set
     */
    public function __construct($class, RecordResultSet $base_result_set)
    {
        $data_classes = array();

        while ($record = $base_result_set->next_result())
        {
            $data_classes[] = $this->record_to_data_class($class, $record);
        }

        parent::__construct($data_classes);
    }

    /**
     * Maps a record to a data class
     *
     * @param string $class
     * @param array $record
     *
     * @return DataClass
     */
    protected function record_to_data_class($class, $record = array())
    {
        $base = is_subclass_of($class, CompositeDataClass::class_name()) ? CompositeDataClass::class_name() : DataClass::class_name();
        $class = is_subclass_of($class, CompositeDataClass::class_name()) ? $record[CompositeDataClass::PROPERTY_TYPE] : $class;

        return $base::factory($class, $record);
    }
}