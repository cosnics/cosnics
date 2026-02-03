<?php
namespace Chamilo\Libraries\Storage\Factory;

/**
 * @package Chamilo\Libraries\Storage\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillarexd@ehb.be>
 */
class DataClassFactory
{
    /**
     * @template tGetDataClass
     *
     * @param class-string<tGetDataClass> $dataClassName
     * @param string[] $record
     *
     * @return tGetDataClass
     */
    public function getDataClass(string $dataClassName, array $record = [])
    {
        $dataClass = new $dataClassName();

        foreach ($dataClass::getDefaultPropertyNames() as $property) {
            if (array_key_exists($property, $record)) {
                $dataClass->setDefaultProperty($property, $record[$property]);
                unset($record[$property]);
            }
        }

        if (count($record) > 0) {
            foreach ($record as $optionalPropertyName => $optionalPropertyValue) {
                $dataClass->setOptionalProperty($optionalPropertyName, $optionalPropertyValue);
            }
        }

        return $dataClass;
    }
}