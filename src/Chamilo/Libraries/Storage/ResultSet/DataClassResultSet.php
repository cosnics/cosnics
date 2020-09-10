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
 * @deprecated Use DataClassIterator or ArrayIterator now
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
     * @param string $className
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass[]
     */
    public function __construct($className, $dataClasses)
    {
        parent::__construct($dataClasses);

        $this->class_name = $className;
    }

    /**
     *
     * @return string
     */
    public function getCacheClassName()
    {
        $compositeDataClassName = CompositeDataClass::class;
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
     * Get the DataClass class name
     *
     * @return string
     */
    public function get_class_name()
    {
        return $this->class_name;
    }

    /**
     * Convert the record to a DataClass object
     *
     * @param string $className
     * @param string[] $record
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     * @throws \Exception
     */
    public function get_object($className, $record)
    {
        /**
         * @var \Chamilo\Libraries\Storage\DataClass\DataClass|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass $baseClassName
         */
        $baseClassName =
            (is_subclass_of($className, CompositeDataClass::class) ? CompositeDataClass::class : DataClass::class);
        $className =
            (is_subclass_of($className, CompositeDataClass::class) ? $record[CompositeDataClass::PROPERTY_TYPE] :
                $className);

        return $baseClassName::factory($className, $record);
    }
}
