<?php
namespace Chamilo\Libraries\Storage\DataClass;

use stdClass;

/**
 * @package Chamilo\Libraries\Storage\DataClass
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PropertyMapper
{
    /**
     * @param \stdClass $class
     * @param string[] $propertyNames
     *
     * @return boolean|string
     */
    public function determineClassKeyValue($class, $propertyNames)
    {
        $keyValue = $class;
        $numberOfProperties = count($propertyNames);

        foreach ($propertyNames as $count => $propertyName)
        {
            if ($keyValue instanceof stdClass)
            {
                if (isset($keyValue->$propertyName))
                {
                    if ($count < ($numberOfProperties - 1))
                    {
                        $keyValue = $keyValue->$propertyName;
                    }
                    else
                    {
                        return $keyValue->$propertyName;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }

        return false;
    }

    /**
     *
     * @param \stdClass[] $dataClasses
     * @param string $propertyName
     *
     * @return \stdClass[][]
     */
    public function groupClassByProperty($dataClasses, $propertyName)
    {
        $mappedDataClasses = [];

        foreach ($dataClasses as $dataClass)
        {
            if (isset($dataClass->$propertyName))
            {
                $propertyValue = $dataClass->$propertyName;

                if (isset($propertyValue) && $propertyValue !== '' && !is_null($propertyValue))
                {
                    if (!array_key_exists($dataClass->$propertyName, $mappedDataClasses))
                    {
                        $mappedDataClasses[$dataClass->$propertyName] = [];
                    }

                    $mappedDataClasses[$dataClass->$propertyName][] = $dataClass;
                }
            }
        }

        return $mappedDataClasses;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass[] $dataClasses
     * @param string $methodName
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[][]
     */
    public function groupDataClassByMethod($dataClasses, $methodName)
    {
        $mappedDataClasses = [];

        foreach ($dataClasses as $dataClass)
        {
            $groupValue = $dataClass->$methodName();

            if ($groupValue)
            {
                $mappedDataClasses[$groupValue][] = $dataClass;
            }
        }

        return $mappedDataClasses;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator $dataClasses
     * @param string $propertyName
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[][]
     */
    public function groupDataClassByProperty($dataClasses, $propertyName)
    {
        $mappedDataClasses = [];

        foreach ($dataClasses as $dataClass)
        {
            if (in_array($propertyName, $dataClass->get_default_property_names()))
            {
                $propertyValue = $dataClass->getDefaultProperty($propertyName);

                if (isset($propertyValue) && $propertyValue !== '' && !is_null($propertyValue))
                {
                    if (!array_key_exists($dataClass->getDefaultProperty($propertyName), $mappedDataClasses))
                    {
                        $mappedDataClasses[$dataClass->getDefaultProperty($propertyName)] = [];
                    }

                    $mappedDataClasses[$dataClass->getDefaultProperty($propertyName)][] = $dataClass;
                }
            }
        }

        return $mappedDataClasses;
    }

    /**
     *
     * @param string[][] $records
     * @param string $propertyName
     *
     * @return string[][]
     */
    public function groupRecordsByProperty($records, $propertyName)
    {
        $mappedRecords = [];

        foreach ($records as $record)
        {
            if (array_key_exists($propertyName, $record))
            {
                if ($record[$propertyName])
                {
                    if (!array_key_exists($record[$propertyName], $mappedRecords))
                    {
                        $mappedRecords[$record[$propertyName]] = [];
                    }

                    $mappedRecords[$record[$propertyName]][] = $record;
                }
            }
        }

        return $mappedRecords;
    }

    /**
     *
     * @param \stdClass[] $classes
     * @param string[] $propertyNames
     *
     * @return \stdClass[]
     */
    public function mapClassByProperties(array $classes, array $propertyNames)
    {
        $mappedClasses = [];

        foreach ($classes as $class)
        {
            $keyValue = $this->determineClassKeyValue($class, $propertyNames);

            if ($keyValue)
            {
                $mappedClasses[$keyValue] = $class;
            }
        }

        return $mappedClasses;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass[] $dataClasses
     * @param string $methodName
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[]
     */
    public function mapDataClassByMethod($dataClasses, $methodName)
    {
        $mappedDataClasses = [];

        foreach ($dataClasses as $dataClass)
        {
            $mapValue = $dataClass->$methodName();

            if ($mapValue)
            {
                $mappedDataClasses[$mapValue] = $dataClass;
            }
        }

        return $mappedDataClasses;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass[] $dataClasses
     * @param string $propertyName
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[]
     */
    public function mapDataClassByProperty($dataClasses, $propertyName)
    {
        $mappedDataClasses = [];

        foreach ($dataClasses as $dataClass)
        {
            $propertyValue = $dataClass->getDefaultProperty($propertyName);

            if (isset($propertyValue) && $propertyValue !== '' && !is_null($propertyValue))
            {
                $mappedDataClasses[$propertyValue] = $dataClass;
            }
        }

        return $mappedDataClasses;
    }

    /**
     *
     * @param string[][] $records
     * @param string $propertyName
     *
     * @return string[][]
     */
    public function mapRecordsByProperty($records, $propertyName)
    {
        $mappedRecords = [];

        foreach ($records as $record)
        {
            $propertyValue = $record[$propertyName];

            if (isset($propertyValue) && $propertyValue !== '' && !is_null($propertyValue))
            {
                $mappedRecords[$propertyValue] = $record;
            }
        }

        return $mappedRecords;
    }
}