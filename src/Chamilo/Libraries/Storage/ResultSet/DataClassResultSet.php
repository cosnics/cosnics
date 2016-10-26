<?php
namespace Chamilo\Libraries\Storage\ResultSet;

use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Libraries\Storage\ResultSet
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use DataClassIterator, RecordIterator or ArrayIterator now
 */
class DataClassResultSet extends ArrayResultSet
{

    /**
     * The DataClass class name
     *
     * @var string
     */
    private $class_name;

    /**
     * Constructor
     *
     * @param string $class_name
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass[]
     */
    public function __construct($class_name, $objects)
    {
        parent::__construct($objects);

        $this->class_name = $class_name;
    }

    /**
     * Get the DataClass class name
     *
     * @return string
     */
    public function get_class_name()
    {
        return $this->class_name;
    }

    public function getCacheClassName()
    {
        $compositeDataClassName = CompositeDataClass::class_name();
        $className = $this->get_class_name();

        $isCompositeDataClass = is_subclass_of($className, $compositeDataClassName);
        $isExtensionClass = get_parent_class($className) !== $compositeDataClassName;

        if ($isCompositeDataClass && $isExtensionClass)
        {
            return $className::parent_class_name();
        }
        else
        {
            return $className;
        }
    }

    /**
     * Convert the record to a DataClass object
     *
     * @param string $class_name
     * @param string[] $record
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function get_object($className, $record)
    {
        $baseClassName = (is_subclass_of($className, CompositeDataClass::class_name()) ? CompositeDataClass::class_name() : DataClass::class_name());
        $className = (is_subclass_of($className, CompositeDataClass::class_name()) ? $record[CompositeDataClass::PROPERTY_TYPE] : $className);
        return $baseClassName::factory($className, $record);
    }
}
