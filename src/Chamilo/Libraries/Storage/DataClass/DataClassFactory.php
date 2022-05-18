<?php
namespace Chamilo\Libraries\Storage\DataClass;

/**
 *
 * @package Chamilo\Libraries\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillarexd@ehb.be>
 */
class DataClassFactory
{

    /**
     *
     * @param string $dataClassName
     * @param string[] $record
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass
     */
    public function getDataClass($dataClassName, $record)
    {
        $dataClassName =
            (is_subclass_of($dataClassName, CompositeDataClass::class) ? $record[CompositeDataClass::PROPERTY_TYPE] :
                $dataClassName);

        $dataClass = new $dataClassName();

        foreach ($dataClass->getDefaultPropertyNames() as $property)
        {
            if (array_key_exists($property, $record))
            {
                $dataClass->setDefaultProperty($property, $record[$property]);
                unset($record[$property]);
            }
        }

        if ($dataClass instanceof CompositeDataClass)
        {
            foreach ($dataClass->getAdditionalPropertyNames() as $property)
            {
                if (array_key_exists($property, $record))
                {
                    $dataClass->setAdditionalProperty($property, $record[$property]);
                    unset($record[$property]);
                }
            }
        }

        if (count($record) > 0 && $dataClass instanceof CompositeDataClass)
        {
            foreach ($record as $optional_property_name => $optional_property_value)
            {
                $dataClass->setOptionalProperty($optional_property_name, $optional_property_value);
            }
        }

        return $dataClass;
    }
}