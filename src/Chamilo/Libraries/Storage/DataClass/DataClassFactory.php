<?php
namespace Chamilo\Libraries\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassExtensionInterface;

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
     * @template tGetDataClass
     *
     * @param class-string<tGetDataClass> $dataClassName
     * @param string[] $record
     *
     * @return tGetDataClass
     */
    public function getDataClass(string $dataClassName, array $record = [])
    {
        /**
         * @var \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
         */
        $dataClass = new $dataClassName();

        foreach ($dataClass::getDefaultPropertyNames() as $property)
        {
            if (array_key_exists($property, $record))
            {
                $dataClass->setDefaultProperty($property, $record[$property]);
                unset($record[$property]);
            }
        }

        if ($dataClass instanceof DataClassExtensionInterface)
        {
            foreach ($dataClass::getAdditionalPropertyNames() as $additionalPropertyName)
            {
                if (array_key_exists($additionalPropertyName, $record))
                {
                    $dataClass->setAdditionalProperty($additionalPropertyName, $record[$additionalPropertyName]);
                    unset($record[$additionalPropertyName]);
                }
            }
        }

        if (count($record) > 0)
        {
            foreach ($record as $optional_property_name => $optional_property_value)
            {
                $dataClass->setOptionalProperty($optional_property_name, $optional_property_value);
            }
        }

        return $dataClass;
    }
}